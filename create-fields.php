<?php

/*
The following fields couldn't be recreated: field_tip_taxonomy, field_university_logo, field_university2_location, field_uni_location_location, field_uni_location_institution, field_course_institution, field_course_location
*/	

echo "\n\n" . __FILE__ . "\n\n";

$sql = "SELECT f.field_name, f.type, f.required, f.multiple, f.module, fi.type_name, fi.label, fi.widget_type FROM $old_db.{$old_prefix}content_node_field f
	LEFT JOIN $old_db.{$old_prefix}content_node_field_instance fi ON f.field_name = fi.field_name 
	WHERE fi.type_name IN (SELECT DISTINCT type FROM $new_db.{$new_prefix}node)";

echo "\n$sql\n";

$result = mysql_query($sql);

module_load_include('inc', 'field_ui', 'field_ui.admin');
$field_type_options = field_ui_field_type_options();
$field_types = array_keys($field_type_options);

print_r($field_types);

$deleting = false;

while ($row = mysql_fetch_array($result)){
	$settings = array();
	$type = $row['type'];
	echo "type " . $type . " / widget " . $row['widget_type'] . "\n";
	if($type == 'text' && $row['widget_type'] == 'text_textarea') $type = 'text_long';
	if($type == 'number_integer' && $row['widget_type'] == 'optionwidgets_onoff'){
		$type = 'list_boolean';
	}

	$name = $row['field_name'];
	$bundle = $row['type_name'];
	if(in_array($type,$field_types)){
		//echo "$type is valid\n";

	    $exist_sql = "SELECT id FROM $new_db.{$new_prefix}field_config WHERE field_name='$name'";
	    //echo "$exist_sql\n";
	    $exists = mysql_result(mysql_query($exist_sql),0);
	    //echo "exists: $exists\n";
	    if(!$exists){
	        $field = array(
	          'field_name' => $name,
	          'type' => $type,
	          'settings' => $settings;
	        );
	        field_create_field($field);
	        $config_done[$name] = true;
	        echo "creating field $name \n";
	    }

        $exist_sql = "SELECT id FROM $new_db.{$new_prefix}field_config_instance WHERE field_name='$name' AND bundle='$bundle'";
        $exists = mysql_result(mysql_query($exist_sql),0);

        $instance = array(
            'field_name' => $name,
            'entity_type' => 'node',
            'label' => $row['label'],
            'bundle' => $bundle,
            'required' => false,

        );

        if(!$exists && !$deleting){
	        echo "creating instance of field $name for bundle $bundle\n";
	        field_create_instance($instance);
	    }
	    if($exists && $deleting){
	        echo "deleting instance of field $name for bundle $bundle\n";
	        field_delete_instance($instance,true);
	    }

	}else{
		$bad_fields[] = $name;
	}



}

if(!empty($bad_fields)){
	$bad_fields = implode(", ",$bad_fields);
	echo "The following fields couldn't be recreated: $bad_fields\n\n";
}

if($deleting){
	mysql_query("SET SESSION group_concat_max_len = 1000000;");
	$sql = mysql_result(mysql_query("SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) 
    AS statement FROM information_schema.tables 
    WHERE table_schema = '$new_db' AND (
	 table_name LIKE '{$new_prefix}field_deleted_%' 
	 );"),0);
	mysql_query($sql);
	mysql_query("DELETE FROM $new_db.{$new_prefix}field_config WHERE deleted = 1;");
	mysql_query("DELETE FROM $new_db.{$new_prefix}field_config_instance WHERE deleted = 1;");
}