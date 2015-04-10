<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

include_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * Menu translations
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 * @ingroup Services
 */
class lfMenuTranslationTableGUI extends ilTable2GUI
{
	/**
	 * Constructor
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_plugin,
		$a_type, $a_id)
	{
		global $ilCtrl, $lng, $ilAccess, $lng;
		
		$this->plugin = $a_plugin;
		$this->type = $a_type;
		$this->id = $a_id;
		
		$this->plugin->includeClass("class.lfCustomMenu.php");
		
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setData($lng->getInstalledLanguages());
		$this->setTitle($this->plugin->txt("translations"));
		$this->setLimit(9999);
		
		$this->addColumn($this->lng->txt("language"));
		$this->addColumn($this->lng->txt("title"));
		
		$this->menu_item = null;
		if ($a_type == "it")
		{
			$this->menu_item = lfCustomMenu::getMenuItem($a_id);
			if ($this->menu_item["it_type"] == lfCustomMenu::ITEM_TYPE_URL)
			{
				$this->addColumn($this->lng->txt("url"));
				$this->ldtarget = lfCustomMenu::getLDTargets($a_id);
			}
		}
		
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate($this->plugin->getDirectory()."/templates/tpl.translation_row.html");
		
		$lng->loadLanguageModule("meta");

		if ($this->type == "mn")
		{
			$this->addCommandButton("saveTranslation", $lng->txt("save"));
		}
		else
		{
			$this->addCommandButton("saveItemTranslation", $lng->txt("save"));
		}
	}
	
	/**
	 * Fill table row
	 */
	protected function fillRow($l)
	{
		global $lng;

		if ($this->menu_item["it_type"] == lfCustomMenu::ITEM_TYPE_URL)
		{
			$this->tpl->setCurrentBlock("target");
			$this->tpl->setVariable("TLANG", $l);
			$this->tpl->setVariable("VAL_TARGET",
				ilUtil::prepareFormOutput($this->ldtarget[$l]));
			$this->tpl->parseCurrentBlock();
		}
		
		$def = ($l == $lng->getDefaultLanguage())
			? " <br />(".$this->plugin->txt("default_language").")"
			: "";
		$this->tpl->setVariable("LANGUAGE",
			$lng->txt("meta_l_".$l).$def);
		$this->tpl->setVariable("LANG", $l);

		$this->tpl->setVariable("VAL",
			ilUtil::prepareFormOutput(lfCustomMenu::lookupTitle($this->type, $this->id, $l)));
	}

}
?>
