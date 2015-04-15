<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * News main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEPersonalWorkspace extends lfMainMenuEntryProvider
{
	const PERSONAL_WORKSPACE = "personal_workspace";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("personal_workspace");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "pwsp";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::PERSONAL_WORKSPACE => $this->lng->txt("personal_workspace"),
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
		global $ilSetting;

		// my groups and courses, if both is available
		switch ($a_id)
		{
			case self::PERSONAL_WORKSPACE:
				return !$ilSetting->get("disable_personal_workspace");
				break;
		}
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
			case self::PERSONAL_WORKSPACE:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToWorkspace");
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
			case self::PERSONAL_WORKSPACE:
				return "mm_pd_wsp";
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
			case self::PERSONAL_WORKSPACE:
				return ilHelp::getMainMenuTooltip("mm_pd_wsp");
				break;
		}
		return "";
	}

}

?>