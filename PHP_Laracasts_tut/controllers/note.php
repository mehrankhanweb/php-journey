<?php
$heading = "Note";


$config = require 'config.php';
$db = new Database($config['database']);

$currentUserId = 1;

$note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetchOrFail();




// if($note['user_id']!=$currentUserId){
//     abort(Response::FORBIDDEN);
// }

// Instead of above we do like this

authorize($note['user_id']===$currentUserId);







require 'views/note.view.php';