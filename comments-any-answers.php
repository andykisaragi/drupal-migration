<?php

echo "\n\n" . __FILE__ . "\n\n";

$sql = <<<EOS
insert into $new_db.comment
select nc.cid, nc.pid, nc.nid, n.uid, n.title, nc.hostname, n.created, n.changed, n.status, nc.thread, u.name, '','','' 
from $old_db.node_comments nc 
left join $old_db.node n on nc.cid = n.nid 
left join $old_db.users u on u.uid = n.uid
where n.status = 1
EOS;

echo "populating comment table...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

// comment body field @todo: bundle field logic isnt right, needs to know abuot the parent type. write an update. maybe mod the table first? also could delete all nodes that dont have a status=1 parent node 

/*

// No

$cases = 'CASE n.type ';
foreach($content_types as $key => $value) {
  $cases .= "WHEN '$key' THEN 'comment_node_$value' ";
}
$cases .= 'END';
*/

$sql = <<<EOS
insert into $new_db.field_data_comment_body 
select 'comment', 'dummy', 0, nc.cid, nc.cid, 'und', 0, nr.body, 'filtered_html'
from $old_db.node_comments nc 
left join $old_db.node_revisions nr on nc.cid = nr.nid 
EOS;

echo "populating field_data_comment_body table...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

//and its revision @todo: see last query

$sql = <<<EOS
insert into $new_db.field_revision_comment_body 
select 'comment', 'dummy', 0, nc.cid, nc.cid, 'und', 0, nr.body, 'filtered_html'
from $old_db.node_comments nc 
left join $old_db.node_revisions nr on nc.cid = nr.nid 
EOS;

echo "populating field_revision_comment_body table...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

// fix bundle column values

$sql = <<<EOS
update node n, comment c, field_data_comment_body f, field_revision_comment_body fr
set f.bundle = concat('comment_node_',n.type), fr.bundle = concat('comment_node_',n.type) 
where n.nid = c.nid and c.cid = f.entity_id and f.entity_id = fr.entity_id;
EOS;

echo "fixing bundle column values...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";


// node comment statistics @todo: 2nd column in the select need to be an actual cid of the most recent comment

$content_types = array('anyanswers_question');

$comment_stats_in = implode("','",array_keys($content_types));

$sql = <<<EOS
insert into $new_db.node_comment_statistics 
select ns.nid, NULL, ns.last_comment_timestamp, '', ns.last_comment_uid, ns.comment_count
from $old_db.node_comment_statistics ns, $old_db.node n 
where ns.nid = n.nid
EOS;

echo "populating node_comment_statistics table...\n";

$result = mysql_query($sql);
echo $sql . "\n";
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

/*
$sql = "update comment set uid = 1";
echo "setting comments to uid 1 for testingz...\n";

$result = mysql_query($sql);
echo $sql . "\n";
if(!$result) { echo mysql_error() . "\n"; }*/
