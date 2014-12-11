<?php

echo "\n\n" . __FILE__ . "\n\n";

$tables = array('nodequeue_nodes','nodequeue_queue','nodequeue_roles','nodequeue_subqueue','nodequeue_types');

foreach ($tables as $table){

	$sql = "DELETE FROM {$new_db}.{$new_prefix}{$table} WHERE qid IN (SELECT DISTINCT qid FROM {$old_db}.{$old_prefix}{$table})";
	echo "table: $table\n";
	echo "deleting...";
	$result = mysql_query($sql);
	if(!$result) { 
		echo $sql . "\n" . mysql_error() . "\n"; 
	}else{
		echo"...done\n";
	}

	$sql = "INSERT INTO {$new_db}.{$new_prefix}{$table} SELECT * FROM {$old_db}.{$old_prefix}{$table}";
	echo "inserting...";
	$result = mysql_query($sql);
	if(!$result) { 
		echo $sql . "\n" . mysql_error() . "\n"; 
	}else{
		echo"...done\n";
	}
}