<?php

// imports content types

echo "\n\n" . __FILE__ . "\n\n";

// node type
// type, name, base, module, description, help, has_title, title_label, custom, modified, locked, disabled, orig_type



// !!! check 'base'

echo "\nImport Content Types\n";
$sql = <<<EOS
INSERT INTO {$new_prefix}node_type 
SELECT DISTINCT type, name, 'node_content', module, description, help, has_title, title_label, 1, modified, locked, 0, orig_type
FROM $old_db.{$old_prefix}node_type
WHERE type NOT IN (SELECT type FROM {$new_prefix}node_type) AND type IN (SELECT DISTINCT type FROM $old_db.{$old_prefix}node)
AND type NOT IN ($skip_types);
EOS;
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

// fields.
// use content_node_field & content_node_field_instance to do this...?
// - actually no - most of them can be done with CCK migration module (with some changes)

$sql = "SELECT id FROM {$new_prefix}field_config WHERE field_name = 'body'";
$body_field_id = mysql_result(mysql_query($sql),0);
if(!$body_field_id) { echo $sql . "\n" . mysql_error() . "\n"; }

echo "\nBody field id: $body_field_id\n";

// something about user profile in this data blob, may need to change?
$body_data_blob = 'a:7:{s:5:"label";s:4:"Body";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"weight";i:0;s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"display_summary";b:1;s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:8:"settings";a:0:{}s:6:"module";s:4:"text";s:6:"weight";i:0;}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:23:"text_summary_or_trimmed";s:8:"settings";a:1:{s:11:"trim_length";i:600;}s:6:"module";s:4:"text";s:6:"weight";i:0;}}s:8:"required";b:0;s:11:"description";s:0:"";s:13:"default_value";N;}';
$result = mysql_query("SELECT type FROM {$new_prefix}node_type");
echo "\nAdd body field to all types\n";
while($row = mysql_fetch_array($result)){

	$types[] = $type = $row['type'];

		// body field

	echo $row['type'];

	$exists_sql = "SELECT id FROM {$new_prefix}field_config_instance WHERE field_name='body' AND entity_type='node' AND bundle='$type'";

	$exists_result = mysql_query($exists_sql);

	if($exists_row = mysql_fetch_array($exists_result)){

		echo " - field instance exists already \n";

	}else{
		$body_sql = "INSERT INTO {$new_prefix}field_config_instance (field_id, field_name, entity_type, bundle, data, deleted) VALUES ($body_field_id, 'body','node', '$type','$body_data_blob',0)";
		$body_result = mysql_query($body_sql);
		if(!$body_result) { 
			echo " - failed\n" . $body_sql . "\n" . mysql_error() . "\n"; 
		}else{
			echo " - field instance added  \n";
		}
	}


}



