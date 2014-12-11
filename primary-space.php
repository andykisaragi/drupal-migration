<?php

// imports term & vocabulary data

echo "\n\n" . __FILE__ . "\n\n";

$sql = "INSERT INTO $new_db.{$new_prefix}field_data_watershed_primary_space SELECT 'node', n.type, 0, n.nid, n.vid, n.language, 0, pt.tid FROM `wsv1_primary_term` pt LEFT JOIN wsv1_node n ON n.vid = pt.vid"

// entity_type, bundle, deleted, entity_id, revision_id, language, delta, watershed_primary_space_value