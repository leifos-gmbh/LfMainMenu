<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Portfolio main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEPortfolio extends lfMainMenuEntryProvider
{
	const PORTFOLIO = "portfolio";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("portfolio");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "prtf";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::PORTFOLIO => $this->lng->txt("portfolio"),
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
			case self::PORTFOLIO:
				return $ilSetting->get("user_portfolios");
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
			case self::PORTFOLIO:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToPortfolio");
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
			case self::PORTFOLIO:
				return "mm_pd_port";
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
			case self::PORTFOLIO:
				return ilHelp::getMainMenuTooltip("mm_pd_port");
				break;
		}
		return "";
	}

}

?>