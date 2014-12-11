<?php

 // just to put the right language in url alias table

$sql = "SELECT * FROM $old_db.{$old_prefix}url_alias WHERE language <> ''";
echo $sql . "\n";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while($row = mysql_fetch_array($result)){


	extract($row);

	$updatesql = "UPDATE $new_db.{$new_prefix}url_alias SET language = '$language' WHERE source = '$src'";
	$updateresult = mysql_query($updatesql);
	if(!$updateresult) { echo mysql_error() . "\n"; }


}
