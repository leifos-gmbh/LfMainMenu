<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Competence management main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMESkill extends lfMainMenuEntryProvider
{
	const MY_COMPETENCES = "my_competences";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("obj_skmg");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "skll";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::MY_COMPETENCES => $this->lng->txt("skills")
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

		$skmg_set = new ilSetting("skmg");

		// my groups and courses, if both is available
		switch ($a_id)
		{
			case self::MY_COMPETENCES:
				return $skmg_set->get("enable_skmg");
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
			case self::MY_COMPETENCES:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToSkills");
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
			case self::MY_COMPETENCES:
				return "mm_pd_skill";
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
			case self::MY_COMPETENCES:
				return ilHelp::getMainMenuTooltip("mm_pd_skill");
				break;
		}
		return "";
	}

}

?>