<?php

echo "\n\n" . __FILE__ . "\n\n";

$sql = <<<EOS
insert into siftmedia_notify_subs 
select nt.sid, nt.uid, case nt.type when 'thread' then 'comment' when 'group' then 'group' end, 
f.value, case nt.send_interval when 0 then 'instant' when 3600 then  'hour' when 43200 then 'day' when 86400 then 'day' when 604800 then 'week' end
from $old_db.notifications nt, $old_db.users u, $old_db.node n, $old_db.notifications_fields f
where nt.sid = f.sid and nt.uid = u.uid and n.nid = f.value 
and nt.send_interval >= 0 
EOS;

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }

$sql = <<<EOS
delete from siftmedia_notify_subs where type ='comment' and nid not in (select nid from node where created > (UNIX_TIMESTAMP() - 7889231))
EOS;

$result = mysql_query($sql);
if(!$result) { echo mysql_error() . "\n"; }