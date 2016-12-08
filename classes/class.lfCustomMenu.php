<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

/**
 * LF custom menu application class
 *
 * @author Alex Killin <killing@leifos.de>
 * @version $Id$
 */
class lfCustomMenu
{
	static $pl;

	const T_PD = 8;
	const T_REP = 9;
	const T_CUSTOM = 7;
	const T_SUBMENU = 5;
	
	const PMODE_BOTH = 0;
	const PMODE_NONPUBLIC_ONLY = 1;
	const PMODE_PUBLIC_ONLY = 2;
	
	const ITEM_TYPE_URL = 0;
	const ITEM_TYPE_REF_ID = 1;
	const ITEM_TYPE_LAST_VISITED = 2;
	const ITEM_TYPE_SEPARATOR = 3;
	const ITEM_TYPE_FEATURE = 4;
	const ITEM_TYPE_SUBMENU = 5;
	const ITEM_TYPE_ADMIN = 6;
	const ITEM_TYPE_CUSTOM_MENU = 7;
	const ITEM_TYPE_PD_MENU = 8;
	const ITEM_TYPE_REP_MENU = 9;
	
	/**
	 * Get menu types
	 *
	 * @return array array of menu types
	 */
	static function getMenuTypes()
	{
		global $ilPluginAdmin;
		
		$pl = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE, "UIComponent",
			"uihk", "LfMainMenu");
		
