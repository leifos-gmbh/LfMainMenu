<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Main menu entry: cognos BLU entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMECognosBLU extends lfMainMenuEntryProvider
{
	const COURSES = "courses";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return "Cognos";
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "cognos";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::COURSES => $this->lng->txt('my_courses_overview'),
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
			case self::COURSES:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jump2CourseOverview");
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
			case self::COURSES:
				return "mm_pd_crs_ovv";
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
			case self::COURSES:
				return ilHelp::getMainMenuTooltip('mm_pd_crs_ovv');
				break;
		}
		return "";
	}

}

?>