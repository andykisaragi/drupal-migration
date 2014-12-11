<?php

echo "\n\n" . __FILE__ . "\n\n";

// migrate user field

function _migrate_user_field($target_field_name,$source_field_name,$new_db,$old_db,$new_prefix,$old_prefix,$field_mappings = NULL) {


  echo "getting data from $source_field_name...\n";
  
  $sql_target1 = "insert into $new_db.{$new_prefix}field_data_field_$target_field_name \n";
  $sql_target2 = "insert into $new_db.{$new_prefix}field_revision_field_$target_field_name \n";
  
  if($field_mappings) {
    $cases = '';
    foreach($field_mappings as $key => $value) {
      $cases .= 'when ' . $key . ' then ' . $value . "\n";
    }
    $sql_select = "select 'user' ,'user' ,0, uid, uid, 'und', 0, CASE field_{$source_field_name}_value $cases END\n";
  } else {
    $sql_select = "select 'user', 'user', 0, uid, uid, 'und', 0, field_{$source_field_name}_value, ''\n";
  }
  
  $sql_from = "from $old_db.{$old_prefix}content_type_profile where uid > 1 and uid < 122363 group by uid;";
  
  $sql = $sql_target1 . $sql_select . $sql_from;
  
  echo $sql;
  
  $result = mysql_query($sql); if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }
  
  $sql = $sql_target2 . $sql_select . $sql_from;
  
  $result = mysql_query($sql); if(!$result) { echo mysql_error() . "\n"; }
  
  echo "...done\n";

}

function _migrate_user_body($target_field_name,$old_type,$new_db,$old_db,$new_prefix,$old_prefix){

  // body 'field'

  $sql = <<<EOS
  insert into $new_db.{$new_prefix}field_data_field_$target_field_name
  select 'user', 'user', 0, n.uid, n.uid, 'und', 0, nr.body,nr.teaser,'filtered_html'
  from $old_db.{$old_prefix}node_revisions nr, $old_db.{$old_prefix}node n
  where n.type = '$old_type' and n.vid = nr.vid
EOS;

  echo "body field table entries for users...\n";

  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

  // ...and its revision entry

  $sql = <<<EOS
  insert into $new_db.{$new_prefix}field_data_field_$target_field_name
  select 'node','$new_type',0,n.uid,n.uid,'und',0,nr.body,nr.teaser,'filtered_html'
  from $old_db.{$old_prefix}node_revisions nr, $old_db.{$old_prefix}node n
  where n.type = '$old_type' and n.vid = nr.vid
EOS;

  echo "body field revision table entries for users...\n";

  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

}

function _migrate_base_node($new_type,$old_type,$new_db,$old_db,$new_prefix,$old_prefix) {

  echo "**** this needs to check for filter format, currently all going in as filtered html\n";

  //d shed - watershed user mappings l
  //$uid = "CASE uid WHEN 12 then 48 WHEN 14 then 45 WHEN 11 then 52 WHEN 4 then 9 ELSE 0 END";
  
  $uid = "uid";

  $sql = "INSERT INTO $new_db.{$new_prefix}node 
  SELECT nid, vid, '$new_type', 'und', title, $uid, status, created, changed, comment, 0, 0, tnid, translate
  FROM $old_db.{$old_prefix}node WHERE type = '$old_type'";

  echo "node table entries for $new_type...\n";

  echo "\n\n$sql\n\n";

  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

  // node revisions

  $uid = "CASE nr.uid WHEN 12 then 48 WHEN 14 then 45 WHEN 11 then 52 WHEN 4 then 9 ELSE 0 END";

  $sql = "INSERT INTO $new_db.{$new_prefix}node_revision 
  SELECT nr.nid, nr.vid, $uid, nr.title, nr.log, nr.timestamp, n.status, n.comment, 0, 0
  FROM $old_db.{$old_prefix}node_revisions nr, $old_db.{$old_prefix}node n 
  WHERE n.type = '$old_type' and n.vid = nr.vid";

  echo "node revision table entries for $new_type...\n";

  //echo "\n\n$sql\n\n";
  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

  // body 'field'

  $sql = "INSERT INTO $new_db.{$new_prefix}field_data_body 
  SELECT 'node','$new_type',0,n.nid,n.vid,'und',0,nr.body,nr.teaser,'full_html'
  FROM $old_db.{$old_prefix}node_revisions nr, $old_db.{$old_prefix}node n
  WHERE n.type = '$old_type' AND n.vid = nr.vid";

  echo "body field table entries for $new_type...\n";

  //echo "\n\n$sql\n\n";
  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

  // ...and its revision entry

  $sql = "INSERT INTO $new_db.{$new_prefix}field_revision_body 
  SELECT 'node','$new_type',0,n.nid,n.vid,'und',0,nr.body,nr.teaser,'filtered_html'
  FROM $old_db.{$old_prefix}node_revisions nr, $old_db.{$old_prefix}node n
  WHERE n.type = '$old_type' AND n.vid = nr.vid";

  echo "body field revision table entries for $new_type...\n";

  //echo "\n\n$sql\n\n";
  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";

  //path alias

  $sql = "INSERT INTO $new_db.{$new_prefix}url_alias (source, alias, language)
  SELECT concat('node/',n.nid), dst, 'und' 
  FROM $old_db.{$old_prefix}url_alias ua, $old_db.{$old_prefix}node n 
  WHERE ua.src = concat('node/',n.nid) AND n.type = '$old_type'";

  echo "url alias table entries for $new_type...\n";

  //echo "\n\n$sql\n\n";
  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n"; }

  echo "...done\n";
  
  

}
