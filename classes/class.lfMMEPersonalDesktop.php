<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Main menu entry: personal desktop overview
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEPersonalDesktop extends lfMainMenuEntryProvider
{
	const OVERVIEW = "overview";
	const MY_COURSES_GROUPS = "my_courses_groups";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("personal_desktop");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "pd";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::OVERVIEW => $this->lng->txt("overview"),
			self::MY_COURSES_GROUPS => $this->lng->txt("my_courses_groups"),
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
			case self::MY_COURSES_GROUPS:
				return ($ilSetting->get('disable_my_offers') == 0 &&
					$ilSetting->get('disable_my_memberships') == 0);
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
			case self::OVERVIEW:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToSelectedItems");
				break;

			case self::MY_COURSES_GROUPS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToMemberships");
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
			case self::OVERVIEW:
				return "mm_pd_sel_items";
				break;

			case self::MY_COURSES_GROUPS:
				return "mm_pd_crs_grp";
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
			case self::OVERVIEW:
				return ilHelp::getMainMenuTooltip("mm_pd_sel_items");
				break;

			case self::MY_COURSES_GROUPS:
				return ilHelp::getMainMenuTooltip("mm_pd_crs_grp");
				break;
		}
		return "";
	}

}

?>