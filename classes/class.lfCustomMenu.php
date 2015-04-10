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
	const T_PD = "pd";
	const T_REP = "rep";
	const T_CUSTOM = "custom";
	
	const PMODE_BOTH = 0;
	const PMODE_NONPUBLIC_ONLY = 1;
	const PMODE_PUBLIC_ONLY = 2;
	
	const ITEM_TYPE_URL = 0;
	const ITEM_TYPE_REF_ID = 1;
	const ITEM_TYPE_LAST_VISITED = 2;
	const ITEM_TYPE_SEPARATOR = 3;
	
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
			self::T_CUSTOM => $pl->txt("menu_t_custom"),
			self::T_PD => $pl->txt("menu_t_pd"),
			self::T_REP => $pl->txt("menu_t_rep")
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
	function getMenus()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_mn ".
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

		$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_mn ".
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
	function addMenu($a_title, $a_type, $a_acc_ref_id, $a_acc_perm, $a_pmode,
		$a_append_lv = false)
	{
		global $ilDB, $ilUser, $lng;

		$max = lfCustomMenu::getMaxMenuNr();

		// menu
		$nid = $ilDB->nextId("ui_uihk_lfmainmenu_mn");
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_mn ".
			"(id, type, nr, acc_ref_id, acc_perm,pmode,append_last_visited) VALUES (".
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
			$ilDB->quote("mn", "text").",".
			$ilDB->quote($a_title, "text").",".
			$ilDB->quote($lng->getDefaultLanguage(), "text").
			")");
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

		$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_mn SET ".
			" type = ".$ilDB->quote($a_type, "text").",".
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
	function activateMenu($a_id, $a_active)
	{
		global $ilDB;
		
		$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_mn SET ".
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
	function updateMenuItem($a_id, $a_target, $a_acc_ref_id, $a_acc_perm, $a_pmode,
		$a_type = 0, $a_ref_id = "", $a_newwin = 0)
	{
		global $ilDB;

		$ilDB->manipulate($q = "UPDATE ui_uihk_lfmainmenu_it SET ".
			" target = ".$ilDB->quote($a_target, "text").",".
			" acc_ref_id = ".$ilDB->quote($a_acc_ref_id, "integer").",".
			" acc_perm = ".$ilDB->quote($a_acc_perm, "text").",".
			" it_type = ".$ilDB->quote($a_type, "integer").",".
			" ref_id = ".$ilDB->quote($a_ref_id, "integer").",".
			" newwin = ".$ilDB->quote((int) $a_newwin, "integer").",".
			" pmode = ".$ilDB->quote($a_pmode, "integer").
			" WHERE id = ".$ilDB->quote($a_id, "integer")
		);
	}

	/**
	 * Update menu item target
	 *
	 * @param
	 */
	function updateMenuItemTarget($a_id, $a_target)
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
	function getMenuItems($a_menu_id)
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
	function addMenuItem($a_menu_id, $a_title, $a_target, $a_acc_ref_id, $a_acc_perm,
		$a_pmode, $a_type, $a_ref_id, $a_newwin = 0)
	{
		global $ilDB, $lng;

		$max = lfCustomMenu::getMaxItemNr($a_menu_id);

		$nid = $ilDB->nextId("ui_uihk_lfmainmenu_it");
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_it ".
			"(id, menu_id, nr, target, acc_ref_id, acc_perm, pmode, it_type, ref_id, newwin) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote($a_menu_id, "integer").",".
			$ilDB->quote($max + 1, "integer").",".
			$ilDB->quote($a_target, "text").",".
			$ilDB->quote($a_acc_ref_id, "integer").",".
			$ilDB->quote($a_acc_perm, "text").",".
			$ilDB->quote($a_pmode, "integer").",".
			$ilDB->quote($a_type, "integer").",".
			$ilDB->quote($a_ref_id, "integer").",".
			$ilDB->quote((int) $a_newwin, "integer").
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
	function deleteItem($a_id)
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
	function getMaxItemNr($a_menu_id)
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
	function getMaxMenuNr()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT MAX(nr) nr FROM ui_uihk_lfmainmenu_mn ");
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
	function deleteMenu($a_id)
	{
		global $ilDB;

		$ilDB->manipulate("DELETE FROM ui_uihk_lfmainmenu_mn WHERE "
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
	function fixNumbering()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT id FROM ui_uihk_lfmainmenu_mn ".
			" ORDER BY nr "
			);
		$cnt = 1;
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_mn SET ".
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
	function setNumbering($a_nr)
	{
		global $ilDB;

		asort($a_nr);
		$cnt = 1;
		foreach ($a_nr as $id => $v)
		{
			$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_mn SET ".
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
	 * @return
	 */
	function fixItemNumbering($a_menu_id)
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
	function setItemNumbering($a_nr)
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
	function getItemPresentationTitle($a_id, $a_type, $a_ref_id, $a_lang)
	{
		global $lng, $ilPluginAdmin;
		
		
		$pl = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE, "UIComponent",
			"uihk", "LfMainMenu");

		if ($a_type == self::ITEM_TYPE_REF_ID)
		{
			return ilObject::_lookupTitle(ilObject::_lookupObjId($a_ref_id));
		}
		else if ($a_type == self::ITEM_TYPE_LAST_VISITED)
		{
			return $lng->txt("last_visited");
		}
		else if ($a_type == self::ITEM_TYPE_SEPARATOR)
		{
			return $pl->txt("separator");
		}
		else
		{
			return self::lookupTitle("it", $a_id, $a_lang, true);
		}
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
	function saveTitle($a_type, $a_id, $a_lang, $a_title)
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
	function deleteLDTargets($a_item_id)
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
	function getLDTargets($a_item_id)
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
	function setLDTargets($a_item_id, $a_targets)
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
	
}
?>
