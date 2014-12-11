<?php

echo "\n\n" . __FILE__ . "\n\n";

// file_managed d7
// fid, uid, filename, uri, filemime, filesize, status, timestamp
// files d6
// fid, uid, filename, filepath, filemime, filesize, status, timestamp, origname

$fid_offset = FID_OFFSET;

$sql = "INSERT INTO $new_db.{$new_prefix}file_managed
	SELECT fid+$fid_offset, uid, filename, REPLACE(filepath,'sites/default/files/','public://'), filemime, filesize, status, timestamp, filename
	FROM $old_db.{$old_prefix}files";

echo "Copying files table entries \n\n $sql \n\n";

$result = mysql_query($sql);
if(!$result) { echo "\n" . mysql_error() . "\n"; }else{ echo " ...done\n\n";}