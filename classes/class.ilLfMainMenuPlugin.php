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
	function getPluginName()
	{
		return "LfMainMenu";
	}
}

?>
