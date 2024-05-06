<?php
$heading = "My notes";

$config = require "config.php";
$db = new Database($config['database']);


$notes = $db->query("select * from notes where user_id= :user_id",['user_id'=>1])->get();






require 'views/notes/index.view.php';