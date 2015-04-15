<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */


include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");
 
/**
 * LF Main menu plugin
 *
 * @author Alex Killing <killing@leifos.de>
 * @version $Id$
 *
 */
class ilLfMainMenuPlugin extends ilUserInterfaceHookPlugin
{
	protected static $feature_entries = null;

	function getPluginName()
	{
		return "LfMainMenu";
	}

	/**
	 * Get feature menu entries
	 *
	 * @return array array of array with keys "service" (string), "feature" (string), "full_title" (string)
	 *               and an instance of lfMainMenuEntry
	 */
	function getFeatureMenuEntries()
	{
		if (self::$feature_entries != null)
		{
			return self::$feature_entries;
		}

		$this->includeClass("class.lfMainMenuEntryProvider.php");
		$fe = array();
		foreach (new DirectoryIterator($this->getDirectory()."/classes") as $fileInfo)
		{
			if (substr($fileInfo->getFilename(), 0, 11) == "class.lfMME")
			{
				$class_file = $fileInfo->getFilename();
				$class_name = substr($class_file, 6, strlen($class_file) - 10);
				$this->includeClass($class_file);
				if (is_subclass_of($class_name, "lfMainMenuEntryProvider"))
				{
					$o = new $class_name();
					foreach ($o->getFeatures() as $k => $title)
					{
						$fe[] = array(
							"service_id" => $o->getServiceId(),
							"service" => $o->getServiceTitle(),
							"feature" => $title,
							"feature_id" => $k,
							"full_id" => $o->getServiceId().":".$k,
							"full_title" => $o->getServiceTitle()." - ".$title,
							"instance" => $o
						);
					}
				}
			}
			$fe = ilUtil::sortArray($fe, "full", "asc");
		}

		self::$feature_entries = $fe;

		return $fe;
	}

	/**
	 * Get feature entry by id
	 *
	 * @param string service_id:feature_id
	 * @return array array with keys "service" (string), "feature" (string), "full_title" (string)
	 *               and an instance of lfMainMenuEntry
	 */
	function getFeatureById($a_id)
	{
		foreach ($this->getFeatureMenuEntries() as $fe)
		{
			if ($a_id == $fe["service_id"].":".$fe["feature_id"])
			{
				return $fe;
			}
		}
		return false;
	}


}

?>
