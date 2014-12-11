<?php

echo "\n\n" . __FILE__ . "\n\n";

$truncate = true;

function select_fields($field, $suffixes){
	if(!is_array($suffixes)) return $field . "_" . $suffixes;
	$select_fields = array();
	foreach ($suffixes as $suffix){
		if ($suffix){
			$select_fields[] = "c." . $field . "_" . $suffix;
		}else{
			$select_fields[] = "NULL";
		}
	}
	$fields = implode(",",$select_fields);
	return $fields;
}

$nid_offset = 0;

$suffixes = array(
	'date' => array("value","value2"),
	'number_integer' => "value",
	'list_boolean' => "value",
	'text' => array("value",null),
	'text_long' => array("value",null), 
	'text_with_summary' => array("value",null), 
);

$sql = "SELECT fci.*, fc.type as field_type FROM $new_db.{$new_prefix}field_config_instance fci 
LEFT JOIN $new_db.{$new_prefix}field_config fc ON fc.id = fci.field_id
WHERE fci.bundle NOT LIKE 'comment%' AND fc.type <> 'taxonomy_term_reference'";
$result = mysql_query($sql);
if(!$result) { echo "\n" . mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){

	extract ($row);

	if($field_name != 'body'){
		//echo "looking for $field_name\n";

		$exist_sql = "SELECT table_name FROM information_schema.tables 
	    WHERE table_schema = '$old_db' AND (
		 table_name = 'content_{$field_name}' 
		 );";

		$exists = mysql_result(mysql_query($exist_sql),0);

		//echo "$field_type \n";

		if($exists){
			$multi_fields[$field_name] = $suffixes[$field_type];
		}else{
			$single_fields[$bundle][$field_name] = $suffixes[$field_type];
		}
	}

}


/*
 * Single-instance fields
 */

//@todo, one day - automate creation of these arrays
// also, could have query determining if the field-specific table exists or not,
// then this could all be one big array.
// can probably merge the file-fields and references bits into one bit of code too.
$types = array(
	"event" => array(
		"field_event_subject" => array("value",null),
		"field_event_subject_desc" => array("value",null),
		"field_event_date" => array("value","value2"),
	),

);


foreach ($single_fields as $type => $fields){
	foreach ($fields as $field => $suffix){

		echo "$field \n";

		$select_field = is_array($suffix) ? select_fields($field, $suffix) : $field . "_" . $suffix;

		if(is_array($suffix)){
			$select_field = select_fields($field, $suffix);
			$suffix = $suffix[0];
		}else{
			$select_field = $field . "_" . $suffix;

		}

		if($truncate){
			echo "Truncating first...\n";
			$sql = "TRUNCATE TABLE {$new_prefix}field_data_$field;";		
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }
			$sql = "TRUNCATE TABLE {$new_prefix}field_revision_$field;";		
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }
		}

		echo "Data: ";
		$sql = "INSERT INTO {$new_prefix}field_data_$field 
		SELECT 'node', 'dshed_$type', 0, nid+$nid_offset, nid+$nid_offset, 'und', 0, $select_field
		FROM $old_db.{$old_prefix}content_type_{$type} c 
		WHERE {$field}_{$suffix} IS NOT NULL;
		";

		//echo "\n\n$sql\n\n";

		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done... ";}
		echo "Revisions: ";
		$sql = "INSERT INTO {$new_prefix}field_revision_$field 
		SELECT 'node', 'dshed_$type', 0, nid+$nid_offset, nid+$nid_offset, 'und', 0, $select_field
		FROM $old_db.{$old_prefix}content_type_{$type} c 
		WHERE {$field}_{$suffix} IS NOT NULL;
		";
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done\n\n";}
	}

}

foreach ($multi_fields as $field => $suffix){

	echo "$field \n";

	$select_field = is_array($suffix) ? select_fields($field, $suffix) : $field . "_" . $suffix;

	if(is_array($suffix)){
		$select_field = select_fields($field, $suffix);
		$suffix = $suffix[0];
	}else{
		$select_field = $field . "_" . $suffix;

	}

	if($truncate){
		echo "Truncating first...\n";
		$sql = "TRUNCATE TABLE {$new_prefix}field_data_$field;";		
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }
		$sql = "TRUNCATE TABLE {$new_prefix}field_revision_$field;";		
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }
	}

	$delta_sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='{$old_prefix}content_{$field}' AND COLUMN_NAME='delta'";

	$result = mysql_query($delta_sql);

	if($row = mysql_fetch_array($result)){
		$delta = "c.delta";
	}else{
		$delta = 0;
	}

	//echo "$delta_sql | $delta\n";

	echo "Data: ";
	$sql = "
	INSERT INTO {$new_prefix}field_data_$field 
	SELECT 'node', CONCAT('dshed_',n.type), 0, c.nid+$nid_offset, c.nid+$nid_offset, 'und', $delta, $select_field
	FROM $old_db.{$old_prefix}content_{$field} c LEFT JOIN $old_db.{$old_prefix}node n ON c.nid = n.nid
	WHERE c.{$field}_{$suffix} IS NOT NULL;
	";

	//echo "\n\n$sql\n\n";

	$result = mysql_query($sql);
	if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }else{ echo " ...done... ";}
	echo "Revisions: ";
	$sql = "
	INSERT INTO {$new_prefix}field_revision_$field 
	SELECT 'node', CONCAT('dshed_',n.type), 0, c.nid+$nid_offset, c.nid+$nid_offset, 'und', $delta, $select_field
	FROM $old_db.{$old_prefix}content_{$field} c LEFT JOIN $old_db.{$old_prefix}node n ON c.nid = n.nid
	WHERE c.{$field}_{$suffix} IS NOT NULL;
	";
	$result = mysql_query($sql);
	if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done\n\n";}

}
