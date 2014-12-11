<?php
echo "\n\n" . __FILE__ . "\n\n";
// clean up existing users table
$sql = <<<EOS
delete from $new_db.{$new_prefix}users where uid > 1;
EOS;
echo "remove any test users...\n";
$result = mysql_query($sql);

$sql = <<<EOS
update $new_db.{$new_prefix}users set name = 'admin_migration' where name = 'admin';
EOS;
$result = mysql_query($sql);

// import appropriate users. setting email to my email at the moment
/*$sql = <<<EOS
insert into $new_db.users select uid, name, pass, mail, theme, signature, 'filtered_html', 
created, access, login, status, NULL, language, 0, mail, data from $old_db.users where access > $access_cutoff and uid > 1000 and uid < 2000; 
EOS;*/

$sql = <<<EOS
insert into $new_db.{$new_prefix}users select uid, name, pass, 'andy.4705@gmail.com', theme, signature, 'filtered_html', 
created, access, login, status, NULL, language, 0, mail, data from $old_db.{$old_prefix}users where access > $access_cutoff and uid > 1; 
EOS;
echo "users table query...\n";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }
echo "...done\n";
/** NEED CLEANUP FOR ONWNED NOSE **/