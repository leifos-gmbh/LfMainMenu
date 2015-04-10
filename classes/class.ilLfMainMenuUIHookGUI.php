<?php

/* Copyright (c) 2012 Leifos GmbH, GPL2, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook class
 *
 * @author Alex Killing <killing@leifos.de>
 * @version $Id$
 *
 */
class ilLfMainMenuUIHookGUI extends ilUIHookPluginGUI
{
	/**
	 * Get html for ui area
	 *
	 * @param
	 * @return
	 */
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		if ($a_comp == "Services/MainMenu" && $a_part == "main_menu_list_entries")
		{
			return $this->replaceMainMenuListEntries($a_par);
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}

	/**
	 * Replace main menu list entries
	 *
	 * @param
	 * @return
	 */
	function replaceMainMenuListEntries($a_par)
	{
		global $lng, $tree, $ilUser, $ilAccess;
		
		$tpl = $this->getPluginObject()->getTemplate("tpl.lf_main_menu_list_entries.html");
//		$a_par["main_menu_gui"]->renderMainMenuListEntries($tpl, false);

		$mm_gui = $a_par["main_menu_gui"];

		// we always render the administration, if user has the permission
		if(ilMainMenuGUI::_checkAdministrationPermission())
		{
			$mm_gui->renderDropDown($tpl, "administration");
		}
		
		// render custom menus
		$this->getPluginObject()->includeClass("class.lfCustomMenu.php");
		$pd_done = false;
		$rep_done = false;
		foreach (lfCustomMenu::getMenus() as $menu)
		{
			if (!$menu["active"])
			{
				continue;
			}
			switch ($menu["type"])
			{
				case "pd":
					if ($_SESSION["AccountId"] != ANONYMOUS_USER_ID)
					{
						if (!$pd_done)
						{
							$mm_gui->renderEntry($tpl, "desktop",
								$lng->txt("personal_desktop"), "#");
							
							include_once("./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php");
							$ov = new ilOverlayGUI("mm_desk_ov");
							$ov->setTrigger("mm_desk_tr");
							$ov->setAnchor("mm_desk_tr");
							$ov->setAutoHide(false);
							$ov->add();
							$tpl->setCurrentBlock("c_item");
							$tpl->parseCurrentBlock();
						}
						$pd_done = true;
					}
					break;
					
				case "rep":
					if (!$rep_done)
					{
						if (is_file("./classes/class.ilLink.php"))
						{
							// ilias 4.2
							include_once("./classes/class.ilLink.php");
						}
						else
						{
							// ilias 4.3
							include_once("./Services/Link/classes/class.ilLink.php");
						}

						$nd = $tree->getNodeData(ROOT_FOLDER_ID);
						$title = $nd["title"];
						if ($title == "ILIAS")
						{
							$title = $lng->txt("repository");
						}
						if ($_SESSION["AccountId"] != ANONYMOUS_USER_ID)
						{
							$mm_gui->renderEntry($tpl, "repository",
								$title, "#");
							include_once("./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php");
							$ov = new ilOverlayGUI("mm_rep_ov");
							$ov->setTrigger("mm_rep_tr");
							$ov->setAnchor("mm_rep_tr");
							$ov->setAutoHide(false);
							$ov->add();
							$tpl->setCurrentBlock("c_item");
							$tpl->parseCurrentBlock();
						}
					}
					$rep_done = true;
					break;
					
				case "custom":
					if ($_SESSION["AccountId"] == ANONYMOUS_USER_ID)
					{
						if ($menu["pmode"] == lfCustomMenu::PMODE_NONPUBLIC_ONLY)
						{
							continue;
						}
					}
					else
					{
						if ($menu["pmode"] == lfCustomMenu::PMODE_PUBLIC_ONLY)
						{
							continue;
						}
					}
					
					if ((int) $menu["acc_ref_id"] == 0 ||
						$ilAccess->checkAccess($menu["acc_perm"], "",
							(int) $menu["acc_ref_id"]))
					{
					
						$items = lfCustomMenu::getMenuItems($menu["id"]);
						
						include_once("./Services/UIComponent/GroupedList/classes/class.ilGroupedListGUI.php");
						$gl = new ilGroupedListGUI();
						$gl->setAsDropDown(true);
	
	//					var_dump($menu);
						$cust_done = false;
						if (count($items) > 0)
						{
							foreach ($items as $item)
							{
								if (($item["it_type"] == lfCustomMenu::ITEM_TYPE_URL &&
										((int) $item["acc_ref_id"] == 0 ||
										$ilAccess->checkAccess($item["acc_perm"], "",
										(int) $item["acc_ref_id"]))
									) ||
									($item["it_type"] == lfCustomMenu::ITEM_TYPE_REF_ID)
								   )
								{
									if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_REF_ID)
									{
										if (is_file("./classes/class.ilLink.php"))
										{
											// ilias 4.2
											include_once("./classes/class.ilLink.php");
										}
										else
										{
											// ilias > 4.3
											include_once("./Services/Link/classes/class.ilLink.php");
										}
										
										$item["target"] = ilLink::_getLink($item["ref_id"]);
									}
									
									// set language dependent target, if given
									if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_URL)
									{
										$ldtargets = lfCustomMenu::getLDTargets($item["id"]);
										if ($ilUser->getLanguage() != $lng->getDefaultLanguage() &&
											$ldtargets[$ilUser->getLanguage()] != "")
										{
											$item["target"] = $ldtargets[$ilUser->getLanguage()];
										}
									}
									
									$ltarget = $item["newwin"]
										? "_blank"
										: "_top";
									
									$gl->addEntry(
										lfCustomMenu::getItemPresentationTitle($item["id"], $item["it_type"],
											$item["ref_id"], $ilUser->getLanguage()),
										$item["target"],
										$ltarget);
									$cust_done = true;
								}
								
								// last visited items
								if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_SEPARATOR)
								{
									$gl->addSeparator();
								}
								
								// last visited items
								if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_LAST_VISITED &&
									$_SESSION["AccountId"] != ANONYMOUS_USER_ID)
								{
									global $ilNavigationHistory;
									$nav_items = $ilNavigationHistory->getItems();
									reset($nav_items);
									$cnt = 0;
									$first = true;
						
									foreach($nav_items as $k => $nav_item)
									{
										if ($cnt >= 10) break;
										
										if (!isset($nav_item["ref_id"]) || !isset($_GET["ref_id"]) ||
											($nav_item["ref_id"] != $_GET["ref_id"] || !$first))			// do not list current item
										{
											if ($cnt == 0)
											{
												$gl->addGroupHeader($lng->txt("last_visited"));
											}
											$obj_id = ilObject::_lookupObjId($nav_item["ref_id"]);
											$cnt ++;
											$icon = ilUtil::img(ilObject::_getIcon($obj_id, "tiny"));
											$gl->addEntry($icon." ".ilUtil::shortenText($nav_item["title"], 50, true), $nav_item["link"],
												"_top");
						
										}
										$first = false;
									}

									if (!$first)
									{
										$cust_done = true;
									}
								}
							}
							
							if ($cust_done)
							{
								$tpl->setCurrentBlock("cust_menu");
								$tpl->setVariable("TXT_CUSTOM",
									lfCustomMenu::lookupTitle("mn", $menu["id"], $ilUser->getLanguage(), true));
								$tpl->setVariable("MM_CLASS", "MMInactive");
								
								if (is_file("./templates/default/images/mm_down_arrow.png"))
								{
									$tpl->setVariable("ARROW_IMG", ilUtil::getImagePath("mm_down_arrow.png"));
								}
								else
								{
									$tpl->setVariable("ARROW_IMG", ilUtil::getImagePath("mm_down_arrow.gif"));
								}
								$tpl->setVariable("CUSTOM_CONT_OV", $gl->getHTML());
								$tpl->setVariable("MM_ID", $menu["id"]);
								$tpl->parseCurrentBlock();
								$tpl->setCurrentBlock("c_item");
								$tpl->parseCurrentBlock();
			
								// overlay
								include_once("./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php");
								$ov = new ilOverlayGUI("cust_mm_".$menu["id"]);
								$ov->setTrigger("cust_tr_".$menu["id"]);
								$ov->setAnchor("cust_tr_".$menu["id"]);
								$ov->setAutoHide(false);
								$ov->add();
							}
						}
					}

					break;
			}
		}

		$html = $tpl->get();
		return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => $html);
	}

	/**
	 * GetDropDownHTML
	 *
	 * @param
	 * @return
	 */
	function getDropDownHTML($a_items, $a_nr)
	{
		include_once("./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php");
		$selection = new ilAdvancedSelectionListGUI();
		//$selection->setFormSelectMode("url_ref_id", "ilNavHistorySelect", true,
		//	"goto.php?target=navi_request", "ilNavHistory", "ilNavHistoryForm",
		//	"_top", $lng->txt("go"), "ilNavHistorySubmit");
		$selection->setListTitle($this->getPluginObject()->txt("featured_courses"));
		$selection->setId("lf_cust_men_".$a_nr);
		$selection->setSelectionHeaderClass("MMInactive");
//		$selection->setHeaderIcon(ilAdvancedSelectionListGUI::NO_ICON);
		if (is_file("./templates/default/images/mm_down_arrow.png"))
		{
			$selection->setHeaderIcon("mm_down_arrow.png");
		}
		else
		{
			$selection->setHeaderIcon("mm_down_arrow.gif");
		}
		$selection->setItemLinkClass("small");
		$selection->setUseImages(false);

		if (is_file("./classes/class.ilLink.php"))
		{
			// ilias 4.2
			include_once("./classes/class.ilLink.php");
		}
		else
		{
			// ilias 4.3
			include_once("./Services/Link/classes/class.ilLink.php");
		}

		foreach($a_items as $item)
		{
			$title = ilObject::_lookupTitle(ilObject::_lookupObjId($item["ref_id"]));
			$selection->addItem($title, $item["ref_id"], ilLink::_getLink($item["ref_id"]),
				"", "", "_top");
		}
		$html = $selection->getHTML();

		return $html;
	}
}
?>
