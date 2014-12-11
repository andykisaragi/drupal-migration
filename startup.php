<?php


echo "\n\n" . __FILE__ . "\n\n";




/*$sql = "SELECT nid FROM $new_db.{$new_prefix}node ORDER BY nid DESC LIMIT 1";
$nid_offset = mysql_result(mysql_query($sql),0);
echo 'nid offset : ' . print_r($nid_offset,true);*/

$sql = "SELECT vid FROM $new_db.{$new_prefix}node ORDER BY vid DESC LIMIT 1";
$vid_offset = mysql_result(mysql_query($sql),0);
echo 'vid offset : ' . print_r($vid_offset,true);

define(NID_OFFSET,$vid_offset);
variable_set('andy_migration_nid_offset',NID_OFFSET);

$sql = "SELECT fid FROM $new_db.{$new_prefix}file_managed ORDER BY fid DESC LIMIT 1";
$fid_offset = mysql_result(mysql_query($sql),0);
echo 'fid offset : ' . print_r($fid_offset,true);

define(FID_OFFSET,$fid_offset);

variable_set('andy_migration_fid_offset',FID_OFFSET);
/*
echo "\nalter old db for convenience\n";
$sql = "ALTER TABLE $old_db.{$old_prefix}content_type_profile ADD uid INT AFTER field_published_profile_value";

$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

$sql = "create index uid_index on $old_db.{$old_prefix}content_type_profile(uid)";
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

echo "\nlinking on uid\n";
$sql = "update $old_db.{$old_prefix}content_type_profile p, $old_db.{$old_prefix}node n set p.uid = n.uid where p.nid = n.nid";
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }*/

/*
echo "deleting old users";
$sql = "delete p, u from 
        $old_db.{$old_prefix}content_type_user_profile p, $old_db.{$old_prefix}users u 
        where 
        u.uid = p.uid and 
        u.uid not in (0,1) and 
        u.access < $access_cutoff ";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }*/






