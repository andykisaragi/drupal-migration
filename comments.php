<?php

echo "\n\n" . __FILE__ . "\n\n";

$content_types = array(
  'blog_post' => 'blog',
  'article' => 'article',
  'group_post' => 'group_post',
  //'anyanswers_question' => 'any_answers',
);

$sql = <<<EOS
insert into $new_db.{$new_prefix}comment
select nc.cid, nc.pid, nc.nid, nc.uid, nc.subject, nc.hostname, n.created, n.changed, nc.status, nc.thread, nc.name, nc.mail, nc.homepage, ''
from $old_db.{$old_prefix}comments nc 
left join $old_db.{$old_prefix}node n on nc.nid = n.nid 
left join $old_db.{$old_prefix}users u on u.uid = nc.uid
where n.status = 1 and nc.status = 0
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
insert into $new_db.{$new_prefix}field_data_comment_body 
select 'comment', 'dummy', 0, nc.cid, nc.cid, 'und', 0, nc.comment, 'filtered_html' 
from $old_db.{$old_prefix}comments nc, $old_db.{$old_prefix}node n
where nc.nid = n.nid and n.status = 1 
EOS;

echo "populating field_data_comment_body table...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

//and its revision @todo: see last query

$sql = <<<EOS
insert into $new_db.{$new_prefix}field_revision_comment_body 
select 'comment', 'dummy', 0, nc.cid, nc.cid, 'und', 0, nc.comment, 'filtered_html' 
from $old_db.{$old_prefix}comments nc, $old_db.{$old_prefix}node n
where nc.nid = n.nid and n.status = 1 
EOS;

echo "populating field_revision_comment_body table...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";

// fix bundle column values

$sql = <<<EOS
update {$new_db}.{$new_prefix}node n, {$new_db}.{$new_prefix}comment c, {$new_db}.{$new_prefix}field_data_comment_body f, {$new_db}.{$new_prefix}field_revision_comment_body fr
set f.bundle = concat('comment_node_',n.type), fr.bundle = concat('comment_node_',n.type) 
where n.nid = c.nid and c.cid = f.entity_id and f.entity_id = fr.entity_id;
EOS;

echo "fixing bundle column values...\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";


// node comment statistics @todo: 2nd column in the select need to be an actual cid of the most recent comment

$comment_stats_in = implode("','",array_keys($content_types));

mysql_query("TRUNCATE TABLE $new_db.{$new_prefix}node_comment_statistics;");

$sql = <<<EOS
insert into $new_db.{$new_prefix}node_comment_statistics 
select ns.nid, NULL, ns.last_comment_timestamp, '', ns.last_comment_uid, ns.comment_count
from $old_db.{$old_prefix}node_comment_statistics ns, $old_db.{$old_prefix}node n 
where ns.nid = n.nid 
EOS;

echo "populating node_comment_statistics table...\n$sql\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";
