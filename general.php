<?php

echo "\n\n" . __FILE__ . "\n\n";

//node counter

$sql = <<<EOS
insert into $new_db.node_counter 
select * from $old_db.node_counter 
EOS;

echo "node counter table entries...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";
  
$sql = "delete from node_counter where nid not in (select nid from node)";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }



//page title


$sql = "SELECT nid, page_title from $old_db.page_title";

echo "getting old page titles\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){

	$data = array('title' => array('value' => $row['page_title']));
	$data = serialize($data);
	$insert_sql = "INSERT INTO metatag (entity_type, entity_id, data) VALUES ('node'," . $row['nid'] . ",'" . $data . "');";
	$insert_result = mysql_query($insert_sql);
	//echo $row['nid'] . " ";
	//if(!$insert_result) { echo $tid . " " . mysql_error() . "\n"; }
	
}
  
$sql = "delete from metatag where entity_type ='node' and entity_id not in (select nid from node)"; 
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }









  
// un assign nodes

$sql = <<<EOS
update node n, node_revision nr 
set n.uid = 1, nr.uid = 1 
where 
n.nid = nr.nid and n.uid not in(select uid from users);
EOS;

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "cleanup for missing users";

//add taxonomy url aliases

$sql = <<<EOS
INSERT INTO url_alias (source, alias, language)
SELECT src, dst, 'und'
FROM $old_db.url_alias
WHERE src NOT LIKE 'node/%' AND src NOT LIKE 'user/%';
EOS;

// need to renumber term aliases

$sql = "SELECT src, dst 
FROM $old_db.url_alias
WHERE src NOT LIKE 'node/%' AND src NOT LIKE 'user/%';";

echo "getting old taxonomy url aliases\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){

	$path = explode("/",$row['src']);
	$tid = array_pop($path);
	$path[] = ((int) $tid) + 10000;
	$row['src'] = implode("/",$path);
	$insert_sql = "INSERT INTO url_alias (source, alias, language) VALUES ('" . $row['src'] . "','" . $row['dst'] . "','und');";
	$insert_result = mysql_query($insert_sql);
	if(!$insert_result) { echo $tid . " " . mysql_error() . "\n"; }
	

}

echo "cleanup for taxonomy url aliases";
