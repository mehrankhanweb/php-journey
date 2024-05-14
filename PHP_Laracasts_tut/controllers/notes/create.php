<?php
use Core\Database;
use Core\Validator;


$config = require base_path('config.php');
$db = new Database($config['database']);
$errors = []; // Can you tell me why this is not inside if statement?

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if(!Validator::string($_POST['body'],1,10)){
        $errors['body'] = 'A body of no more than 1,000 characters is required';
    }

    if(empty($errors)){
    $db->query('insert into notes(body, user_id) values(:body, :user_id)',[
        'body'=>$_POST['body'],
        'user_id' => 1
    ]);

    // Reset body after successful insertion
    $_POST['body'] = '';

    }

}


view('notes/create.view.php',[
    'heading'=>'Create Note',
    'errors'=>$errors
]);

// Why $errors is defined outside if condition
// view() function call, that includes 'errors' => $errors is executed every time the script runs, not just when handling a POST request. This is why $errors needs to be defined outside of the POST checking block—it needs to be available to your view function on all types of requests, including GET.