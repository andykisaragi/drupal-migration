<?php

echo "\n\n" . __FILE__ . "\n\n";

echo "DELETING ALL NODES!!\n\n";

//$sql = "select entity_id from wsd7_field_data_field_space where field_space_tid = 112;";
//$sql = "select nid as entity_id from wsd7_node where nid > " . variable_get('andy_migration_nid_offset');
//$sql = "select nid as entity_id from wsd7_node where nid > 3606";
/*$sql = "SELECT nid FROM $new_db.{$new_prefix}node";

$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }
$count = 0;
while($row = mysql_fetch_array($result)){

	node_delete($row['entity_id']);
	$count++;
	if($count%10 == 0) echo ' . ';
	if($count%100 == 0) echo "$count\n";

}*/

$tables = array();

if ($settings['delete_nodes']){
	$tables = array('node','node_revision');
	$prefixes = array('field_data_','field_revision','quiz_');
	foreach ($prefixes as $prefix){
		$sql = "SHOW TABLES LIKE '$prefix%';";
		echo "$sql\n";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)){
			$tables[] = $row[0];
		}
	}
}

foreach($tables as $table){
	$truncate_sql = "TRUNCATE TABLE $new_db.{$new_prefix}{$table}";
	echo "truncating $table ... ";
	$truncate_result = mysql_query($truncate_sql);
	if(!$truncate_result) { echo $truncate_sql . "\n" . mysql_error() . "\n"; }
}
	
echo "\n\n";


/*
echo "DELETING ALL DSHED FILES \n\n";
$sql = "DELETE FROM wsd7_file_managed WHERE fid > " . variable_get('andy_migration_fid_offset');
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

echo "DELETING ALL DSHED TERMS \n\n";
$sql = "select tid from wsd7_taxonomy_term_data where vid = 11";

$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }
while($row = mysql_fetch_array($result)){


	taxonomy_term_delete($row['tid']);
	echo $row['tid'] . " ";

}*/

