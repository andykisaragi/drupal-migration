<?php



echo "\n\n" . __FILE__ . "\n\n";

$new_type = 'webform';
$old_type = 'webform';

//_migrate_base_node($new_type,$old_type,$new_db,$old_db);

// webform table

$case = <<<EOC
CASE substring(w.confirmation,0,7) when 'http://' then '<confirmation>' else '' END
EOC;

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform 
select w.nid, w.confirmation, 'full_html', $case, 1, 0, w.teaser, 0, 0, 1, w.submit_text, w.submit_limit, w.submit_interval, -1, -1 
from $old_db.{$old_prefix}webform w, $old_db.{$old_prefix}node n 
where w.nid = n.nid and n.status = 1 
EOS;

echo "webform\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";




// webform_component

echo "should probably check the 'extra' is the same format... probably is-ish..\n";

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform_component 
select wc.nid, wc.cid, wc.pid, wc.form_key, wc.name, wc.type, '', wc.extra, wc.mandatory, wc.weight 
from $old_db.{$old_prefix}webform_component wc, $old_db.{$old_prefix}node n 
where wc.nid = n.nid and n.status = 1 
EOS;

echo "webform_component\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";



// webform emails

echo "dropping all additional validate or submit data. dont know how it might function, will avoid errors to users...\n";

echo "all custom email templates to drop off too\n";

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform_emails 
select *
from $old_db.{$old_prefix}webform_emails
EOS;

echo "webform_emails\n";

$result = mysql_query($sql);
if(!$result) { echo $sql . "\n" . mysql_error() . "\n"; }

echo "...done\n";




// webform roles

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform_roles 
select wr.nid, wr.rid  
from $old_db.{$old_prefix}webform_roles wr, $old_db.{$old_prefix}node n 
where wr.nid = n.nid and n.status = 1 
EOS;

echo "webform_roles\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";




// webform submissions

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform_submissions 
select ws.sid, ws.nid, ws.uid, 0, ws.submitted, ws.remote_addr 
from $old_db.{$old_prefix}webform_submissions ws, $old_db.{$old_prefix}node n 
where ws.nid = n.nid and n.status = 1 
EOS;

echo "webform_submissions\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n";




// webform_submitted_data

$sql = <<<EOS
insert into $new_db.{$new_prefix}webform_submitted_data 
select wd.nid, wd.sid, wd.cid, wd.no, wd.data
from $old_db.{$old_prefix}webform_submitted_data wd, $old_db.{$old_prefix}node n 
where wd.nid = n.nid and n.status = 1 
EOS;

echo "webform_submitted)_data\n";

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

echo "...done\n"; 

// update the redirect thing

$sql = "SELECT nid, confirmation FROM $new_db.{$new_prefix}webform";
$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }else{
	while($row = mysql_fetch_array($result)){

		$conf = strip_tags($row['confirmation']);
		$nid = $row['nid'];
		
		if (strpos($conf,'http') === 0 || strpos($conf,'internal:') === 0){
		
			$sql = "UPDATE $new_db.{$new_prefix}webform SET redirect_url = '$conf', confirmation = '' WHERE nid = $nid";
		
			
		}else{
			$sql = "UPDATE $new_db.{$new_prefix}webform SET redirect_url = '<confirmation>' WHERE nid = $nid";
		}
		$update_result = mysql_query($sql);
		if(!$result) { echo mysql_error() . " - $nid \n"; }

	}
}
