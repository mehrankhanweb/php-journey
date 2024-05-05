<?php

require 'Validator.php';

$heading = "Create Note";

$config = require 'config.php';
$db = new Database($config['database']);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $errors = [];

    if(!Validator::string($_POST['body'],1,1000)){
        $errors['body'] = 'A body of no more than 1,000 characters is required';
    }


    // if(strlen($_POST['body']) > 1000) {
    //     $errors['body'] = 'the body cannot be more than 1,000 characters.';
    // }



    if(empty($errors)){
    $db->query('insert into notes(body, user_id) values(:body, :user_id)',[
        'body'=>$_POST['body'],
        'user_id' => 1
    ]);
    }
    // Reset body after successful insertion

    $_POST['body'] = '';
}


require 'views/note-create.view.php';