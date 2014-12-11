<?php

// imports quiz shuiz

echo "\n\n" . __FILE__ . "\n\n";

$sql = "SHOW TABLES LIKE 'quiz_%';";

$result = mysql_query($sql);

$other_schema = array(
'scool' => 'scool_d7',
'scool_d7' => 'scool',
);
while($row = mysql_fetch_array($result)){

	$diff = array();

	$table = $row[0];
	
	$diff_result = mysql_query("SELECT table_schema, table_name, column_name,ordinal_position,data_type,column_type FROM
	(
		SELECT
			table_schema,table_name, column_name,ordinal_position,
			data_type,column_type,COUNT(1) rowcount
		FROM information_schema.columns
		WHERE
		(
			(table_schema='scool' AND table_name='$table') OR
			(table_schema='scool_d7' AND table_name='$table')
		)
		AND table_name = '$table'
		GROUP BY
			column_name,ordinal_position,
			data_type,column_type
		HAVING COUNT(1)=1
	) A;");
	
	echo "\n$table\n";
	while($diff_row = mysql_fetch_array($diff_result)){
		$diff[$diff_row['table_name']][$diff_row['column_name']][$diff_row['table_schema']] = $diff_row['column_type'];
		if(isset($diff[$diff_row['table_name']][$diff_row['column_name']][$other_schema[$diff_row['table_schema']]])){
			$diff[$diff_row['table_name']][$diff_row['column_name']][$other_schema[$diff_row['table_schema']]] = "NONE";
		}
		
	}
	print_r($diff);
	echo "\n";

}