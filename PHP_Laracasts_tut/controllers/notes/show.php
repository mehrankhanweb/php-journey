<?php
$heading = "Note";


$config = require 'config.php';
$db = new Database($config['database']);

$currentUserId = 1;

$note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetchOrFail();


authorize($note['user_id']===$currentUserId);







require 'views/notes/show.view.php';