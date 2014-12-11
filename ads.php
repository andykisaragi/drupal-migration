<?php

// imports ads 

echo "\n\n" . __FILE__ . "\n\n";

$sql = "SHOW TABLES LIKE 'ad_%';";

$result = mysql_query($sql);

$other_schema = array(
'scool' => 'scool_d7',
'scool_d7' => 'scool',
);
while($row = mysql_fetch_array($result)){

	$table = $row[0];

	$import_sql = "INSERT INTO $new_db.{$new_prefix}{$table}
		SELECT * FROM $old_db.{$old_prefix}{$table}";

	echo "importing $table ... ";

	$import_result = mysql_query($import_sql);
	if(!$import_result) { echo $import_sql . "\n" . mysql_error() . "\n"; }
}

echo "\n\n";