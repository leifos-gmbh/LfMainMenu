<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * User service main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEUser extends lfMainMenuEntryProvider
{
	const PROFILE = "profile";
	const SETTINGS = "settings";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("user");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "user";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::PROFILE => $this->lng->txt("personal_profile"),
			self::SETTINGS => $this->lng->txt("personal_settings")
		);
	}

	/**
	 * Check, if the entry is visible
	 *
	 * @param string feature id
	 * @return bool active
	 */
	public function isVisible($a_id)
	{
		global $rbacsystem, $ilUser, $ilSetting;

		return true;
	}

	/**
	 * Get content
	 *
	 * @param string feature id
	 * @return string href
	 */
	public function getHref($a_id)
	{
		switch ($a_id)
		{
			case self::PROFILE:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToProfile");
				break;

			case self::SETTINGS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToSettings");
				break;
		}
		return "";
	}

	/**
	 * Get id
	 *
	 * @param string feature id
	 * @return string element id
	 */
	public function getDomElementId($a_id)
	{
		switch ($a_id)
		{
			case self::PROFILE:
				return "mm_pd_profile";
				break;

			case self::SETTINGS:
				return "mm_pd_sett";
				break;
		}
		return "";
	}

	/**
	 * Get tooltip
	 *
	 * @param string feature id
	 * @return string tooltip content
	 */
	public function getTooltip($a_id)
	{
		switch ($a_id)
		{
			case self::PROFILE:
				return ilHelp::getMainMenuTooltip("mm_pd_profile");
				break;

			case self::SETTINGS:
				return ilHelp::getMainMenuTooltip("mm_pd_sett");
				break;
		}
		return "";
	}

}

?>