<#1>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'menu_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'nr' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	),
	'acc_ref_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'acc_perm' => array (
		'type' => 'text',
		'length' => 20,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_it", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_it", array("id"));
$ilDB->createSequence('ui_uihk_lfmainmenu_it');

?>
<#2>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'type' => array (
		'type' => 'text',
		'length' => 10,
		'notnull' => true
	),
	'nr' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	),
	'acc_ref_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'acc_perm' => array (
		'type' => 'text',
		'length' => 20,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_mn", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_mn", array("id"));
$ilDB->createSequence('ui_uihk_lfmainmenu_mn');

?>
<#3>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'type' => array (
		'type' => 'text',
		'length' => 5,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'title' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_tl", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_tl", array("id", "type"));
$ilDB->createSequence('ui_uihk_lfmainmenu_tl');

?>
<#4>
<?php
$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_tl", "title",
		array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	));
?>
<#5>
<?php
$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_it", "target",
		array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	));

?>
<#6>
<?php

	$ilDB->dropPrimaryKey("ui_uihk_lfmainmenu_tl");
	$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_tl", array("id", "type", "lang"));

?>
<#7>
<?php
	//
?>
<#8>
<?php
	//
?>
<#9>
<?php
	//
?>
<#10>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "pmode",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#11>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "pmode",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#12>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "it_type",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#13>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "ref_id",
	array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => false,
		'default' => 0
	));
?>
<#14>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "active",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#15>
<?php
$fields = array(
	'item_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_ldt", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_ldt", array("item_id", "lang"));

?>
<#16>
<?php
$ilDB->dropTable("ui_uihk_lfmainmenu_ldt");

$fields = array(
	'item_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_ldt", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_ldt", array("item_id", "lang"));

?>
<#17>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "append_last_visited",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#18>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "newwin",
		array (
		'type' => 'integer',
		'length' => 1,
		'default' => 0,
		'notnull' => false
	));

?>
<#19>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "feature_id",
	array (
		'type' => 'text',
		'length' => 400,
		'notnull' => false
	));

?>
<#20>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "submenu_id",
	array (
		'type' => 'integer',
		'length' => 4,
		'default' => 0,
		'notnull' => false
	));

?>
<#21>
<?php

$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_it", 'acc_perm', array (
	'type' => 'text',
	'length' => 20,
	'notnull' => false,
	'default' => null
)
);

?>
<#22>
<?php

$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_mn", 'acc_perm', array (
		'type' => 'text',
		'length' => 20,
		'notnull' => false,
		'default' => null
	)
);

?>
<#23>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "active",
	array (
		'type' => 'integer',
		'length' => 1,
		'default' => 0,
		'notnull' => false
	));
$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
	" active = ".$ilDB->quote(1, "integer")
	);
?>
<#24>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "append_last_visited",
	array (
		'type' => 'integer',
		'length' => 1,
		'default' => 0,
		'notnull' => false
	));
?>
<#25>
<?php
$set = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_mn "
	);
while ($r = $ilDB->fetchAssoc($set))
{
	switch ($r["type"])
	{
		case "pd":
			$it_type = 8;
			break;
		case "rep":
			$it_type = 9;
			break;
		case "custom":
			$it_type = 7;
			break;
	}
	if ($r["type"] != "submenu")
	{
		$nid = $ilDB->nextId("ui_uihk_lfmainmenu_it");
		$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_it ".
			"(id, menu_id, nr, target, acc_ref_id, acc_perm, pmode, it_type, ref_id, newwin, feature_id, submenu_id, active, append_last_visited) VALUES (".
			$ilDB->quote($nid, "integer").",".
			$ilDB->quote(0, "integer").",".
			$ilDB->quote($r["nr"], "integer").",".
			$ilDB->quote("", "text").",".
			$ilDB->quote($r["acc_ref_id"], "integer").",".
			$ilDB->quote($r["acc_perm"], "text").",".
			$ilDB->quote($r["pmode"], "integer").",".
			$ilDB->quote($it_type, "integer").",".
			$ilDB->quote(0, "integer").",".
			$ilDB->quote(0, "integer").",".
			$ilDB->quote("", "text").",".
			$ilDB->quote(0, "integer").",".
			$ilDB->quote($r["active"], "integer").",".
			$ilDB->quote($r["append_last_visited"], "integer").
			")");

		// link items to new entries
		$ilDB->manipulate("UPDATE ui_uihk_lfmainmenu_it SET ".
			" menu_id = ".$ilDB->quote($nid, "integer").
			" WHERE menu_id = ".$ilDB->quote($r["id"], "integer")
			);

		// migrate language entries
		$set2 = $ilDB->query("SELECT * FROM ui_uihk_lfmainmenu_tl ".
			" WHERE type = ".$ilDB->quote("mn", "text").
			" AND id = ".$ilDB->quote($r["id"], "integer")
			);
		while ($rec2 = $ilDB->fetchAssoc($set2))
		{
			$ilDB->manipulate("INSERT INTO ui_uihk_lfmainmenu_tl ".
				"(id, type, lang, title) VALUES (".
				$ilDB->quote($nid, "integer").",".
				$ilDB->quote("it", "text").",".
				$ilDB->quote($rec2["lang"], "text").",".
				$ilDB->quote($rec2["title"], "text").
				")");
		}
	}
}
?>