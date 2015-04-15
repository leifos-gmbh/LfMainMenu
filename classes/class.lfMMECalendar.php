<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Tracking/Learning progress main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMECalendar extends lfMainMenuEntryProvider
{
	const CALENDAR = "calendar";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("calendar");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "cal";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::CALENDAR => $this->lng->txt("calendar")
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

		$settings = ilCalendarSettings::_getInstance();

		// my groups and courses, if both is available
		switch ($a_id)
		{
			case self::CALENDAR:
				return $settings->isEnabled();
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
			case self::CALENDAR:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToCalendar");
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
			case self::CALENDAR:
				return "mm_pd_cal";
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
			case self::CALENDAR:
				return ilHelp::getMainMenuTooltip("mm_pd_cal");
				break;
		}
		return "";
	}

}

?>