<?php

echo "\n\n" . __FILE__ . "\n\n";




$sql = "SELECT t.table_name AS table_name
FROM information_schema.tables AS t
WHERE t.table_name LIKE '{$new_prefix}field_data%'
OR t.table_name LIKE '{$new_prefix}field_revision%'
ORDER BY table_name";
$result = mysql_query($sql);
if(!$result) { echo "\n" . mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){
	$tables[$row['table_name']] = $row['table_name'];
}

$sql = "SELECT nid, language, tnid, translate FROM $old_db.{$old_prefix}node WHERE language <> '' OR tnid <> 0";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){

	extract($row);

	$updatesql = "UPDATE $new_db.{$new_prefix}node SET language = '$language', tnid = $tnid, translate = $translate WHERE nid = $nid";
	$updateresult = mysql_query($updatesql);
	if(!$updateresult) { echo mysql_error() . "\n"; }

	/*foreach ($tables as $table){

		$updatesql = "UPDATE $new_db.{$table} SET language = '$language' WHERE entity_id = $nid AND entity_type = 'node'";
		$updateresult = mysql_query($updatesql);
		if(!$updateresult) { echo mysql_error() . "\n"; }

	}*/
}