<?php

echo "\n\n" . __FILE__ . "\n\n";



echo "insert terms to taxonomy index \n";
$sql = <<<EOS
insert into $new_db.{$new_prefix}taxonomy_index 
select t.nid, t.tid, 0, UNIX_TIMESTAMP() 
from $old_db.{$old_prefix}term_node t, $old_db.{$old_prefix}term_data d where t.tid = d.tid;
EOS;
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }


// create fields for vocabs
echo "creating taxonomy fields....\n";
$sql = "SELECT v.vid, v.name, v.machine_name, nt.type FROM $new_db.{$new_prefix}taxonomy_vocabulary v 
    LEFT JOIN $old_db.{$old_prefix}vocabulary_node_types nt ON v.vid = nt.vid";
//echo "$sql\n";
$result = mysql_query($sql);
$config_done = [];
$fields = [];
while ($row = mysql_fetch_array($result)){
    $name = 'field_' . str_replace(' ','_',strtolower($row['name']));
    $exist_sql = "SELECT id FROM $new_db.{$new_prefix}field_config WHERE field_name='$name'";
    //echo "$exist_sql\n";
    $exists = mysql_result(mysql_query($exist_sql),0);
    //echo "exists: $exists\n";
    if(!$exists){
        $field = array(
          'field_name' => $name,
          'type' => 'taxonomy_term_reference',
          'settings' => array(
            'allowed_values' => array(
              array(
                'vocabulary' => $row['machine_name'],
                'parent' => 0
              ),
            ),
          ),
        );
        field_create_field($field);
        $config_done[$name] = true;
    }
    $fields[$name][] = $row;
}

foreach ($fields as $field_name => $rows){
    $field_id = mysql_result(mysql_query("SELECT id FROM $new_db.{$new_prefix}field_config WHERE field_name='$field_name' ORDER BY id DESC LIMIT 1"),0);
    foreach($rows as $row){
        $type = $row['type'];
        if(!empty($type)){
            $exist_sql = "SELECT id FROM $new_db.{$new_prefix}field_config_instance WHERE field_name='$field_name' AND bundle='$type'";
            $exists = mysql_result(mysql_query($exist_sql),0);
            if(!$exists){
                $instance = array(
                    'field_name' => $field_name,
                    'entity_type' => 'node',
                    'label' => $row['name'],
                    'bundle' => $row['type'],
                    'required' => false,
                    'widget' => array(
                        'type' => 'options_select'
                    ),
                    'display' => array(
                        'default' => array('type' => 'hidden'),
                        'teaser' => array('type' => 'hidden')
                    )
                );
                field_create_instance($instance);
            }
        }
    }
}

// get fields from field_config?
$sql = "SELECT field_name, data FROM $new_db.{$new_prefix}field_config WHERE type = 'taxonomy_term_reference'";
$result = mysql_query($sql);
$term_fields = [];
while ($row = mysql_fetch_array($result)){
    $data = unserialize($row['data']);
    $vocab = $data['settings']['allowed_values'][0]['vocabulary'];
    $sql = "SELECT vid FROM $new_db.{$new_prefix}taxonomy_vocabulary WHERE machine_name = '$vocab'";
    $vid = mysql_result(mysql_query($sql),0);
    $term_fields[$row['field_name']] = $vid;
}

// seriously? this shouldnt work
// still not sure what this does yknow

$sql = <<<EOS
select 'node', n.type, 0, n.nid, n.nid, 'und',
CASE 
        WHEN COALESCE(@rownum, 0) = 0 
        THEN @rownum:=c-1 
        ELSE @rownum:=@rownum-1 
    END as delta,
t.tid
from 
node n, taxonomy_index t, (select nid, count(*) as c from taxonomy_index group by nid) as test, taxonomy_term_data d
where t.nid = n.nid and n.nid = test.nid and t.tid = d.tid and d.vid = 2;
EOS;
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; } else { echo "dumm query\n\n"; }


foreach($term_fields as $field_name => $vid){
    echo "$field_name\n";
    $sql = "INSERT INTO field_data_{$field_name}
    select 'node', n.type, 0, n.nid, n.nid, 'und',
    CASE 
            WHEN COALESCE(@rownum, 0) = 0 
            THEN @rownum:=c-1 
            ELSE @rownum:=@rownum-1 
        END as delta,
    t.tid
    from 
    node n, taxonomy_index t, (select nid, count(*) as c from taxonomy_index group by nid) as test, taxonomy_term_data d
    where t.nid = n.nid and n.nid = test.nid and t.tid = d.tid and d.vid = $vid;";
    $result = mysql_query($sql);
    if(!$result) { echo mysql_error() . "\n"; }
    $sql = "insert into field_revision_{$field_name} 
    select 'node', n.type, 0, n.nid, n.nid, 'und',
    CASE 
            WHEN COALESCE(@rownum, 0) = 0 
            THEN @rownum:=c-1 
            ELSE @rownum:=@rownum-1 
        END as delta,
    t.tid
    from 
    node n, taxonomy_index t, (select nid, count(*) as c from taxonomy_index group by nid) as test, taxonomy_term_data d
    where t.nid = n.nid and n.nid = test.nid and t.tid = d.tid and d.vid = $vid;";
    $result = mysql_query($sql);
    if(!$result) { echo mysql_error() . "\n"; }
}