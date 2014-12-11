<?php

echo "\n\n" . __FILE__ . "\n\n";

$truncate = false;

/*
 * Single-instance fields
 */

//@todo, one day - automate creation of these arrays
$types = array(

);


foreach ($types as $type => $fields){
	foreach ($fields as $field => $suffixes){

		echo "$field \n";

		$main_suffix = $suffixes[0];

		if($truncate){
			echo "Truncating first...\n";
			$sql = "TRUNCATE TABLE {$new_prefix}field_data_$field;";		
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }
			$sql = "TRUNCATE TABLE {$new_prefix}field_revision_$field;";		
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }
		}

		$sql = "SELECT * FROM $old_db.{$old_prefix}content_type_{$type} WHERE {$field}_{$main_suffix} IS NOT NULL;";
		echo "$sql\n";
		$rows_result = mysql_query($sql);
		while($row = mysql_fetch_array($rows_result)){
			$data = unserialize($row[$field . "_data"]);
			$insert_fields = array();
			foreach ($suffixes as $suffix){
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

			$nid = $row['nid'];
			$vid = $row['vid'];	
			$sql = "INSERT INTO {$new_prefix}field_data_$field VALUES ('node', '$type', 0, $nid, $vid, 'und', 0, $insert_fields);";
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }
			$sql = "INSERT INTO {$new_prefix}field_revision_$field VALUES ('node', '$type', 0, $nid, $vid, 'und', 0, $insert_fields);";
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }	

			if($main_suffix == 'fid'){
				$fid = $row[$field . '_fid'];
				$sql = "INSERT INTO {$new_prefix}file_usage VALUES ($fid, 'file', 'node', $nid,1);";
				$result = mysql_query($sql);
				if(!$result) { echo "\n" . mysql_error() . "\n"; }	
			}

		}

		echo " ...done\n\n";


	}

}


$fields = array(
	"field_image" => array("fid","alt","description","width","height"),
);
$field_mappings = array(
	"field_image" => array(
		"dshed_article" => "field_keyimage",
		"dshed_featured_item" => "field_keyimage",
		"dshed_item_set" => "field_keyimage",
		"dshed_project" => "field_keyimage",
		),
	);

foreach ($fields as $field => $suffixes){

	echo "$field \n";

	$main_suffix = $suffixes[0];

	if($truncate){
		echo "Truncating first...\n";
		$sql = "TRUNCATE TABLE {$new_prefix}field_data_$field;";		
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }
		$sql = "TRUNCATE TABLE {$new_prefix}field_revision_$field;";		
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }
	}

	$sql = "SELECT c.*, CONCAT('dshed_',n.type) AS type FROM $old_db.{$old_prefix}content_{$field} c LEFT JOIN $old_db.{$old_prefix}node n ON c.nid = n.nid WHERE {$field}_{$main_suffix} IS NOT NULL;";
	echo "$sql\n";
	$rows_result = mysql_query($sql);
	while($row = mysql_fetch_array($rows_result)){

		//echo $row['type'] . " " . $row['nid']. " " . $row['field_image_fid'] . "\n";

		$nid = $vid = $row['nid'] + NID_OFFSET;

		$type = $row['type'];	
		$data = unserialize($row[$field . "_data"]);
		if(!$data){
			// annoying hack time because of badly encoded shiraz in tha databizzle
			preg_match('/"width";i:([0-9]+)/',$row[$field . "_data"],$width);
			preg_match('/"height";i:([0-9]+)/',$row[$field . "_data"],$height);
			preg_match('/"alt";s:[0-9]+:("(\w| |,|\.)+")/',$row[$field . "_data"],$alt);
			$data = array();
			$data['width'] = $width[1];
			$data['height'] = $height[1];
			$data['alt'] = str_replace('"','',$alt[1]);
			$data['description'] = str_replace('"','',$alt[1]);
		}
		$insert_fields = array();
		foreach ($suffixes as $suffix){
			if(isset($row[$field . "_" . $suffix])){
				$value = $row[$field . "_" . $suffix];
				if($suffix == 'fid') $value += FID_OFFSET;
				$insert_fields[] = "'" . mysql_escape_string($value) . "'";	
			}else if(isset($data[$suffix])){
				$insert_fields[] = "'" . mysql_escape_string($data[$suffix]) . "'";	
			}else{
				$insert_fields[] = "DEFAULT";
			}
		}    

		$insert_fields = implode(", ",$insert_fields);

		$target_field = isset($field_mappings[$field][$type]) ? $field_mappings[$field][$type] : $field;

		$delta = $row['delta'] ? $row['delta'] : 0;	
		$sql = "INSERT INTO {$new_prefix}field_data_$target_field VALUES ('node', '$type', 0, $nid, $vid, 'und', $delta, $insert_fields);";
		
		$result = mysql_query($sql);
		if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; die();}
		
		$sql = "INSERT INTO {$new_prefix}field_revision_$target_field VALUES ('node', '$type', 0, $nid, $vid, 'und', $delta, $insert_fields);";
		
		$result = mysql_query($sql);
		if(!$result) { echo "\n" . mysql_error() . "\n"; }

		if($main_suffix == 'fid'){
			$fid = $row[$field . '_fid'] + FID_OFFSET;
			$sql = "INSERT INTO {$new_prefix}file_usage VALUES ($fid, 'file', 'node', $nid,1);";
			$result = mysql_query($sql);
			if(!$result) { echo "\n" . mysql_error() . "\n"; }	
		}
	}


}


// UPDATE `wsd7_field_revision_field_keyimage` SET field_keyimage_title = field_keyimage_alt WHERE (field_keyimage_title IS NULL OR field_keyimage_title = '')



