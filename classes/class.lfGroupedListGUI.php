<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/GroupedList/classes/class.ilGroupedListGUI.php");

/**
 * Extension of grouped list gui class, enables submenus
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 */
class lfGroupedListGUI extends ilGroupedListGUI
{
	protected $pl = null;
	protected $submenus = array();

	/**
	 * Constructor
	 */
	function __construct($a_pl)
	{
		$this->pl = $a_pl;
		parent::__construct();
	}


	/**
	 * Add submenu
	 *
	 * @param
	 * @return
	 */
	function addSubmenu($a_id, $a_title)
	{
		$list = new lfGroupedListGUI($this->pl);
		$this->submenus[$a_id] = array("list" => $list);
		$this->items[] = array("type" => "submenu", "menu_id" => $a_id, "title" => $a_title, "list" => $list);
		return $list;
	}

	/**
	 * Add submenu
	 *
	 * @param
	 * @return
	 */
	function addRaw($a_rawdata)
	{
		$this->items[] = array("type" => "raw", "data" => $a_rawdata);
	}

	/**
	 * Get HTML
	 *
	 * @param
	 * @return
	 */
	function getHTML()
	{
		global $ilCtrl;

		$GLOBALS["tpl"]->addJavascript($this->pl->getDirectory()."/js/lfMainMenu.js");

		$tpl = $this->pl->getTemplate("tpl.lf_grouped_list_gui.html");
		$tt_calls = "";

		foreach ($this->items as $i)
		{
			switch($i["type"])
			{
				case "sep":
					$tpl->touchBlock("sep");
					$tpl->touchBlock("item");
					break;

				case "next_col":
					$tpl->touchBlock("next_col");
					$tpl->touchBlock("item");
					break;

				case "group_head":
					$tpl->setCurrentBlock("group_head");
					if ($i["add_class"] != "")
					{
						$tpl->setVariable("ADD_CLASS", $i["add_class"]);
					}
					$tpl->setVariable("GROUP_HEAD", $i["content"]);
					$tpl->parseCurrentBlock();
					$tpl->touchBlock("item");
					break;

				case "entry":
					if ($i["href"] != "")
					{
						$tpl->setCurrentBlock("linked_entry");
						if ($i["add_class"] != "")
						{
							$tpl->setVariable("ADD_CLASS", $i["add_class"]);
						}
						$tpl->setVariable("HREF", $i["href"]);
						$tpl->setVariable("TXT_ENTRY", $i["content"]);
						if ($i["target"] != "")
						{
							$tpl->setVariable("TARGET", 'target="'.$i["target"].'"');
						}
						else
						{
							$tpl->setVariable("TARGET", 'target="_top"');
						}
						if ($i["onclick"] != "")
						{
							$tpl->setVariable("ONCLICK", 'onclick="'.$i["onclick"].'"');
						}
						if ($i["id"] != "")
						{
							$tpl->setVariable("ID", 'id="'.$i["id"].'"');
						}
						$tpl->parseCurrentBlock();
						$tpl->touchBlock("item");
						if ($i["ttip"] != "" && $i["id"] != "")
						{
							include_once("./Services/UIComponent/Tooltip/classes/class.ilTooltipGUI.php");
							if ($ilCtrl->isAsynch())
							{
								$tt_calls.= " ".ilTooltipGUI::getTooltip($i["id"], $i["ttip"],
										"", $i["tt_my"], $i["tt_at"], $i["tt_use_htmlspecialchars"]);
							}
							else
							{
								ilTooltipGUI::addTooltip($i["id"], $i["ttip"],
									"", $i["tt_my"], $i["tt_at"], $i["tt_use_htmlspecialchars"]);
							}
						}

					}
					else
					{
						$tpl->setCurrentBlock("unlinked_entry");
						if ($i["add_class"] != "")
						{
							$tpl->setVariable("ADD_CLASS2", $i["add_class"]);
						}
						$tpl->setVariable("TXT_ENTRY2", $i["content"]);
						$tpl->parseCurrentBlock();
					}
					break;

				case "submenu":
					$tpl->setCurrentBlock("submenu");
					$tpl->setVariable("SUBMENU_TITLE", $i["title"]);
					$tpl->setVariable("SUBMENU", $i["list"]->getHTML());
					$tpl->parseCurrentBlock();
					$tpl->touchBlock("item");
					break;

				case "raw":
					$tpl->setCurrentBlock("raw");
					$tpl->setVariable("RAW", $i["data"]);
					$tpl->parseCurrentBlock();
					$tpl->touchBlock("item");
					break;

			}
		}

		if ($this->multi_column)
		{
			$tpl->touchBlock("multi_start");
			$tpl->touchBlock("multi_end");
		}

		if ($tt_calls != "")
		{
			$tpl->setCurrentBlock("script");
			$tpl->setVariable("TT_CALLS", $tt_calls);
			$tpl->parseCurrentBlock();
		}

		if ($this->getAsDropDown())
		{
			if ($this->dd_pullright)
			{
				$tpl->setVariable("LIST_CLASS", "dropdown-menu pull-right");
			}
			else
			{
				$tpl->setVariable("LIST_CLASS", "dropdown-menu");
			}
			$tpl->setVariable("LIST_ROLE", "menu");
		}
		else
		{
			$tpl->setVariable("LIST_CLASS", "dropdown-menu");
			$tpl->setVariable("LIST_ROLE", "");
		}

		return $tpl->get();
	}

}

?>
