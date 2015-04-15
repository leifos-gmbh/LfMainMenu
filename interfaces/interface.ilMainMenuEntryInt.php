<?php
/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface
 *
 * @author Alex Killing <killing@leifos.de>
 */
interface ilMainMenuEntryInt
{
	/**
	 * Get title of entry as string
	 *
	 * @return bool active
	 */
	public function getTitle();

	/**
	 * Adds the answer specific form parts to a question property form gui.
	 *
	 * @return bool active
	 */
	public function isActive();

	public function getContent();

	public function getHref();

}