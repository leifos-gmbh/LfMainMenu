<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
 
/**
 * LF Main Menu Plugin Configuration Screen
 *
 * @author Alex Killing <killing@leifos.de>
 * @version $Id$
 *
 */
class ilLfMainMenuConfigGUI extends ilPluginConfigGUI
{
	/**
	* Handles all commmands, default is "configure"
	*/
	function performCommand($cmd)
	{
		global $ilCtrl;
		
		$ilCtrl->saveParameter($this, "menu_id");
		
		switch ($cmd)
		{
			default:
				$this->$cmd();
				break;

		}
	}

	/**
	 * Configure
	 *
	 * @param
	 * @return
	 */
	function configure()
	{
		global $tpl, $ilToolbar, $ilCtrl;

		$ilToolbar->addButton($this->getPluginObject()->txt("add_menu"),
			$ilCtrl->getLinkTarget($this, "addMenu"));

		$this->getPluginObject()->includeClass("class.lfCustomMenusTableGUI.php");
		$table = new lfCustomMenusTableGUI($this, "configure", $this->getPluginObject());
		$tpl->setContent($table->getHTML());
	}

	////
	//// Menu related
	////
	
	/**
	 * Add menu form
	 */
	function addMenu()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl;

		$ilTabs->setBackTarget($this->getPluginObject()->txt("menu_items"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$this->initMenuForm("create");
		$tpl->setContent($this->form->getHTML());
	}
	
	/**
	 * Edit menu form
	 */
	function editMenu()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl;

		$ilTabs->setBackTarget($this->getPluginObject()->txt("menu_items"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$this->initMenuForm("edit");
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Init  form.
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initMenuForm($a_mode = "edit")
	{
		global $lng, $ilCtrl, $ilUser;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// menu type
		$type = new ilRadioGroupInputGUI($this->getPluginObject()->txt("menu_type"), "type");
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		foreach (lfCustomMenu::getMenuTypes() as $t => $l)
		{
			$type_opt[$t] = new ilRadioOption($l, $t,
				$this->getPluginObject()->txt("menu_type_info_".$t));
			$type->addOption($type_opt[$t]);
		}
		$type->setValue("custom");
		$this->form->addItem($type);
		
		// title
		$ti = new ilTextInputGUI($this->getPluginObject()->txt("menu_title"), "title");
		$ti->setMaxLength(200);
		$lng->loadLanguageModule("meta");
		$ti->setInfo($lng->txt("meta_l_".$lng->getDefaultLanguage()).
			" (".$this->getPluginObject()->txt("default_language").")");
		$type_opt["custom"]->addSubItem($ti);
		
		// public mode
		$options = lfCustomMenu::getPublicModes();
		$pmode = new ilSelectInputGUI($this->getPluginObject()->txt("visibility"), "pmode");
		$pmode->setOptions($options);
		$type_opt["custom"]->addSubItem($pmode);
		
		// access check ref id
		$acc_ref_id = new ilNumberInputGUI($this->getPluginObject()->txt("access_check_ref_id"), "acc_ref_id");
		$acc_ref_id->setInfo($this->getPluginObject()->txt("access_check_ref_id_info"));
		$acc_ref_id->setMaxLength(8);
		$acc_ref_id->setSize(8);
		$type_opt["custom"]->addSubItem($acc_ref_id);
		
		// access check permission
		$options = array("visible" => "visible",
			"read" => "read",
			"write" => "write");
		$acc_perm = new ilSelectInputGUI($this->getPluginObject()->txt("permission"), "acc_perm");
		$acc_perm->setInfo($this->getPluginObject()->txt("access_check_permission_info"));
		$acc_perm->setOptions($options);
		$acc_perm->setValue("read");
		$type_opt["custom"]->addSubItem($acc_perm);
		
		// append lv 
		$alv = new ilCheckboxInputGUI($this->getPluginObject()->txt("append_lv"),
			"append_lv");
		$type_opt["pd"]->addSubItem($alv);
		
		if ($a_mode == "edit")
		{
			$menu = lfCustomMenu::getMenu($_GET["menu_id"]);
			$type->setValue($menu["type"]);
			if ((int) $menu["acc_ref_id"] > 0)
			{
				$acc_ref_id->setValue($menu["acc_ref_id"]);
			}
			else
			{
				$acc_ref_id->setValue("");
			}
			$acc_perm->setValue($menu["acc_perm"]);
			$pmode->setValue($menu["pmode"]);
			$alv->setChecked($menu["append_last_visited"]);
			$ti->setValue(lfCustomMenu::lookupTitle("mn", $_GET["menu_id"],
				$lng->getDefaultLanguage()));
		}
		
		// save and cancel commands
		if ($a_mode == "create")
		{
			$this->form->addCommandButton("saveMenu", $lng->txt("save"));
			$this->form->addCommandButton("configure", $lng->txt("cancel"));
			$this->form->setTitle($this->getPluginObject()->txt("new_menu"));
		}
		else
		{
			$this->form->addCommandButton("updateMenu", $lng->txt("save"));
			$this->form->addCommandButton("configure", $lng->txt("cancel"));
			$this->form->setTitle($this->getPluginObject()->txt("edit_menu"));
		}

		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}

	/**
	 * Save menu
	 */
	public function saveMenu()
	{
		global $tpl, $lng, $ilCtrl;
	
		$this->initMenuForm("create");
		if ($this->form->checkInput())
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			lfCustomMenu::addMenu($_POST["title"],
				$_POST["type"], $_POST["acc_ref_id"], $_POST["acc_perm"],
				$_POST["pmode"], $_POST["append_lv"]);
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}
	
	/**
	 * Update menu
	 */
	public function updateMenu()
	{
		global $tpl, $lng, $ilCtrl;

		$this->initMenuForm("edit");
		if ($this->form->checkInput())
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			lfCustomMenu::updateMenu((int) $_GET["menu_id"],
				$_POST["type"], $_POST["acc_ref_id"], $_POST["acc_perm"],
				$_POST["pmode"], $_POST["append_lv"]);
			
			lfCustomMenu::saveTitle("mn", (int) $_GET["menu_id"],
				$lng->getDefaultLanguage(), $_POST["title"]);

			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}
	
	/**
	 * Confirm item deletion
	 */
	function confirmItemDeletion()
	{
		global $ilCtrl, $tpl, $lng;

		if (!is_array($_POST["id"]) || count($_POST["id"]) == 0)
		{
			ilUtil::sendInfo($lng->txt("no_checkbox"), true);
			$ilCtrl->redirect($this, "listItems");
		}
		else
		{
			include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
			$cgui = new ilConfirmationGUI();
			$cgui->setFormAction($ilCtrl->getFormAction($this));
			$cgui->setHeaderText($this->getPluginObject()->txt("really_delete"));
			$cgui->setCancel($lng->txt("cancel"), "listItems");
			$cgui->setConfirm($lng->txt("delete"), "deleteItems");

			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			foreach ($_POST["id"] as $i)
			{
				$cgui->addItem("id[]", $i,
					lfCustomMenu::lookupTitle("it", (int) $i));
			}

			$tpl->setContent($cgui->getHTML());
		}
	}

	/**
	 * Delete items
	 *
	 * @param
	 * @return
	 */
	function deleteItems()
	{
		global $lng, $ilCtrl;

		if (!is_array($_POST["id"]) || count($_POST["id"]) == 0)
		{
		}
		else
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			foreach ($_POST["id"] as $i)
			{
				lfCustomMenu::deleteItem((int) $i);
			}
			lfCustomMenu::fixItemNumbering($_GET["menu_id"]);
		}

		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "listItems");
	}

	/**
	 * Activate Menus
	 *
	 * @param
	 * @return
	 */
	function activateMenus()
	{
		global $lng, $ilCtrl;
		
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		foreach ($_POST["id"] as $id)
		{
			lfCustomMenu::activateMenu($id, 1);
		}
		
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "configure");
	}
	
