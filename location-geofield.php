<?php

/*
Import from d6 location module to d7 geofield


should I do a node save / form submit for each one ????


*/	

echo "\n\n" . __FILE__ . "\n\n";

$sqls = array();

$sqls[] = "SELECT n.nid, l.latitude, l.longitude FROM $new_db.{$new_prefix}node n
LEFT JOIN $old_db.{$old_prefix}content_type_uni_location ul ON n.nid = ul.nid
LEFT JOIN $old_db.{$old_prefix}location l ON ul.field_uni_location_location_lid = l.lid
WHERE n.type = 'uni_location'";

$sqls[] = "SELECT n.nid, l.latitude, l.longitude FROM $new_db.{$new_prefix}node n
LEFT JOIN $old_db.{$old_prefix}content_type_university2 u ON n.nid = u.nid
LEFT JOIN $old_db.{$old_prefix}location l ON u.field_university2_location_lid = l.lid
WHERE n.type = 'university2'";

echo "copying locations.....\n";


$count = 0;
foreach($sqls as $sql){

  $result = mysql_query($sql);
  if(!$result) { echo mysql_error() . "\n$sql\n\n"; }

  while ($row = mysql_fetch_array($result)){

    $node = node_load($row['nid']);
    $count++;
    if($count % 20 == 0){
      echo " . ";
    }
    $lat = $row['latitude'];
    $lon = $row['longitude'];
    if($lat && $lon){
      $node->field_location = array(
        'und' => array(
            0 => array(
              'geom' => "POINT ($lon $lat)",
              'geo_type' => "point",
              'lat' => $lat,
              'lon' => $lon,
              'left' => $lon,
              'top' => $lat,
              'right' => $lon,
              'bottom' => $lat,
            )
          )
        );

      node_save($node);
    }

  }

}


/*
$sql = "SELECT c.nid, c.vid, l.* FROM $old_db.{$old_prefix}content_type_university2 c
LEFT JOIN $old_db.{$old_prefix}location l ON c.field_university2_location_lid = l.lid
WHERE c.field_university2_location_lid > 0";

$result = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n$sql\n\n"; }


while ($row = mysql_fetch_array($result)){


	extract($row);


	$geo = geocoder('latlon', "POINT($latitude, $longitude)");
	//print_r($geo);
	$geo = serialize($geo);

	$sql = "INSERT INTO $new_db.{$new_prefix}field_data_field_location VALUES ('node','university2',0,$nid,$vid,'und',0,'$geo','point',$latitude, $longitude,$longitude,$latitude,$longitude,$latitude,'')";

	$insert_query = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n$sql\n\n"; }

}

$handlers = geocoder_handler_info();

//print_r($handlers);



$field_name = 'field_location';

$table_name = 'field_data_' . $field_name;
$revision_table_name =  'field_revision_' . $field_name;

// Populate geohash column.
geophp_load();
$sql = "SELECT {$field_name}_geom AS geom, entity_id, revision_id, delta FROM $table_name";
$result = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n$sql\n\n"; }

while($record = mysql_fetch_object($result)) {
	echo $record->entity_id . "\n";
  if (!empty($record->geom)) {
print_r($record->geom);
    $geom = geoPHP::load($record->geom);
	

			// Truncate geohash to max length.
    $geohash_truncated = substr($geom->out('geohash'), 0, GEOFIELD_GEOHASH_LENGTH);
    db_update($table_name)
      ->fields(array(
        $field_name . '_geohash' => $geohash_truncated,
      ))
      ->condition('entity_id', $record->entity_id)
      ->condition('revision_id', $record->revision_id)
      ->condition('delta', $record->delta)
      ->execute();
  }
}

$sql = "SELECT {$field_name}_geom AS geom, entity_id, revision_id, delta FROM $revision_table_name";
$result = mysql_query($sql);
	if(!$result) { echo mysql_error() . "\n$sql\n\n"; }

while($record = mysql_fetch_object($result)) {
  if (!empty($record->geom)) {
    $geom = geoPHP::load($record->geom);
			// Truncate geohash to max length.
    $geohash_truncated = substr($geom->out('geohash'), 0, GEOFIELD_GEOHASH_LENGTH);
    db_update($revision_table_name)
      ->fields(array(
        $field_name . '_geohash' => $geohash_truncated
      ))
      ->condition('entity_id', $record->entity_id)
      ->condition('revision_id', $record->revision_id)
      ->condition('delta', $record->delta)
      ->execute();
  }
}
*/