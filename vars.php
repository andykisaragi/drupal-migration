<?php

//define some variables
echo "\n\n" . __FILE__ . "\n\n";

define('DRUPAL_ROOT', '/var/www/scool-d7');
$_SERVER['REMOTE_ADDR']   = "d7.s-cool.co.uk";
$migration_script_path    = '/var/www/migration';
$access_cutoff            = 0;       

$settings = [
	'delete_nodes' => false,

];

$type_prefix = '';
$skip_types = "'" . implode("', '",array('profile','ad')) . "'";