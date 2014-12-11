<?php

// imports node references

echo "\n\n" . __FILE__ . "\n\n";

// d7: field_data_field_notices
// entity_type, bundle, deleted, entity_id, revision_id, language, delta, field_notices_traget_id
// d6: content_field_notices
// vid, nid, delta, field_notices_nid


// shared fields
$fields = array("field_background_image",
	"field_featured_items",
	"field_item_sets",
	"field_projects", );

$nid_offset = NID_OFFSET;

foreach ($fields as $field){

	echo "\nImport Node References for $field\n";

	$src_table = "{$old_prefix}content_{$field}";

	$sql = "SELECT t.table_name AS table_name
FROM information_schema.tables AS t
INNER JOIN information_schema.columns c ON t.table_name = c.table_name
WHERE c.column_name = 'delta'
AND t.table_name = '$src_table'";

	$has_delta = mysql_result(mysql_query($sql),0);

	$delta = $has_delta ? 'c.delta' : '0';

	echo "Data: ";
	$sql = "INSERT INTO {$new_prefix}field_data_$field 
	SELECT 'node', CONCAT('dshed_',n.type), 0, c.nid+$nid_offset, c.nid+$nid_offset, 'und', $delta, c.{$field}_nid+$nid_offset
	FROM $old_db.{$src_table} c LEFT JOIN $old_db.{$old_prefix}node n ON c.nid = n.nid
	WHERE c.{$field}_nid > 0 AND c.{$field}_nid IS NOT NULL;";

	echo "\n$sql\n";

	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done\n";}

	echo "Revisions: ";
	$sql = "INSERT INTO {$new_prefix}field_revision_$field 
	SELECT 'node', CONCAT('dshed_',n.type), 0, c.nid+$nid_offset, c.nid+$nid_offset, 'und', $delta, c.{$field}_nid+$nid_offset
	FROM $old_db.{$src_table} c LEFT JOIN $old_db.{$old_prefix}node n ON c.nid = n.nid
	WHERE c.{$field}_nid > 0 AND c.{$field}_nid IS NOT NULL;";

	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done\n";}
	
	$sql = "DELETE FROM {$new_prefix}field_data_$field WHERE entity_type = 'node' AND bundle NOT IN (SELECT DISTINCT type FROM {$new_prefix}node);";
	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done\n";}

}

// fields only in one type
$fields = array();

foreach ($fields as $type => $field){

	echo "\nImport Node References for $field\n";
	echo "Data: ";
	$sql = "
	INSERT INTO {$new_prefix}field_data_$field 
	SELECT 'node', '$type', 0, nid, vid, 'und', 0, {$field}_nid
	FROM $old_db.{$old_prefix}content_type_{$type} c 
	WHERE {$field}_nid > 0 AND {$field}_nid IS NOT NULL;
	";
	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done\n";}
	echo "Revisions: ";
	$sql = "
	INSERT INTO {$new_prefix}field_revision_$field 
	SELECT 'node', '$type', 0, nid, vid, 'und', 0, {$field}_nid
	FROM $old_db.{$old_prefix}content_type_{$type} c 
	WHERE {$field}_nid > 0 AND {$field}_nid IS NOT NULL;
	";
	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done\n";}
	

}
