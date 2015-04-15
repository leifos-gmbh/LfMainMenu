<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface
 *
 * @author Alex Killing <killing@leifos.de>
 */
abstract class lfMainMenuEntryProvider
{
	protected $ctrl;

	/**
	 * Construct
	 */
	function __construct()
	{
		global $ilCtrl, $lng;

		$this->ctrl = $ilCtrl;
		$this->lng = $lng;
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	abstract public function getServiceTitle();

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	abstract public function getServiceId();

	/**
	 * Get feature title
	 *
	 * @return array of strings, key is feature id, value is feature title
	 */
	abstract public function getFeatures();

	/**
	 * Check, if the entry of feature is visible
	 *
	 * @param string feature id
	 * @return bool active
	 */
	abstract public function isVisible($a_id);

	/**
	 * Get content
	 *
	 * @param string feature id
	 * @return string href
	 */
	abstract public function getHref($a_id);

	/**
	 * Get content
	 *
	 * @param string feature id
	 * @return string html
	 */
	public function getContent($a_id)
	{
		$this->getTitle($a_id);
	}

	/**
	 * Get id
	 *
	 * @param string feature id
	 * @return string element id
	 */
	public function getDomElementId($a_id)
	{
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
		return "";
	}

}
?>