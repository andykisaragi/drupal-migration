<?php


$sql = "SELECT nid FROM node";

echo "getting nids\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){
	
	echo $row['nid'] . " ";

	/*$count_sql = "SELECT COUNT(cid) FROM comment WHERE nid = " . $row['nid'] . ";";
	$count_result = mysql_query($count_sql);
	if(!$count_result) { echo mysql_error() . "\n"; }
	if ($count_row = mysql_fetch_array($count_result)){
		echo $count_row[0] . "\n";
		$update_sql = "UPDATE node_comment_statistics SET comment_count = " . $count_row[0] . " WHERE nid = " . $row['nid'] . ";";
		$update_result = mysql_query($update_sql);
		if(!$update_result) { echo $tid . " " . mysql_error() . "\n"; }
		
	}*/
	$cid_sql = "SELECT cid FROM comment WHERE nid = " . $row['nid'] . " ORDER BY cid DESC LIMIT 1;";
	$cid_result = mysql_query($cid_sql);
	if(!$cid_result) { echo mysql_error() . "\n"; }
	if ($cid_row = mysql_fetch_array($cid_result)){
		echo $cid_row[0] . "\n";
		$update_sql = "UPDATE node_comment_statistics SET cid = " . $cid_row[0] . " WHERE nid = " . $row['nid'] . ";";
		$update_result = mysql_query($update_sql);
		if(!$update_result) { echo $tid . " " . mysql_error() . "\n"; }
		
	}




}