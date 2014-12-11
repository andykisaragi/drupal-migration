<?php

echo "\n\n" . __FILE__ . "\n\n";


$sql = "SELECT tid FROM $new_db.{$new_prefix}taxonomy_term_data WHERE vid = 5 AND name = 'DShed'";

$tid = mysql_result(mysql_query($sql),0);

if($tid){

	$sql = "INSERT INTO $new_db.{$new_prefix}taxonomy_index 
		SELECT nid, $tid, 0, UNIX_TIMESTAMP() FROM $new_db.{$new_prefix}node WHERE nid > " . NID_OFFSET . ";";

	echo "\n Tagging all new nodes with DShed space (tid $tid ) in taxonomy_index \n $sql \n";

	$result = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n"; } else { echo "done....\n\n"; }
	// vid => field_name
	$fields = array('field_space' => 5,'watershed_primary_space' => 5);

	// populates taxonomy index & fields

	// joe's magic query which does *something*

	$sql = "
	select 'node', n.type, 0, n.nid, n.nid, 'und',
	CASE 
	        WHEN COALESCE(@rownum, 0) = 0 
	        THEN @rownum:=c-1 
	        ELSE @rownum:=@rownum-1 
	    END as delta,
	t.tid
	from 
	{$new_prefix}node n, {$new_prefix}taxonomy_index t, (select nid, count(*) as c from {$new_prefix}taxonomy_index group by nid) as test, {$new_prefix}taxonomy_term_data d
	where t.nid = n.nid and n.nid = test.nid and t.tid = d.tid and d.vid = 5;
	";
	$result = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n"; } else { echo "dumm query\n\n"; }

	foreach ($fields as $field => $vid){

		echo "$field\n";

		$offset = NID_OFFSET;
		// field for Spaces

		$sql = "INSERT INTO {$new_prefix}field_data_$field
		SELECT 'node', n.type, 0, n.nid, n.nid, 'und', 0, ti.tid 
		FROM wsd7_node n 
		LEFT JOIN wsd7_taxonomy_index ti ON n.nid = ti.nid 
		LEFT JOIN wsd7_taxonomy_term_data td ON td.tid = ti.tid 
		WHERE n.nid > $offset AND td.vid = $vid;
		";
		echo "\n $sql \n";
		$result = mysql_query($sql);
		if(!$result) { echo mysql_error() . "\n"; }
		$sql = "INSERT INTO {$new_prefix}field_revision_$field
		SELECT 'node', n.type, 0, n.nid, n.nid, 'und', 0, ti.tid 
		FROM wsd7_node n 
		LEFT JOIN wsd7_taxonomy_index ti ON n.nid = ti.nid 
		LEFT JOIN wsd7_taxonomy_term_data td ON td.tid = ti.tid 
		WHERE n.nid > $offset AND td.vid = $vid;
		";
		$result = mysql_query($sql);
		if(!$result) { echo mysql_error() . "\n"; }


	}




}



