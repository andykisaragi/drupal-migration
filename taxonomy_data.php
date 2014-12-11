<?php

// imports term & vocabulary data


echo "\n\n" . __FILE__ . "\n\n";


// vocabulary
// vid, name, machine_name, description, hierarchy, module, weight


echo "\nImport Vocabularies\n";
$sql = <<<EOS
insert into {$new_prefix}taxonomy_vocabulary 
select distinct vid, name, LOWER(REPLACE(name,' ','_')), description, hierarchy, 'taxonomy', weight
from $old_db.{$old_prefix}vocabulary;
EOS;
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }


// term data
// tid, vid, name, description, format, weight
/*
echo "\ninsert terms to taxonomy term data\n";
$sql = <<<EOS
insert into {$new_prefix}taxonomy_term_data 
select distinct tid, vid, name, description, 'filtered_html', weight
from $old_db.{$old_prefix}term_data;
EOS;
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

// term hierarchy
// tid, parent



echo "\ninsert terms to taxonomy hierarchy\n";
$sql = <<<EOS
insert into {$new_prefix}taxonomy_term_hierarchy 
select distinct tid, parent
from $old_db.{$old_prefix}term_hierarchy;
EOS;
$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }*/