<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

include_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * LF Menu items table
 *
 * @author Alex Killing <killing@leifos.de>
 * @version $Id$
 *
 */
class lfMenuItemsTableGUI extends ilTable2GUI
{

	/**
	 * Constructor
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_plugin, $a_menu_id)
	{
		global $ilCtrl, $lng, $ilAccess, $lng;

		$this->plugin = $a_plugin;
		$this->menu_id = $a_menu_id;

		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setData($this->getItems($this->menu_id));
		$this->setTitle($this->plugin->txt("menu_items").
			": ".lfCustomMenu::lookupTitle("mn",$this->menu_id));
		$this->setLimit(9999);

		$this->addColumn("", "", "1", true);
		$this->addColumn($this->plugin->txt("nr"), "nr", "1");
		$this->addColumn($this->lng->txt("title"));
		$this->addColumn($this->plugin->txt("ref_id"));
		$this->addColumn($this->lng->txt("target"));
		$this->addColumn($this->plugin->txt("access_check_ref_id"));
		$this->addColumn($this->plugin->txt("permission"));
		$this->addColumn($lng->txt("action"));

		$this->setDefaultOrderField("nr");
		$this->setDefaultOrderDirection("asc");

		$this->setEnableHeader(true);
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate($this->plugin->getDirectory()."/templates/tpl.menu_items_row.html");
		$this->setEnableTitle(true);

		$this->addCommandButton("updateItemNumbering", $this->plugin->txt("update_numbering"));
		$this->addMultiCommand("confirmItemDeletion", $lng->txt("delete"));
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
	 * Get courses
	 *
	 * @param
	 * @return
	 */
	function getItems($a_menu_id)
	{
		$this->plugin->includeClass("class.lfCustomMenu.php");
		$items = lfCustomMenu::getMenuItems($a_menu_id);
		
		return $items;
	}

	/**
	 * Fill table row
	 */
	protected function fillRow($a_set)
	{
		global $lng, $ilCtrl;

		$ilCtrl->setParameter($this->parent_obj, "item_id", $a_set["id"]);
		$this->tpl->setCurrentBlock("cmd");
		$this->tpl->setVariable("HREF_CMD",
			$ilCtrl->getLinkTarget($this->parent_obj, "editMenuItem"));
		$this->tpl->setVariable("TXT_CMD",
			$lng->txt("edit"));
		$this->tpl->parseCurrentBlock();
		
		if ($a_set["it_type"] == lfCustomMenu::ITEM_TYPE_REF_ID
			|| $a_set["it_type"] == lfCustomMenu::ITEM_TYPE_URL)
		{
			$this->tpl->setCurrentBlock("cmd");
			$this->tpl->setVariable("HREF_CMD",
				$ilCtrl->getLinkTarget($this->parent_obj, "editItemTitles"));
			$this->tpl->setVariable("TXT_CMD",
				$this->plugin->txt("translations"));
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setVariable("VAL_NR", $a_set["nr"] * 10);
		$this->tpl->setVariable("VAL_ID", $a_set["id"]);
		$this->tpl->setVariable("VAL_TITLE",
			lfCustomMenu::getItemPresentationTitle($a_set["id"], $a_set["it_type"],
				$a_set["ref_id"], $lng->getDefaultLanguage(), $a_set["feature_id"]));
		if ($a_set["it_type"] == lfCustomMenu::ITEM_TYPE_REF_ID)
		{
			$this->tpl->setVariable("REF_ID", $a_set["ref_id"]);
		}
		else
		{
			$this->tpl->setVariable("VAL_TARGET", $a_set["target"]);
			if ((int) $a_set["acc_ref_id"] > 0)
			{
				$this->tpl->setVariable("VAL_ACC_PERM", $a_set["acc_perm"]);
				$this->tpl->setVariable("VAL_ACC_REF_ID", $a_set["acc_ref_id"]);
			}
		}

	}

}
?>
