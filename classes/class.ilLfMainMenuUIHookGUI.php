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
	 * @var ilLanguage
	 */
	protected $lng;

	/**
	 * @var ilSetting
	 */
	protected $settings;

	/**
	 * construct
	 *
	 * @param
	 * @return
	 */
	public function __construct()
	{
	}

	/**
	 * is_5_4
	 *
	 * @return bool
	 */
	protected function is_5_4()
	{
		if (substr(ILIAS_VERSION_NUMERIC, 0, 3) == "5.4")
		{
			return true;
		}
		return false;
	}


	/**
	 * Get html for ui area
	 *
	 * @param
	 * @return
	 */
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		global $DIC;

		$this->lng = $DIC->language();
		$this->settings = $DIC->settings();
		//$this->nav_history = $DIC["ilNavigationHistory"];
		$this->nav_history = new ilNavigationHistory();
		$this->ctrl = $DIC->ctrl();

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
					if ($ilUser->getId() != ANONYMOUS_USER_ID && !$this->is_5_4())
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
					if ($ilUser->getId() == ANONYMOUS_USER_ID)
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

						$this->renderEntry($tpl, "repository",
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
					$rep_done = true;
					break;
					
				case lfCustomMenu::ITEM_TYPE_CUSTOM_MENU:
					if ($ilUser->getId() == ANONYMOUS_USER_ID)
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
					if ($ilUser->getId() == ANONYMOUS_USER_ID)
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
					if ($ilUser->getId() == ANONYMOUS_USER_ID)
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
						$this->renderDropDown($tpl, "administration");
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
			$this->renderDropDown($tpl, "administration");
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
		global $ilUser, $lng, $ilAccess;

		$cust_done = false;
		foreach ($items as $item)
		{
			if ($ilUser->getId() == ANONYMOUS_USER_ID)
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
				$ilUser->getId() != ANONYMOUS_USER_ID)
			{
				$nav_items = $this->nav_history->getItems();
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
				$sm = $gl->addSubmenu($item["id"], lfCustomMenu::getItemPresentationTitle($item["id"], $item["it_type"],
						$item["ref_id"], $ilUser->getLanguage(), $item["full_id"]));
				$items = lfCustomMenu::getMenuItems($item["id"]);
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

	/**
	 * GetDropDownHTML
	 *
	 * @param
	 */
	function renderDropDown($a_tpl, $a_id)
	{
		$lng = $this->lng;
		$ilSetting = $this->settings;

		$id = strtolower($a_id);
		$a_tpl->setCurrentBlock("entry_".$id);
		include_once("./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php");
		$selection = new ilAdvancedSelectionListGUI();
		if ($this->active == $a_id || ($this->active == "" && $a_id == "repository"))
		{
			$selection->setSelectionHeaderClass("MMActive");
			$a_tpl->setVariable("SEL", '<span class="ilAccHidden">('.$lng->txt("stat_selected").')</span>');
		}
		else
		{
			$selection->setSelectionHeaderClass("MMInactive");
		}

		$selection->setSelectionHeaderSpanClass("MMSpan");

		$selection->setHeaderIcon(ilAdvancedSelectionListGUI::ICON_ARROW);
		$selection->setItemLinkClass("small");
		$selection->setUseImages(false);

		switch ($id)
		{
			// desktop drop down
			case "desktop":
				$selection->setListTitle($lng->txt("personal_desktop"));
				$selection->setId("dd_pd");

				// overview
				$selection->addItem($lng->txt("overview"), "", "ilias.php?baseClass=ilPersonalDesktopGUI",
					"", "", "_top");

				if(!$ilSetting->get("disable_personal_workspace"))
				{
					// workspace
					$selection->addItem($lng->txt("personal_workspace"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToWorkspace",
						"", "", "_top");
				}

				// profile
				$selection->addItem($lng->txt("personal_profile"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToProfile",
					"", "", "_top");

				// skills
				$skmg_set = new ilSetting("skmg");
				if ($skmg_set->get("enable_skmg"))
				{
					$selection->addItem($lng->txt("skills"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToSkills",
						"", "", "_top");
				}

				// portfolio
				if ($ilSetting->get('user_portfolios'))
				{
					$selection->addItem($lng->txt("portfolio"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToPortfolio",
						"", "", "_top");
				}

				// news
				if ($ilSetting->get("block_activated_news"))
				{
					$selection->addItem($lng->txt("news"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToNews",
						"", "", "_top");
				}

				// Learning Progress
				include_once("Services/Tracking/classes/class.ilObjUserTracking.php");
				if (ilObjUserTracking::_enabledLearningProgress())
				{
					//$ilTabs->addTarget("learning_progress", $this->ctrl->getLinkTargetByClass("ilLearningProgressGUI"));
					$selection->addItem($lng->txt("learning_progress"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToLP",
						"", "", "_top");
				}

				// calendar
				include_once('./Services/Calendar/classes/class.ilCalendarSettings.php');
				$settings = ilCalendarSettings::_getInstance();
				if($settings->isEnabled())
				{
					$selection->addItem($lng->txt("calendar"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToCalendar",
						"", "", "_top");
				}

				// mail
				if($this->mail)
				{
					$selection->addItem($lng->txt('mail'), '', 'ilias.php?baseClass=ilMailGUI',	'', '', '_top');
				}

				// contacts
				require_once 'Services/Contact/BuddySystem/classes/class.ilBuddySystem.php';
				if(ilBuddySystem::getInstance()->isEnabled())
				{
					$selection->addItem($lng->txt('mail_addressbook'), '', 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToContacts', '', '', '_top');
				}

				// private notes
				if (!$ilSetting->get("disable_notes"))
				{
					$selection->addItem($lng->txt("notes_and_comments"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToNotes",
						"", "", "_top");
				}

				// bookmarks
				if (!$ilSetting->get("disable_bookmarks"))
				{
					$selection->addItem($lng->txt("bookmarks"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToBookmarks",
						"", "", "_top");
				}

				// settings
				$selection->addItem($lng->txt("personal_settings"), "", "ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToSettings",
					"", "", "_top");

				break;

			// administration
			case "administration":
				$selection->setListTitle($lng->txt("administration"));
				$selection->setId("dd_adm");
				$selection->setAsynch(true);
				$selection->setAsynchUrl("ilias.php?baseClass=ilAdministrationGUI&cmd=getDropDown&cmdMode=asynch");
				break;

		}

		$a_tpl->setVariable("TXT_ADMINISTRATION", $lng->txt("administration"));
		$a_tpl->parseCurrentBlock();
	}

	/**
	 * Render main menu entry
	 *
	 * @param
	 * @return
	 */
	function renderEntry($a_tpl, $a_id, $a_txt, $a_script, $a_target = "_top")
	{
		$lng = $this->lng;
		$ilNavigationHistory = $this->nav_history;
		$ilSetting = $this->settings;
		$ilCtrl = $this->ctrl;

		$id = strtolower($a_id);
		$id_up = strtoupper($a_id);
		$a_tpl->setCurrentBlock("entry_".$id);

		include_once("./Services/UIComponent/GroupedList/classes/class.ilGroupedListGUI.php");

		// repository
		if ($a_id == "repository")
		{
			$gl = new ilGroupedListGUI();
			$gl->setAsDropDown(true);

			include_once("./Services/Link/classes/class.ilLink.php");
			$icon = ilUtil::img(ilObject::_getIcon(ilObject::_lookupObjId(1), "tiny"));

			$gl->addEntry($icon." ".$a_txt." - ".$lng->txt("rep_main_page"), ilLink::_getStaticLink(1,'root',true),
				"_top");

			$items = $ilNavigationHistory->getItems();
			reset($items);
			$cnt = 0;
			$first = true;

			foreach($items as $k => $item)
			{
				if ($cnt >= 10) break;

				if (!isset($item["ref_id"]) || !isset($_GET["ref_id"]) ||
					($item["ref_id"] != $_GET["ref_id"] || !$first))			// do not list current item
				{
					if ($cnt == 0)
					{
						$gl->addGroupHeader($lng->txt("last_visited"), "ilLVNavEnt");
					}
					$obj_id = ilObject::_lookupObjId($item["ref_id"]);
					$cnt ++;
					$icon = ilUtil::img(ilObject::_getIcon($obj_id, "tiny"));
					$ititle = ilUtil::shortenText(strip_tags($item["title"]), 50, true); // #11023
					$gl->addEntry($icon." ".$ititle, $item["link"],	"_top", "", "ilLVNavEnt");

				}
				$first = false;
			}

			if ($cnt > 0)
			{
				$gl->addEntry("» ".$lng->txt("remove_entries"), "#", "",
					"return il.MainMenu.removeLastVisitedItems('".
					$ilCtrl->getLinkTargetByClass("ilnavigationhistorygui", "removeEntries", "", true)."');",
					"ilLVNavEnt");
			}

			$a_tpl->setVariable("REP_EN_OV", $gl->getHTML());
		}

		// desktop
		if ($a_id == "desktop")
		{
			$gl = new ilGroupedListGUI();
			$gl->setAsDropDown(true);

			// overview
			$gl->addEntry($lng->txt("overview"),
				"ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToSelectedItems",
				"_top", "", "", "mm_pd_sel_items", ilHelp::getMainMenuTooltip("mm_pd_sel_items"),
				"left center", "right center", false);

			require_once 'Services/PersonalDesktop/ItemsBlock/classes/class.ilPDSelectedItemsBlockViewSettings.php';
			$pdItemsViewSettings = new ilPDSelectedItemsBlockViewSettings($GLOBALS['DIC']->user());

			// my groups and courses, if both is available
			if($pdItemsViewSettings->allViewsEnabled())
			{
				$gl->addEntry($lng->txt("my_courses_groups"),
					"ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToMemberships",
					"_top", "", "", "mm_pd_crs_grp", ilHelp::getMainMenuTooltip("mm_pd_crs_grp"),
					"left center", "right center", false);
			}

			// bookmarks
			if (!$ilSetting->get("disable_bookmarks"))
			{
				$gl->addEntry($lng->txt("bookmarks"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToBookmarks",
					"_top", "", "", "mm_pd_bookm", ilHelp::getMainMenuTooltip("mm_pd_bookm"),
					"left center", "right center", false);
			}

			// private notes
			if (!$ilSetting->get("disable_notes") || !$ilSetting->get("disable_comments"))
			{
				$lng->loadLanguageModule("notes");
				$t = $lng->txt("notes");
				$c = "jumpToNotes";
				if (!$ilSetting->get("disable_notes") && !$ilSetting->get("disable_comments"))
				{
					$t = $lng->txt("notes_and_comments");
				}
				if ($ilSetting->get("disable_notes"))
				{
					$t = $lng->txt("notes_comments");
					$c = "jumpToComments";
				}
				$gl->addEntry($t, "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=".$c,
					"_top", "", "", "mm_pd_notes", ilHelp::getMainMenuTooltip("mm_pd_notes"),
					"left center", "right center", false);
			}

			// news
			if ($ilSetting->get("block_activated_news"))
			{
				$gl->addEntry($lng->txt("news"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToNews",
					"_top", "", "", "mm_pd_news", ilHelp::getMainMenuTooltip("mm_pd_news"),
					"left center", "right center", false);
			}

			// overview is always active
			$gl->addSeparator();

			$separator = false;

			if($ilSetting->get("enable_my_staff") and ilMyStaffAccess::getInstance()->hasCurrentUserAccessToMyStaff() == true)
			{
				// my staff
				$gl->addEntry($lng->txt("my_staff"), "ilias.php?baseClass=" . ilPersonalDesktopGUI::class . "&cmd=" . ilPersonalDesktopGUI::CMD_JUMP_TO_MY_STAFF,
					"_top", "", "", "mm_pd_mst", ilHelp::getMainMenuTooltip("mm_pd_mst"),
					"left center", "right center", false);
				$separator = true;
			}

			if(!$ilSetting->get("disable_personal_workspace"))
			{
				// workspace
				$gl->addEntry($lng->txt("personal_workspace"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToWorkspace",
					"_top", "", "", "mm_pd_wsp", ilHelp::getMainMenuTooltip("mm_pd_wsp"),
					"left center", "right center", false);

				$separator = true;
			}


			// portfolio
			if ($ilSetting->get('user_portfolios'))
			{
				$gl->addEntry($lng->txt("portfolio"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToPortfolio",
					"_top", "", "", "mm_pd_port", ilHelp::getMainMenuTooltip("mm_pd_port"),
					"left center", "right center", false);

				$separator = true;
			}

			// skills
			$skmg_set = new ilSetting("skmg");
			if ($skmg_set->get("enable_skmg"))
			{
				$gl->addEntry($lng->txt("skills"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToSkills",
					"_top", "", "", "mm_pd_skill", ilHelp::getMainMenuTooltip("mm_pd_skill"),
					"left center", "right center", false);

				$separator = true;
			}

			require_once 'Services/Badge/classes/class.ilBadgeHandler.php';
			if(ilBadgeHandler::getInstance()->isActive())
			{
				$gl->addEntry($lng->txt('obj_bdga'),
					'ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToBadges', '_top'
					, "", "", "mm_pd_contacts", ilHelp::getMainMenuTooltip("mm_pd_badges"),
					"left center", "right center", false);

				$separator = true;
			}


			// Learning Progress
			include_once("Services/Tracking/classes/class.ilObjUserTracking.php");
			if (ilObjUserTracking::_enabledLearningProgress() &&
				(ilObjUserTracking::_hasLearningProgressOtherUsers() ||
					ilObjUserTracking::_hasLearningProgressLearner()))
			{
				//$ilTabs->addTarget("learning_progress", $this->ctrl->getLinkTargetByClass("ilLearningProgressGUI"));
				$gl->addEntry($lng->txt("learning_progress"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToLP",
					"_top", "", "", "mm_pd_lp", ilHelp::getMainMenuTooltip("mm_pd_lp"),
					"left center", "right center", false);

				$separator = true;
			}

			if($separator)
			{
				$gl->addSeparator();
			}

			$separator = false;

			// calendar
			include_once('./Services/Calendar/classes/class.ilCalendarSettings.php');
			$settings = ilCalendarSettings::_getInstance();
			if($settings->isEnabled())
			{
				$gl->addEntry($lng->txt("calendar"), "ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToCalendar",
					"_top", "", "", "mm_pd_cal", ilHelp::getMainMenuTooltip("mm_pd_cal"),
					"left center", "right center", false);

				$separator = true;
			}

			// mail
			if($this->mail)
			{
				$gl->addEntry($lng->txt('mail'), 'ilias.php?baseClass=ilMailGUI', '_top',
					"", "", "mm_pd_mail", ilHelp::getMainMenuTooltip("mm_pd_mail"),
					"left center", "right center", false);

				$separator = true;
			}

			// contacts
			require_once 'Services/Contact/BuddySystem/classes/class.ilBuddySystem.php';
			if(ilBuddySystem::getInstance()->isEnabled())
			{
				$gl->addEntry($lng->txt('mail_addressbook'),
					'ilias.php?baseClass=ilPersonalDesktopGUI&cmd=jumpToContacts', '_top'
					, "", "", "mm_pd_contacts", ilHelp::getMainMenuTooltip("mm_pd_contacts"),
					"left center", "right center", false);

				$separator = true;
			}


			$a_tpl->setVariable("DESK_CONT_OV", $gl->getHTML());
		}

		$a_tpl->setVariable("TXT_".$id_up, $a_txt);
		$a_tpl->setVariable("SCRIPT_".$id_up, $a_script);
		$a_tpl->setVariable("TARGET_".$id_up, $a_target);
		if ($this->active == $a_id || ($this->active == "" && $a_id == "repository"))
		{
			$a_tpl->setVariable("SEL", '<span class="ilAccHidden">('.$lng->txt("stat_selected").')</span>');
		}

		if($a_id == "repository")
		{
			include_once("./Services/Accessibility/classes/class.ilAccessKey.php");
			if (ilAccessKey::getKey(ilAccessKey::LAST_VISITED) != "")
			{
				$a_tpl->setVariable("ACC_KEY_REPOSITORY", 'accesskey="'.
					ilAccessKey::getKey(ilAccessKey::LAST_VISITED).'"');
			}
		}
		if($a_id == "desktop")
		{
			include_once("./Services/Accessibility/classes/class.ilAccessKey.php");
			if (ilAccessKey::getKey(ilAccessKey::PERSONAL_DESKTOP) != "")
			{
				$a_tpl->setVariable("ACC_KEY_DESKTOP", 'accesskey="'.
					ilAccessKey::getKey(ilAccessKey::PERSONAL_DESKTOP).'"');
			}
		}


		$a_tpl->parseCurrentBlock();
	}

}
?>
