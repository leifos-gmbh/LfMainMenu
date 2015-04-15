<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Main menu entry: personal desktop overview
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEBookmarks extends lfMainMenuEntryProvider
{
	const BOOKMARKS = "bookmarks";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("bookmarks");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "bookm";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::BOOKMARKS => $this->lng->txt("bookmarks"),
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
			case self::BOOKMARKS:
				return !$ilSetting->get("disable_bookmarks");
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
			case self::BOOKMARKS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToBookmarks");
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
			case self::BOOKMARKS:
				return "mm_pd_bookm";
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
			case self::BOOKMARKS:
				return ilHelp::getMainMenuTooltip("mm_pd_bookm");
				break;
		}
		return "";
	}

}

?>