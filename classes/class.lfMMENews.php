<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * News main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMENews extends lfMainMenuEntryProvider
{
	const NEWS = "news";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("news");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "news";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::NEWS => $this->lng->txt("news"),
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
			case self::NEWS:
				return $ilSetting->get("block_activated_news");
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
			case self::NEWS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToNews");
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
			case self::NEWS:
				return "mm_pd_news";
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
			case self::NEWS:
				return ilHelp::getMainMenuTooltip("mm_pd_news");
				break;
		}
		return "";
	}

}

?>