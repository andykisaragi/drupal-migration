<?php

// comment out memcache to run this


require_once "vars.php";
require_once "db_settings.php";

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// may need to switch to drupal dir? 

function microtime_float() {
    list ($msec, $sec) = explode(' ', microtime());
    $microtime = (float)$msec + (float)$sec;
    return $microtime;
}
$start = microtime_float();

echo "\n===========\n";

$conn = mysql_connect($db_host,$db_user,$db_pass);
if(!$conn) { die('Could not connect:' . mysql_error()); }
mysql_select_db($new_db,$conn);

	require_once "functions.php";
	
	//require_once "delete-everything.php";
	
	//require_once "startup.php";
	
	//require_once "types.php";
	//require_once "nodes.php";
	//require_once "create-fields.php";
	
	//require_once "nodes-fields.php";
	
	//require_once "location-geofield.php";

	//require_once "taxonomy_data.php";
	//require_once "taxonomy.php";

	//require_once "users-users.php";

	//require_once "comments.php";

	//require_once "quiz.php";
	



	//require_once "users-users.php";
	//require_once "users-roles.php";
	//require_once "users-fields.php";
	//require_once "users-files.php";	

	//require_once "menu.php";
	//require_once "users-emails.php";
	//require_once "users-rehash.php";
	//require_once "blocks.php";
	//
	//require_once "node-webform.php";/
	
	
	//require_once "nodes-fields.php";
	require_once "references.php";
	//require_once "files.php";
	//require_once "nodes-file-fields.php";

	//require_once "fix-languages.php";
	//require_once "fix-aliases.php";

	//require_once "nodequeue.php";




	
$end = microtime_float();
// Print results.
//echo "\n\nNid offset " . variable_get('andy_migration_nid_offset') . " / fid offset " . variable_get('andy_migration_fid_offset') . "\n\n";

echo 'Script Execution Time: ' . round($end - $start, 3) . ' seconds' . "\n";   