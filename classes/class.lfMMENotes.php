<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Notes main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMENotes extends lfMainMenuEntryProvider
{
	const NOTES_AND_COMMENTS = "notes_and_comments";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("notes");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "notes";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::NOTES_AND_COMMENTS => $this->lng->txt("notes_and_comments"),
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
			case self::NOTES_AND_COMMENTS:
				return !$ilSetting->get("disable_notes");
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
			case self::NOTES_AND_COMMENTS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToNotes");
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
			case self::NOTES_AND_COMMENTS:
				return "mm_pd_notes";
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
			case self::NOTES_AND_COMMENTS:
				return ilHelp::getMainMenuTooltip("mm_pd_notes");
				break;
		}
		return "";
	}

}

?>