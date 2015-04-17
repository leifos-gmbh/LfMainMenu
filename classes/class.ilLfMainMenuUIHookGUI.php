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
		global $tpl;

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

		include_once("./Services/YUI/classes/class.ilYuiUtil.php");
		ilYuiUtil::initConnection();

		$GLOBALS["tpl"]->addCSS($this->getPluginObject()->getStyleSheetLocation("lfmainmenu.css"));

		$tpl = $this->getPluginObject()->getTemplate("tpl.lf_main_menu_list_entries.html");
//		$a_par["main_menu_gui"]->renderMainMenuListEntries($tpl, false);

		$mm_gui = $a_par["main_menu_gui"];

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
			switch ($menu["it_type"])
			{
				case lfCustomMenu::ITEM_TYPE_PD_MENU:
					if ($_SESSION["AccountId"] != ANONYMOUS_USER_ID)
					{
						if (!$pd_done)
						{
							$title = lfCustomMenu::getItemPresentationTitle($menu["id"], $menu["it_type"],
								0, $ilUser->getLanguage(), $menu["full_id"]);

							$mm_gui->renderEntry($tpl, "desktop",
								$title, "#");
							
							include_once("./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php");
/*							$ov = new ilOverlayGUI("mm_desk_ov");
							$ov->setTrigger("mm_desk_tr");
							$ov->setAnchor("mm_desk_tr");
							$ov->setAutoHide(false);
							$ov->add();*/
							$tpl->setCurrentBlock("c_item");
							$tpl->parseCurrentBlock();
						}
						$pd_done = true;
					}
					break;
					
				case lfCustomMenu::ITEM_TYPE_REP_MENU:
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

						$title = lfCustomMenu::getItemPresentationTitle($menu["id"], $menu["it_type"],
							0, $ilUser->getLanguage(), $menu["full_id"]);

						if ($_SESSION["AccountId"] != ANONYMOUS_USER_ID)
						{
							$mm_gui->renderEntry($tpl, "repository",
								$title, "#");
							include_once("./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php");
/*							$ov = new ilOverlayGUI("mm_rep_ov");
							$ov->setTrigger("mm_rep_tr");
							$ov->setAnchor("mm_rep_tr");
							$ov->setAutoHide(false);
							$ov->add();*/
							$tpl->setCurrentBlock("c_item");
							$tpl->parseCurrentBlock();
						}
					}
					$rep_done = true;
					break;
					
				case lfCustomMenu::ITEM_TYPE_CUSTOM_MENU:
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

						$this->getPluginObject()->includeClass("class.lfGroupedListGUI.php");
						$gl = new lfGroupedListGUI($this->getPluginObject());
						$gl->setAsDropDown(true);
	
	//					var_dump($menu);
						$cust_done = false;
						if (count($items) > 0)
						{
							$cust_done = $this->addMenuItems($gl, $items);

							if ($cust_done)
							{
								$tpl->setCurrentBlock("cust_menu");
								$tpl->setVariable("TXT_CUSTOM",
									lfCustomMenu::lookupTitle("it", $menu["id"], $ilUser->getLanguage(), true));
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
/*								$ov = new ilOverlayGUI("cust_mm_".$menu["id"]);
								$ov->setTrigger("cust_tr_".$menu["id"]);
								$ov->setAnchor("cust_tr_".$menu["id"]);
								$ov->setAutoHide(false);
								$ov->add();*/
							}
						}
					}

					break;

				case lfCustomMenu::ITEM_TYPE_FEATURE:
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

					// check visibility
					$f = $this->getPluginObject()->getFeatureById($menu["feature_id"]);
					if (is_array($f) && $f["instance"]->isVisible($f["feature_id"]))
					{
						$tpl->setCurrentBlock("simple_item");
						$tpl->setVariable("SI_TEXT", lfCustomMenu::getItemPresentationTitle($menu["id"], $menu["it_type"],
							0, $ilUser->getLanguage(), $f["full_id"]));
						$tpl->setVariable("SI_HREF", $f["instance"]->getHref($f["feature_id"]));
						$tpl->parseCurrentBlock();
						$tpl->setCurrentBlock("c_item");
						$tpl->parseCurrentBlock();
					}
					break;

				case lfCustomMenu::ITEM_TYPE_URL:
				case lfCustomMenu::ITEM_TYPE_REF_ID:
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

					$item = $menu;
					// urls and ref ids
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

						$tpl->setCurrentBlock("simple_item");
						$tpl->setVariable("SI_TEXT", lfCustomMenu::getItemPresentationTitle($menu["id"], $menu["it_type"],
							$menu["ref_id"], $ilUser->getLanguage(), ""));
						$tpl->setVariable("SI_HREF", $item["target"]);
						$tpl->setVariable("SI_TARGET", " target='".$ltarget."' ");
						$tpl->parseCurrentBlock();
						$tpl->setCurrentBlock("c_item");
						$tpl->parseCurrentBlock();

					}
					break;

				case lfCustomMenu::ITEM_TYPE_ADMIN:
					// we always render the administration, if user has the permission
					if(!$this->admin_menu && ilMainMenuGUI::_checkAdministrationPermission())
					{
						$mm_gui->renderDropDown($tpl, "administration");
						$tpl->setCurrentBlock("c_item");
						$tpl->parseCurrentBlock();
						$this->admin_menu = true;
					}
					break;
			}
		}

		// we always render the administration, if user has the permission
		if(!$this->admin_menu && ilMainMenuGUI::_checkAdministrationPermission())
		{
			$mm_gui->renderDropDown($tpl, "administration");
			$tpl->setCurrentBlock("c_item");
			$tpl->parseCurrentBlock();
		}

		$html = $tpl->get();
		return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => $html);
	}

	/**
	 * Add menu items
	 *
	 * @param lfGroupedListGUI $gl list gui
	 * @param array $items item array
	 * @return bool items added?
	 */
	function addMenuItems($gl, $items)
	{
		global $ilUser, $lng;

		$cust_done = false;
		foreach ($items as $item)
		{
			if ($_SESSION["AccountId"] == ANONYMOUS_USER_ID)
			{
				if ($item["pmode"] == lfCustomMenu::PMODE_NONPUBLIC_ONLY)
				{
					continue;
				}
			}
			else
			{
				if ($item["pmode"] == lfCustomMenu::PMODE_PUBLIC_ONLY)
				{
					continue;
				}
			}

			// urls and ref ids
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
						$item["ref_id"], $ilUser->getLanguage(), $item["full_id"]),
					$item["target"],
					$ltarget);
				$cust_done = true;
			}

			// features
			if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_FEATURE)
			{
				// check visibility
				$f = $this->getPluginObject()->getFeatureById($item["feature_id"]);
				if (is_array($f) && $f["instance"]->isVisible($f["feature_id"]))
				{
					$gl->addEntry(
						lfCustomMenu::getItemPresentationTitle($item["id"], $item["it_type"],
							0, $ilUser->getLanguage(), $f["full_id"]),
						$f["instance"]->getHref($f["feature_id"]));
					$cust_done = true;
				}
			}


			// separator
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

			// submenu
			if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_SUBMENU)
			{
				$sm = $gl->addSubmenu($item["submenu_id"], lfCustomMenu::getItemPresentationTitle($item["id"], $item["it_type"],
						$item["ref_id"], $ilUser->getLanguage(), $item["full_id"]));
				$items = lfCustomMenu::getMenuItems($item["submenu_id"]);
				if ($this->addMenuItems($sm, $items))
				{
					$cust_done = true;
				}
			}

			// we always render the administration, if user has the permission
			if ($item["it_type"] == lfCustomMenu::ITEM_TYPE_ADMIN)
			{
				$this->admin_menu = true;
				if(ilMainMenuGUI::_checkAdministrationPermission())
				{
					$gl->addRaw('<li class="dropdown yamm lfAdminDropDown"><a data-toggle="dropdown" class="dropdown-toggle" data-loaded="0" href="#" id="mm_adm_tr">Administration</a>
						<div id="adm_ajax_target"></div>
						<script>
								$(function() {
									$("#mm_adm_tr").on("click", function (event) {
										event.preventDefault();
										event.stopPropagation();
										$(this).parent().toggleClass("open");
										if ($("#mm_adm_tr").attr("data-loaded") == 0) {
											$("#mm_adm_tr").attr("data-loaded", "1");
											il.Util.sendAjaxGetRequestToUrl ("ilias.php?baseClass=ilAdministrationGUI&cmd=getDropDown&cmdMode=asynch", {}, {el_id: "adm_ajax_target", inner: false}, function(o) {
												// perform page modification
												if(o.responseText !== undefined) {
													$("#" + o.argument.el_id).replaceWith(o.responseText);
													il.Util.fixPosition($("#mm_adm_tr").siblings("ul.dropdown-menu"));
													if (il.Help) {
														il.Help.updateTooltips();
													}
													var el = $(".ilMainMenu.ilTopFixed");
													if (el) {
				//									il.UICore.shrinkFixedElementToViewport(el);
													}
												}

											});
									}
									});
								});
						</script>
					</li>');
					$cust_done = true;
				}
			}

		}
		return $cust_done;
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
