<?php

echo "\n\n" . __FILE__ . "\n\n";
$path = "/data/PT/www/sites/default/files/images/";
$filesdir = opendir($path);

while($filename = readdir($filesdir)){

	if (strpos($filename,'.thumbnail.')!==false){
		echo $filename . "\n";
		
		$newname = str_replace('.thumbnail.','.',$filename);
		
		if (!file_exists($newname)){
			rename($path . $filename, $path . $newname);
		}
	}
	
}

$path = "/data/PT/www/sites/default/files/";

$sql = "SELECT fid, uri FROM $new_db.file_managed";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

while ($row = mysql_fetch_array($result)){

	echo $row['fid'];

	$filepath = str_replace('public://',$path,$row['uri']);
	if (!file_exists($filepath)){
	
		echo " - " . $filepath . " - ";
		$filepath .= "_0";
		
		if (file_exists($filepath)){
			
			$filepath = str_replace($path,'public://',$row['uri']);
			
			$filename = explode("/",$filepath);
			$filename = array_pop($filename);
		
			$update = "UPDATE $new_db.file_managed SET uri = '" . $filepath . "' WHERE fid = " . $row['fid'];
				
			$upresult = mysql_query($update);
			if(!$upresult) { echo mysql_error() . "\n"; }		
			
			$update = "UPDATE $new_db.file_managed SET filename = '" . $filename . "' WHERE fid = " . $row['fid'];
				
			$upresult = mysql_query($update);
			if(!$upresult) { echo mysql_error() . "\n"; }
			
			echo "updated $fid \n";
		
		}
	
	}
	
	echo "\n";

}