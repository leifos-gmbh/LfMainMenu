<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

include_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * LF Menus table
 *
 * @author Alex Killing <killing@leifos.de>
 * @version $Id$
 *
 */
class lfCustomMenusTableGUI extends ilTable2GUI
{

	/**
	 * Constructor
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_plugin)
	{
		global $ilCtrl, $lng, $ilAccess, $lng;

		$this->plugin = $a_plugin;

		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setData($this->getMenus());
		$this->setTitle($this->plugin->txt("menus"));
		$this->setLimit(9999);

		$this->addColumn("", "", "1", true);
		$this->addColumn($this->plugin->txt("nr"), "nr", "1");
		$this->addColumn($lng->txt("title"));
		$this->addColumn($lng->txt("type"));
		$this->addColumn($lng->txt("active"));
		$this->addColumn($this->plugin->txt("access_check_ref_id"));
		$this->addColumn($this->plugin->txt("permission"));
		$this->addColumn($lng->txt("action"));

		$this->setDefaultOrderField("nr");
		$this->setDefaultOrderDirection("asc");

		$this->setEnableHeader(true);
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate($this->plugin->getDirectory()."/templates/tpl.menus_row.html");
		$this->setEnableTitle(true);

		$this->addCommandButton("updateNumbering", $this->plugin->txt("update_numbering"));
		
		$this->addMultiCommand("activateMenus", $lng->txt("activate"));
		$this->addMultiCommand("deactivateMenus", $lng->txt("deactivate"));
		$this->addMultiCommand("confirmMenuDeletion", $lng->txt("delete"));
	}
	
	/**
	 * Should this field be sorted numeric?
	 *
	 * @return	boolean		numeric ordering; default is false
	 */
	function numericOrdering($a_field)
	{
		if ($a_field == "nr")
		{
			return true;
		}
		return false;
	}

	/**
	 * Get menus
	 *
	 * @param
	 * @return
	 */
	function getMenus()
	{
		$this->plugin->includeClass("class.lfCustomMenu.php");
		$items = lfCustomMenu::getMenus();
		
		return $items;
	}

	/**
	 * Fill table row
	 */
	protected function fillRow($a_set)
	{
		global $lng, $ilUser, $ilCtrl;

		$ilCtrl->setParameter($this->parent_obj, "menu_id", $a_set["id"]);
		$this->tpl->setCurrentBlock("cmd");
		$this->tpl->setVariable("HREF_CMD",
			$ilCtrl->getLinkTarget($this->parent_obj, "editMenu"));
		$this->tpl->setVariable("TXT_CMD",
			$lng->txt("edit"));
		$this->tpl->parseCurrentBlock();
		
		if ($a_set["type"] == "custom")
		{
			$this->tpl->setCurrentBlock("cmd");
			$this->tpl->setVariable("HREF_CMD",
				$ilCtrl->getLinkTarget($this->parent_obj, "editTitles"));
			$this->tpl->setVariable("TXT_CMD",
				$this->plugin->txt("translations"));
			$this->tpl->parseCurrentBlock();
		
			$this->tpl->setCurrentBlock("cmd");
			$this->tpl->setVariable("HREF_CMD",
				$ilCtrl->getLinkTarget($this->parent_obj, "listItems"));
			$this->tpl->setVariable("TXT_CMD",
				$this->plugin->txt("edit_items"));
			$this->tpl->parseCurrentBlock();
		}
		
		$ilCtrl->setParameter($this->parent_obj, "menu_id", "");
		
		$this->tpl->setVariable("VAL_NR", $a_set["nr"] * 10);
		$this->tpl->setVariable("VAL_ID", $a_set["id"]);
		if ((int) $a_set["acc_ref_id"] > 0 && $a_set["type"] == "custom")
		{
			$this->tpl->setVariable("VAL_ACC_REF_ID", $a_set["acc_ref_id"]);
			$this->tpl->setVariable("VAL_ACC_PERM", $a_set["acc_perm"]);
		}
		$this->tpl->setVariable("VAL_TYPE", $this->plugin->txt("menu_t_".$a_set["type"]));
		$this->tpl->setVariable("VAL_TITLE",
			lfCustomMenu::lookupTitle("mn", $a_set["id"], $ilUser->getLanguage(), true));
		
		if ($a_set["active"])
		{
			$this->tpl->setVariable("VAL_ACTIVE", $lng->txt("active"));
		}
		else
		{
			$this->tpl->setVariable("VAL_ACTIVE", $lng->txt("inactive"));
		}

	}

}
?>
