<?php

use Core\Database;

$config = require base_path('config.php');
$db = new Database($config['database']);

$currentUserId = 1;
if($_SERVER['REQUEST_METHOD']==='POST'){

    // form was submitted, delete the current note.

    $note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetchOrFail();

    authorize($note['user_id']===$currentUserId);

    $db->query('delete from notes where id = :id', [
        'id'=>$_GET['id']
    ]);
    header('location: /notes');
    exit();
}else{
    $note = $db->query("select * from notes where id=:id",[ 'id'=>$_GET['id']])->fetchOrFail();

    authorize($note['user_id']===$currentUserId);
}



view('notes/show.view.php',[
    'heading'=>'Note',
    'note'=>$note
]);