		return array(
			self::T_CUSTOM => $pl->txt("menu_t_7"),
			self::T_PD => $pl->txt("menu_t_8"),
			self::T_REP => $pl->txt("menu_t_9")
			);
	}
	
	/**
	 * Get public modes
	 *
	 * @return array array of public modes
	 */
	static function getPublicModes()
	{
		global $ilPluginAdmin;
		
		$pl = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE, "UIComponent",
			"uihk", "LfMainMenu");
		
		return array(
			self::PMODE_BOTH => $pl->txt("menu_p_mode_0"),
			self::PMODE_NONPUBLIC_ONLY => $pl->txt("menu_p_mode_1"),
			self::PMODE_PUBLIC_ONLY => $pl->txt("menu_p_mode_2")
			);
	}
	
	/**
	 * Get menus
	 */
	static function getMenus($a_include_submenues = false)
	{
		global $ilDB;

		$sub = ($a_include_submenues)
			? ""
			: " AND it_type <> ".$ilDB->quote(self::T_SUBMENU, "integer");

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_it ".
			" WHERE menu_id = ".$ilDB->quote(0, "integer").
			$sub.
			" ORDER BY nr "
			);
		$menus = array();
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$menus[] = $rec;
		}
		return $menus;
	}

	/**
	 * Get menu
	 */
	static function getMenu($a_menu_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_it ".
			" WHERE id = ".$ilDB->quote($a_menu_id, "integer"));
		$rec = $ilDB->fetchAssoc($set);
		return $rec;
	}

	/**
	 * Add menu
	 *
	 * @param
	 * @return
	 */
	static function addMenu($a_title, $a_type, $a_acc_ref_id, $a_acc_perm, $a_pmode,
		$a_append_lv = false)
	{
		global $ilDB, $ilUser, $lng;

		$max = 0;
		if ($a_type != self::T_SUBMENU)
		{
			$max = lfCustomMenu::getMaxMenuNr();
		}
		// menu
		$nid = $ilDB->nextId("ui_uihk_lfmainmenu_it");
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_it ".
			"(id, it_type, nr, acc_ref_id, acc_perm,pmode,append_last_visited) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote($a_type, "text").",".
			$ilDB->quote($max + 1, "integer").",".
			$ilDB->quote($a_acc_ref_id, "integer").",".
			$ilDB->quote($a_acc_perm, "text").",".
			$ilDB->quote($a_pmode, "integer").",".
			$ilDB->quote($a_append_lv, "integer").
			")");
		
		// title
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_tl ".
			"(id, type, title, lang) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote("it", "text").",".
			$ilDB->quote($a_title, "text").",".
			$ilDB->quote($lng->getDefaultLanguage(), "text").
			")");

		return $nid;
	}
	
	/**
	 * Update menu
	 *
	 * @param
	 */
	function updateMenu($a_menu_id, $a_type, $a_acc_ref_id, $a_acc_perm, $a_pmode,
		$a_append_lv)
	{
		global $ilDB;

		$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
			" it_type = ".$ilDB->quote($a_type, "text").",".
			" acc_ref_id = ".$ilDB->quote($a_acc_ref_id, "integer").",".
			" pmode = ".$ilDB->quote($a_pmode, "integer").",".
			" acc_perm = ".$ilDB->quote($a_acc_perm, "text").",".
			" append_last_visited = ".$ilDB->quote($a_append_lv, "integer").
			" WHERE id = ".$ilDB->quote($a_menu_id, "integer")
		);
	}
	
	/**
	 * Activate menu
	 *
	 * @param
	 * @return
	 */
	static function activateMenu($a_id, $a_active)
	{
		global $ilDB;
		
		$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
			" active  = ".$ilDB->quote((int) $a_active, "integer").
			" WHERE id = ".$ilDB->quote($a_id, "integer")
			);
	}
	
	
	////
	//// Get menu item related stuff
	////
	
	/**
	 * Update menu
	 *
	 * @param
	 */
	static function updateMenuItem($a_id, $a_target, $a_acc_ref_id, $a_acc_perm, $a_pmode,
		$a_type = 0, $a_ref_id = "", $a_newwin = 0, $a_feature_id = "", $a_append_lv = 0)
	{
		global $ilDB;

		$ilDB->manipulate($q = "UPDATE ui_uihk_lfmainmenu_it SET ".
			" target = ".$ilDB->quote($a_target, "text").",".
			" acc_ref_id = ".$ilDB->quote($a_acc_ref_id, "integer").",".
			" acc_perm = ".$ilDB->quote($a_acc_perm, "text").",".
			" it_type = ".$ilDB->quote($a_type, "integer").",".
			" ref_id = ".$ilDB->quote($a_ref_id, "integer").",".
			" newwin = ".$ilDB->quote((int) $a_newwin, "integer").",".
			" pmode = ".$ilDB->quote($a_pmode, "integer").",".
			" feature_id = ".$ilDB->quote($a_feature_id, "text").",".
			" append_last_visited = ".$ilDB->quote($a_append_lv, "integer").
			" WHERE id = ".$ilDB->quote($a_id, "integer")
		);

	}

	/**
	 * Update menu item target
	 *
	 * @param
	 */
	static function updateMenuItemTarget($a_id, $a_target)
	{
		global $ilDB;

		$ilDB->manipulate($q = "UPDATE ui_uihk_lfmainmenu_it SET ".
			" target = ".$ilDB->quote($a_target, "text").
			" WHERE id = ".$ilDB->quote($a_id, "integer")
		);
	}
	

	/**
	 * Get menu
	 */
	static function getMenuItem($a_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_it ".
			" WHERE id = ".$ilDB->quote($a_id, "integer"));
		$rec = $ilDB->fetchAssoc($set);

		return $rec;
	}

	/**
	 * Get menu items of a menu
	 *
	 * @return	array		array of courses
	 */
	static function getMenuItems($a_menu_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_it ".
			" WHERE menu_id = ".$ilDB->quote($a_menu_id, "integer").
			" ORDER BY nr "
			);
		$items = array();
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$items[] = $rec;
		}
		return $items;
	}

	/**
	 * Add item
	 *
	 * @param
	 * @return
	 */
	static function addMenuItem($a_menu_id, $a_title, $a_target, $a_acc_ref_id, $a_acc_perm,
		$a_pmode, $a_type, $a_ref_id, $a_newwin = 0, $a_feature_id = 0, $a_append_lv = 0)
	{
		global $ilDB, $lng;

		$max = lfCustomMenu::getMaxItemNr($a_menu_id);

		if ($a_type == self::ITEM_TYPE_SUBMENU)
		{
//			$submenu_id = self::addMenu("", self::T_SUBMENU, 0, "", 0);
		}

		$nid = $ilDB->nextId("ui_uihk_lfmainmenu_it");
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_it ".
			"(id, menu_id, nr, target, acc_ref_id, acc_perm, pmode, it_type, ref_id, newwin, feature_id, submenu_id, active, append_last_visited) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote($a_menu_id, "integer").",".
			$ilDB->quote($max + 1, "integer").",".
			$ilDB->quote($a_target, "text").",".
			$ilDB->quote($a_acc_ref_id, "integer").",".
			$ilDB->quote($a_acc_perm, "text").",".
			$ilDB->quote($a_pmode, "integer").",".
			$ilDB->quote($a_type, "integer").",".
			$ilDB->quote($a_ref_id, "integer").",".
			$ilDB->quote((int) $a_newwin, "integer").",".
			$ilDB->quote($a_feature_id, "text").",".
			$ilDB->quote($submenu_id, "integer").",".
			$ilDB->quote(1, "integer").",".
			$ilDB->quote($a_append_lv, "integer").

			")");
		
		// title
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_tl ".
			"(id, type, title, lang) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote("it", "text").",".
			$ilDB->quote($a_title, "text").",".
			$ilDB->quote($lng->getDefaultLanguage(), "text").
			")");

	}

	/**
	 * Delete item
	 *
	 * @param
	 * @return
	 */
	static function deleteItem($a_id)
	{
		global $ilDB;

		$ilDB->manipulate("DELETE FROM ui_uihk_lfmainmenu_it WHERE "
			." id = ".$ilDB->quote($a_id, "integer")
		);

	}

	/**
	 * Get max nr
	 *
	 * @return	int		max number
	 */
	static function getMaxItemNr($a_menu_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT MAX(nr) nr FROM ui_uihk_lfmainmenu_it ".
			"WHERE menu_id = ".$ilDB->quote($a_menu_id, "integer"));
		$rec = $ilDB->fetchAssoc($set);

		return (int) $rec["nr"];
	}

	/**
	 * Get max menu nr
	 *
	 * @return	int		max number
	 */
	static function getMaxMenuNr()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT MAX(nr) nr FROM ui_uihk_lfmainmenu_it WHERE menu_id = 0");
		$rec = $ilDB->fetchAssoc($set);

		return (int) $rec["nr"];
	}

	/**
	 * Lookup ref id
	 *
	 * @param	int		entry id
	 * @return	int		ref id
	 */
	static function lookupRefId($a_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT ref_id FROM ui_uihk_lfmainmenu_it WHERE ".
			" id = ".$ilDB->quote($a_id, "integer")
		);
		$rec = $ilDB->fetchAssoc($set);

		return (int) $rec["ref_id"];
	}

	/**
	 * Delete menu
	 *
	 * @param
	 * @return
	 */
	static function deleteMenu($a_id)
	{
		global $ilDB;

		$ilDB->manipulate("DELETE FROM ui_uihk_lfmainmenu_it WHERE "
			." id = ".$ilDB->quote($a_id, "integer")
		);
		$ilDB->manipulate("DELETE FROM ui_uihk_lfmainmenu_it WHERE "
			." menu_id = ".$ilDB->quote($a_id, "integer")
		);

	}

	/**
	 * Fix numbering
	 *
	 * @param
	 * @return
	 */
	static function fixNumbering()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT id FROM ui_uihk_lfmainmenu_it WHERE menu_id = 0 ".
			" ORDER BY nr "
			);
		$cnt = 1;
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
				" nr = ".$ilDB->quote($cnt, "integer").
				" WHERE id = ".$ilDB->quote($rec["id"], "integer")
				);
			$cnt++;
		}
	}

	/**
	 * Set numbering
	 *
	 * @param
	 * @return
	 */
	static function setNumbering($a_nr)
	{
		global $ilDB;

		asort($a_nr);
		$cnt = 1;
		foreach ($a_nr as $id => $v)
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
				" nr = ".$ilDB->quote($cnt, "integer").
				" WHERE id = ".$ilDB->quote($id, "integer")
				);
			$cnt++;
		}
	}
	
	/**
	 * Fix item numbering
	 *
	 * @param
	 */
	static function fixItemNumbering($a_menu_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT id FROM ui_uihk_lfmainmenu_it ".
			" WHERE menu_id = ".$ilDB->quote($a_menu_id, "integer").
			" ORDER BY nr "
			);
		$cnt = 1;
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
				" nr = ".$ilDB->quote($cnt, "integer").
				" WHERE id = ".$ilDB->quote($rec["id"], "integer")
				);
			$cnt++;
		}
	}

	/**
	 * Set item numbering
	 *
	 * @param
	 * @return
	 */
	static function setItemNumbering($a_nr)
	{
		global $ilDB;

		asort($a_nr);
		$cnt = 1;
		foreach ($a_nr as $id => $v)
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
				" nr = ".$ilDB->quote($cnt, "integer").
				" WHERE id = ".$ilDB->quote($id, "integer")
				);
			$cnt++;
		}
	}
	
	/**
	 * Get item presentation title
	 *
	 * @param
	 * @return
	 */
	static function getItemPresentationTitle($a_id, $a_type, $a_ref_id, $a_lang, $a_full_feature_id)
	{
		global $lng, $ilPluginAdmin;

		$pl = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE, "UIComponent",
			"uihk", "LfMainMenu");

		if ($a_type == self::ITEM_TYPE_LAST_VISITED)
		{
			return $lng->txt("last_visited");
		}
		if ($a_type == self::ITEM_TYPE_SEPARATOR)
		{
			return $pl->txt("separator");
		}

		$title = self::lookupTitle("it", $a_id, $a_lang, true);


		if ($title == "")
		{
			if ($a_type == self::ITEM_TYPE_REF_ID)
			{
				return ilObject::_lookupTitle(ilObject::_lookupObjId($a_ref_id));
			}
			else if ($a_type == self::ITEM_TYPE_PD_MENU)
			{
				return $lng->txt("personal_desktop");
			}
			else if ($a_type == self::ITEM_TYPE_REP_MENU)
			{
				global $tree;
				$nd = $tree->getNodeData(ROOT_FOLDER_ID);
				$title = $nd["title"];
				if ($title == "ILIAS")
				{
					$title = $lng->txt("repository");
				}
				return $title;
			}
			else if ($a_type == self::ITEM_TYPE_ADMIN)
			{
				return $pl->txt("administration");
			}
			else
			{
				if ($title == "" && $a_type == self::ITEM_TYPE_FEATURE)
				{
					$feat = $pl->getFeatureById($a_full_feature_id);
					$title = $feat["feature"];
				}

			}
		}
		return $title;
	}
	
	
	/**
	 * Lookup title
	 *
	 * @param
	 * @return
	 */
	static function lookupTitle($a_type, $a_id, $a_lang = "", $a_fallback = false)
	{
		global $ilDB, $lng;
		
		if ($a_lang == "")
		{
			$a_lang = $lng->getDefaultLanguage();
		}
		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_tl ".
			" WHERE id = ".$ilDB->quote($a_id, "integer").
			" AND type = ".$ilDB->quote($a_type, "text").
			" AND lang = ".$ilDB->quote($a_lang, "text")
			);
		$rec  = $ilDB->fetchAssoc($set);
		
		if ($rec["title"] == "" && $a_lang != $lng->getDefaultLanguage()
			&& $a_fallback)
		{
			return self::lookupTitle($a_type, $a_id);
		}
		
		return $rec["title"];
	}

	/**
	 * Save title
	 *
	 * @param
	 * @return
	 */
	static function saveTitle($a_type, $a_id, $a_lang, $a_title)
	{
		global $ilDB;
		
		$ilDB->replace("ui_uihk_lfmainmenu_tl",
			array("id" => array("integer", $a_id),
				"type" => array("text", $a_type),
				"lang" => array("text", $a_lang)),
			array("title" => array("text", $a_title))
			);
	}
	
	////
	//// Language dependent item targets
	////
	
	/**
	 * Delete language dep. targets
	 *
	 * @param
	 * @return
	 */
	static function deleteLDTargets($a_item_id)
	{
		global $ilDB;
		
		$ilDB->manipulate("DELETE FROM ui_uihk_lfmainmenu_ldt WHERE ".
			" item_id = ".$ilDB->quote($a_item_id, "integer")
			);
	}
	
	/**
	 * Get language dependent targets
	 *
	 * @param
	 * @return
	 */
	static function getLDTargets($a_item_id)
	{
		global $ilDB, $lng;
		
		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_ldt ".
			" WHERE item_id = ".$ilDB->quote($a_item_id, "integer")
			);
		$targets = array();
		while ($rec  = $ilDB->fetchAssoc($set))
		{
			if ($lng->getDefaultLanguage() != $rec["lang"])
			{
				$targets[$rec["lang"]] = $rec["target"];
			}
			else
			{
				$it = self::getMenuItem($a_item_id);
				$targets[$rec["lang"]] = $it["target"];
			}
		}
		return $targets;
	}
	
	/**
	 * Set language dependent targets
	 *
	 * @param
	 * @return
	 */
	static function setLDTargets($a_item_id, $a_targets)
	{
		global $ilDB, $lng;
		
		self::deleteLDTargets($a_item_id);
		
		foreach ($a_targets as $l => $t)
		{
			$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_ldt ".
				"(item_id, lang, target) VALUES (".
				$ilDB->quote($a_item_id, "integer").",".
				$ilDB->quote(ilUtil::stripSlashes($l), "text").",".
				$ilDB->quote(ilUtil::stripSlashes($t), "text").
				")");
			
			if ($lng->getDefaultLanguage() == $l)
			{
				self::updateMenuItemTarget($a_item_id, ilUtil::stripSlashes($t));
			}
		}
	}

	/**
	 * Get item type name
	 *
	 * @param
	 * @return
	 */
	static function getItemTypeName($a_type)
	{
		$pl = self::getPlugin();

		switch ($a_type)
		{
			case self::ITEM_TYPE_URL: 			return $pl->txt("target");
			case self::ITEM_TYPE_REF_ID: 		return $pl->txt("ref_id");
			case self::ITEM_TYPE_LAST_VISITED: 	return $pl->txt("append_lv");
			case self::ITEM_TYPE_SEPARATOR: 	return $pl->txt("separator");
			case self::ITEM_TYPE_FEATURE: 		return $pl->txt("feature");
			case self::ITEM_TYPE_SUBMENU: 		return $pl->txt("submenu");
			case self::ITEM_TYPE_ADMIN: 		return $pl->txt("administration");
		}
		return "";
	}

	/**
	 * Get plugin object
	 *
	 * @param
	 * @return
	 */
	static function getPlugin()
	{
		global $ilPluginAdmin;

		if (!is_object(self::$pl))
		{
			self::$pl = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE, "UIComponent",
				"uihk", "LfMainMenu");
		}

		return self::$pl;
	}


}
?>
