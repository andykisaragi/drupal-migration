<?php

echo "\n\n" . __FILE__ . "\n\n";



echo "Migrating from Taxonomy Nodes to Term entities:\n";

echo "Body:  ";
echo "Data: ";
$sql = "
INSERT INTO {$new_prefix}field_data_field_body_text 
SELECT 'taxonomy_term', 'vocabulary_9', 0, tn.tid, tn.tid, 'und', 0, nr.body, nr.teaser, 'filtered_html' 
FROM $old_db.{$old_prefix}taxonomynode tn LEFT JOIN $old_db.{$old_prefix}node_revisions nr ON tn.nid = nr.nid
";
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done... ";}
echo "Revisions: ";
$sql = "
INSERT INTO {$new_prefix}field_revision_field_body_text 
SELECT 'taxonomy_term', 'vocabulary_9', 0, tn.tid, tn.tid, 'und', 0, nr.body, nr.teaser, 'filtered_html' 
FROM $old_db.{$old_prefix}taxonomynode tn LEFT JOIN $old_db.{$old_prefix}node_revisions nr ON tn.nid = nr.nid
";
$result = mysql_query($sql);
if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done\n\n";}



echo "Overview:  ";
echo "Data: ";
$sql = "
INSERT INTO {$new_prefix}field_data_field_overview
SELECT 'taxonomy_term', 'vocabulary_9', 0, tn.tid, tn.tid, 'und', 0, f.field_overview_value, 'filtered_html' 
FROM $old_db.{$old_prefix}taxonomynode tn LEFT JOIN $old_db.{$old_prefix}content_field_overview f ON tn.nid = f.nid

";
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done... ";}
echo "Revisions: ";
$sql = "
INSERT INTO {$new_prefix}field_revision_field_overview
SELECT 'taxonomy_term', 'vocabulary_9', 0, tn.tid, tn.tid, 'und', 0, f.field_overview_value, 'filtered_html' 
FROM $old_db.{$old_prefix}taxonomynode tn LEFT JOIN $old_db.{$old_prefix}content_field_overview f ON tn.nid = f.nid
";
$result = mysql_query($sql);
if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done\n\n";}



echo "Keyimage:  ";

$field = 'field_keyimage';
$data_items = array("fid","alt","description","width","height");

$sql = "SELECT tn.tid, f.field_keyimage_fid, f.field_keyimage_data FROM $old_db.{$old_prefix}taxonomynode tn LEFT JOIN $old_db.{$old_prefix}content_field_keyimage f ON tn.nid = f.nid";

echo "$sql\n";

$result = mysql_query($sql);

while($row = mysql_fetch_array($result)){
	extract($row);
	echo " $tid . ";
	$data = unserialize($row[$field . "_data"]);
	$insert_fields = array();
	foreach ($data_items as $suffix){
		if(isset($row[$field . "_" . $suffix])){
			$value = $row[$field . "_" . $suffix];
			$insert_fields[] = "'" . mysql_escape_string($value) . "'";	
		}else if(isset($data[$suffix])){
			$insert_fields[] = "'" . mysql_escape_string($data[$suffix]) . "'";	
		}else{
			$insert_fields[] = "DEFAULT";
		}
	}    
	$insert_fields = implode(", ",$insert_fields);
	$sql = "INSERT INTO {$new_prefix}field_data_field_keyimage VALUES ('taxonomy_term', 'vocabulary_9', 0, $tid, $tid, 'und', 0, $insert_fields);";
	//echo $sql . "\n";
	$res = mysql_query($sql);
	if(!$res) { echo $sql . "\n" . mysql_error() . "\n"; }
	$sql = "INSERT INTO {$new_prefix}field_revision_field_keyimage VALUES ('taxonomy_term', 'vocabulary_9', 0, $tid, $tid, 'und', 0, $insert_fields);";
	$res = mysql_query($sql);
	if(!$res) { echo "\n" . mysql_error() . "\n"; }

	if($main_suffix == 'fid'){
		$fid = $row[$field . '_fid'];
		$sql = "INSERT INTO {$new_prefix}file_usage VALUES ($fid, 'file', 'taxonomy_term', $tid,1);";
		$res = mysql_query($sql);
		if(!$res) { echo "\n" . mysql_error() . "\n"; }	
	}

}