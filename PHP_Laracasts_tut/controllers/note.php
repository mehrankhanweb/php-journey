<?php
$heading = "Note";


$config = require 'config.php';
$db = new Database($config['database']);


$note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetch();

$currentUserId = 1;

if(!$note){
    abort(Response::NOT_FOUND);
}

if($note['user_id']!=$currentUserId){
    abort(Response::FORBIDDEN);
}







require 'views/note.view.php';