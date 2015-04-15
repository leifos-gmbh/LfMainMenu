<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Tracking/Learning progress main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMETracking extends lfMainMenuEntryProvider
{
	const LEARNING_PROGRESS = "learning_progress";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("obj_trac");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "trac";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::LEARNING_PROGRESS => $this->lng->txt("learning_progress")
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

		include_once("Services/Tracking/classes/class.ilObjUserTracking.php");

		// my groups and courses, if both is available
		switch ($a_id)
		{
			case self::LEARNING_PROGRESS:
				return ilObjUserTracking::_enabledLearningProgress() &&
					(ilObjUserTracking::_hasLearningProgressOtherUsers() ||
					ilObjUserTracking::_hasLearningProgressLearner());
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
			case self::LEARNING_PROGRESS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToLP");
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
			case self::LEARNING_PROGRESS:
				return "mm_pd_lp";
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
			case self::LEARNING_PROGRESS:
				return ilHelp::getMainMenuTooltip("mm_pd_lp");
				break;
		}
		return "";
	}

}

?>