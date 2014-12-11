<?php

// imports custom blocks

echo "\n\n" . __FILE__ . "\n\n";

// block custom / boxes

echo "\nImport Custom Blocks (boxes)\n";
$sql = <<<EOS
INSERT INTO {$new_prefix}block_custom 
SELECT b.bid, b.body, b.info, f.format FROM 
{$old_db}.{$old_prefix}boxes b 
LEFT JOIN {$old_db}.{$old_prefix}filter_formats ff ON ff.format = b.format 
LEFT JOIN {$new_db}.{$new_prefix}filter_format f on ff.name = f.name
EOS;
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

// blocks
// bid, module, delta, theme, status, weight, region, custom, visibility, pages, title, cache

echo "\nImport Custom Block Instances\n";
/*
$sql = "SELECT DISTINCT theme FROM {$new_prefix}block";
$themes = array();
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }
while($row = mysql_fetch_array($result)){
	$themes[] = $row['theme'];
}*/

$sql = "SELECT bid FROM {$new_prefix}block_custom";

$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }
while($row = mysql_fetch_array($result)){

	$delta = $row['bid'];

	$instance_sql = "SELECT * FROM {$old_db}.{$old_prefix}blocks WHERE module = 'block' AND delta = $delta";
	$instance_result = mysql_query($instance_sql);
	if(!$instance_result) { echo $instance_sql . "\n" . mysql_error() . "\n"; }
	while($instance_row = mysql_fetch_array($instance_result)){

		$theme = $instance_row['theme'] ? "'" . $instance_row['theme'] . "'" : 'DEFAULT';;
		$status = $instance_row['status'] ? "'" . $instance_row['status'] . "'" : 'DEFAULT';;
		$weight = $instance_row['weight'] ? "'" . $instance_row['weight'] . "'" : 'DEFAULT';;
		$region = $instance_row['region'] ? "'" . $instance_row['region'] . "'" : 'DEFAULT';;
		$custom = $instance_row['custom'] ? "'" . $instance_row['custom'] . "'" : 'DEFAULT';;
		$visibility = $instance_row['visibility'] ? "'" . $instance_row['visibility'] . "'" : 'DEFAULT';;
		$pages = $instance_row['pages'] ? "'" . $instance_row['pages'] . "'" : 'DEFAULT';
		$title = $instance_row['title'] ? "'" . $instance_row['title'] . "'" : 'DEFAULT';;
		$cache = $instance_row['cache'] ? "'" . $instance_row['cache'] . "'" : 'DEFAULT';;
		$insert_sql = "INSERT INTO {$new_prefix}block (module, delta, theme, status, weight, region, custom, visibility, pages, title, cache) 
			VALUES ('block',$delta, $theme,$status,$weight,$region,$custom,$visibility,$pages,$title,$cache)";


		$insert_result = mysql_query($insert_sql);
		if(!$insert_result) { echo $insert_sql . "\n" . mysql_error() . "\n"; break; }

	}

}
