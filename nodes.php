<?php

// imports nodes

echo "\n\n" . __FILE__ . "\n\n";

/*
$types = array(

	'page' => 'page',
	'twitterfeed' => 'twitterfeed',
	);


foreach($types as $old_type => $new_type){

	_migrate_base_node($new_type,$old_type,$new_db,$old_db,$new_prefix,$old_prefix);

}*/

$sql = "SELECT DISTINCT type FROM $new_db.{$new_prefix}node_type WHERE type NOT IN ($skip_types)";

$result = mysql_query($sql);


while($row = mysql_fetch_array($result)){

	_migrate_base_node($row['type'],$row['type'],$new_db,$old_db,$new_prefix,$old_prefix);
	
}