	/**
	 * Deactivate Menus
	 *
	 * @param
	 * @return
	 */
	function deactivateMenus()
	{
		global $lng, $ilCtrl;
		
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		
		foreach ($_POST["id"] as $id)
		{
			lfCustomMenu::activateMenu($id, 0);
		}
		
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "configure");
	}
	
	
	////
	//// Menu item related
	////
	
	/**
	 * Configure
	 *
	 * @param
	 * @return
	 */
	function listItems()
	{
		global $tpl, $ilToolbar, $ilCtrl, $ilTabs, $lng;

		$ilToolbar->addButton($this->getPluginObject()->txt("add_menu_item"),
			$ilCtrl->getLinkTarget($this, "addMenuItem"));
		
		$ilTabs->setBackTarget($this->getPluginObject()->txt("all_menus"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$this->getPluginObject()->includeClass("class.lfMenuItemsTableGUI.php");
		$table = new lfMenuItemsTableGUI($this, "listItems", $this->getPluginObject(),
			$_GET["menu_id"]);
		$tpl->setContent($table->getHTML());
	}

	/**
	 * Add menu item
	 */
	function addMenuItem()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl;
		
		$ilCtrl->saveParameter($this, "item_id");

		$ilTabs->setBackTarget($this->getPluginObject()->txt("all_menus"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$this->initMenuItemForm("create");
		$tpl->setContent($this->form->getHTML());
	}
	
	/**
	 * Edit menu item
	 */
	function editMenuItem()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl;
		
		$ilCtrl->saveParameter($this, "item_id");

		$ilTabs->setBackTarget($this->getPluginObject()->txt("all_menus"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$this->initMenuItemForm("edit");
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Update menu item
	 */
	public function updateMenuItem()
	{
		global $tpl, $lng, $ilCtrl;

		$this->initMenuItemForm("edit");
		if ($this->form->checkInput())
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			lfCustomMenu::updateMenuItem($_GET["item_id"],
				$_POST["target"], $_POST["acc_ref_id"], $_POST["acc_perm"],
				$_POST["pmode"], $_POST["type"], $_POST["ref_id"], $_POST["newwin"]);
			
			lfCustomMenu::saveTitle("it", (int) $_GET["item_id"],
				$lng->getDefaultLanguage(), $_POST["title"]);

			
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "listItems");
		}
		else
		{
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}
	

	/**
	 * Init menu item form
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initMenuItemForm($a_mode = "edit")
	{
		global $lng, $ilCtrl, $ilUser;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// title
		$ti = new ilTextInputGUI($lng->txt("title"), "title");
		$ti->setMaxLength(200);
		$lng->loadLanguageModule("meta");
		$ti->setInfo($lng->txt("meta_l_".$lng->getDefaultLanguage()).
			" (".$this->getPluginObject()->txt("default_language").")");
		$this->form->addItem($ti);
		
		// public mode
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		$options = lfCustomMenu::getPublicModes();
		$pmode = new ilSelectInputGUI($this->getPluginObject()->txt("visibility"), "pmode");
		$pmode->setOptions($options);
		$this->form->addItem($pmode);
		
		// type
		$type = new ilRadioGroupInputGUI($this->getPluginObject()->txt("item_type"), "type");
		$type->setValue(lfCustomMenu::ITEM_TYPE_URL);
		$turl = new ilRadioOption($lng->txt("url"), lfCustomMenu::ITEM_TYPE_URL,
			"");
		$type->addOption($turl);
		$tref = new ilRadioOption($this->getPluginObject()->txt("ref_id"), lfCustomMenu::ITEM_TYPE_REF_ID,
			"");
		$type->addOption($tref);
		$lv = new ilRadioOption($lng->txt("last_visited"), lfCustomMenu::ITEM_TYPE_LAST_VISITED,
			"");
		$type->addOption($lv);
		$sep = new ilRadioOption($this->getPluginObject()->txt("separator"), lfCustomMenu::ITEM_TYPE_SEPARATOR,
			"");
		$type->addOption($sep);
		
		$this->form->addItem($type);

		// target
		$target = new ilTextInputGUI($this->getPluginObject()->txt("target"), "target");
		$target->setMaxLength(200);
		$turl->addSubItem($target);

		// new window?
		$nw = new ilCheckboxInputGUI($this->getPluginObject()->txt("newwin"), "newwin");
		$turl->addSubItem($nw);

		// access check ref id
		$acc_ref_id = new ilNumberInputGUI($this->getPluginObject()->txt("access_check_ref_id"), "acc_ref_id");
		$acc_ref_id->setInfo($this->getPluginObject()->txt("access_check_ref_id_info"));
		$acc_ref_id->setMaxLength(8);
		$acc_ref_id->setSize(8);
		$turl->addSubItem($acc_ref_id);
		
		// access check permission
		$options = array("visible" => "visible",
			"read" => "read",
			"write" => "write");
		$acc_perm = new ilSelectInputGUI($this->getPluginObject()->txt("permission"), "acc_perm");
		$acc_perm->setInfo($this->getPluginObject()->txt("access_check_permission_info"));
		$acc_perm->setOptions($options);
		$acc_perm->setValue("read");
		$turl->addSubItem($acc_perm);
		
		// access check ref id
		$ref_id = new ilNumberInputGUI($this->getPluginObject()->txt("ref_id"), "ref_id");
		//$ref_id->setInfo($this->getPluginObject()->txt("access_check_ref_id_info"));
		$ref_id->setMaxLength(8);
		$ref_id->setSize(8);
		$tref->addSubItem($ref_id);
		
		if ($a_mode == "edit")
		{
			$item = lfCustomMenu::getMenuItem($_GET["item_id"]);
			$target->setValue($item["target"]);
			$acc_perm->setValue($item["acc_perm"]);
			$pmode->setValue($item["pmode"]);
			$type->setValue($item["it_type"]);
			$ref_id->setValue($item["ref_id"]);
			$nw->setChecked($item["newwin"]);
			$ti->setValue(lfCustomMenu::lookupTitle("it", $_GET["item_id"],
				$lng->getDefaultLanguage()));
			if ((int) $item["acc_ref_id"] > 0)
			{
				$acc_ref_id->setValue($item["acc_ref_id"]);
			}
			else
			{
				$acc_ref_id->setValue("");
			}

		}
		
		// save and cancel commands
		if ($a_mode == "create")
		{
			$this->form->addCommandButton("saveMenuItem", $lng->txt("save"));
			$this->form->addCommandButton("listItems", $lng->txt("cancel"));
			$this->form->setTitle($this->getPluginObject()->txt("new_menu_item"));
		}
		else
		{
			$this->form->addCommandButton("updateMenuItem", $lng->txt("save"));
			$this->form->addCommandButton("listItems", $lng->txt("cancel"));
			$this->form->setTitle($this->getPluginObject()->txt("edit_menu_item"));
		}

		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}

	/**
	 * Save item
	 *
	 * @param
	 * @return
	 */
	function saveMenuItem()
	{
		global $ilCtrl, $lng;

		$this->initMenuItemForm("create");
		if ($this->form->checkInput())
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			lfCustomMenu::addMenuItem((int) $_GET["menu_id"], $_POST["title"],
				$_POST["target"],
				$_POST["acc_ref_id"], $_POST["acc_perm"], $_POST["pmode"],
				$_POST["type"], $_POST["ref_id"], $_POST["newwin"]);
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "listItems");
		}
		else
		{
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}

	/**
	 * Confirm entry deletion
	 */
	function confirmMenuDeletion()
	{
		global $ilCtrl, $tpl, $lng;

		if (!is_array($_POST["id"]) || count($_POST["id"]) == 0)
		{
			ilUtil::sendInfo($lng->txt("no_checkbox"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
			$cgui = new ilConfirmationGUI();
			$cgui->setFormAction($ilCtrl->getFormAction($this));
			$cgui->setHeaderText($this->getPluginObject()->txt("really_delete"));
			$cgui->setCancel($lng->txt("cancel"), "configure");
			$cgui->setConfirm($lng->txt("delete"), "deleteMenus");

			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			foreach ($_POST["id"] as $i)
			{
				$cgui->addItem("id[]", $i,
					lfCustomMenu::lookupTitle("mn", (int) $i));
			}

			$tpl->setContent($cgui->getHTML());
		}
	}

	/**
	 * Delete menus
	 *
	 * @param
	 * @return
	 */
	function deleteMenus()
	{
		global $lng, $ilCtrl;

		if (!is_array($_POST["id"]) || count($_POST["id"]) == 0)
		{
		}
		else
		{
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			foreach ($_POST["id"] as $i)
			{
				lfCustomMenu::deleteMenu((int) $i);
			}
			lfCustomMenu::fixNumbering();
		}

		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "configure");
	}

	/**
	 * Update numbering
	 *
	 * @param
	 * @return
	 */
	function updateNumbering()
	{
		global $ilCtrl;

		if (is_array($_POST["nr"]))
		{
			$nr = ilUtil::stripSlashesArray($_POST["nr"]);
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			asort($nr);
			lfCustomMenu::setNumbering($nr);
		}
		$ilCtrl->redirect($this, "configure");
	}
	
	/**
	 * Update item numbering
	 *
	 * @param
	 * @return
	 */
	function updateItemNumbering()
	{
		global $ilCtrl;

		if (is_array($_POST["nr"]))
		{
			$nr = ilUtil::stripSlashesArray($_POST["nr"]);
			$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
			asort($nr);
			lfCustomMenu::setItemNumbering($nr);
		}
		$ilCtrl->redirect($this, "listItems");
	}
	
	////
	//// translation
	////
	
	/**
	 * Menu translation
	 */
	function editTitles()
	{
		global $tpl, $ilTabs, $ilCtrl;
		
		$ilTabs->setBackTarget($this->getPluginObject()->txt("all_menus"),
			$ilCtrl->getLinkTarget($this, "configure"));

		
		$this->getPluginObject()->includeClass("class.lfMenuTranslationTableGUI.php");
		
		$table = new lfMenuTranslationTableGUI($this, "editTitles", $this->getPluginObject(),
			"mn", (int) $_GET["menu_id"]);
		
		$tpl->setContent($table->getHTML());
	}

	/**
	 * Save translations
	 *
	 * @param
	 * @return
	 */
	function saveTranslation()
	{
		global $lng, $ilCtrl;
		
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		foreach ($lng->getInstalledLanguages() as $l)
		{
			lfCustomMenu::saveTitle("mn", $_GET["menu_id"], $l,
				ilUtil::stripSlashes($_POST["trans"][$l]));
		}
		
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "configure");
	}
	
	/**
	 * Item translation
	 */
	function editItemTitles()
	{
		global $tpl, $ilTabs, $ilCtrl;
		
		$ilCtrl->saveParameter($this, "item_id");
		
		$ilTabs->setBackTarget($this->getPluginObject()->txt("menu_items"),
			$ilCtrl->getLinkTarget($this, "listItems"));

		$this->getPluginObject()->includeClass("class.lfMenuTranslationTableGUI.php");
		
		$table = new lfMenuTranslationTableGUI($this, "editItemTitles", $this->getPluginObject(),
			"it", (int) $_GET["item_id"]);
		
		
		
		$tpl->setContent($table->getHTML());
	}

	/**
	 * Save item translations
	 */
	function saveItemTranslation()
	{
		global $lng, $ilCtrl;
		
		//$ilCtrl->saveParameter($this, "item_id");
		
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		foreach ($lng->getInstalledLanguages() as $l)
		{
			lfCustomMenu::saveTitle("it", $_GET["item_id"], $l,
				ilUtil::stripSlashes($_POST["trans"][$l]));
		}
		
		if (is_array($_POST["target"]))
		{
			lfCustomMenu::setLDTargets((int) $_GET["item_id"], $_POST["target"]);
		}

		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->setParameter($this, "item_id", $_GET["item_id"]);
		$ilCtrl->redirect($this, "editItemTitles");
	}

}
?>
