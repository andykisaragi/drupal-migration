<?php

echo "\n\n" . __FILE__ . "\n\n";

/*
//Basic content types

$new_type = 'any_answers';
$old_type = 'anyanswers_question';

_migrate_base_node($new_type,$old_type,$new_db,$old_db);

//opportunities

$new_type = 'opportunities';
$old_type = 'opportunity';

_migrate_base_node($new_type,$old_type,$new_db,$old_db);
*/

//press

$new_type = 'press';
$old_type = 'press';

_migrate_base_node($new_type,$old_type,$new_db,$old_db);

$new_type = 'page';
$old_type = 'page';

_migrate_base_node($new_type,$old_type,$new_db,$old_db);

//microsite
/*
$new_type = 'microsite_page';
$old_type = 'microsite_page';

_migrate_base_node($new_type,$old_type,$new_db,$old_db);*/

