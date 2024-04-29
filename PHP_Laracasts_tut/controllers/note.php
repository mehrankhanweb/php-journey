<?php
$heading = "Note";

$id = $_GET['id'];

$config = require 'config.php';
$db = new Database($config['database']);

$note = $db->query("select * from notes where id = :id",['id'=>$id])->fetch();









require 'views/note.view.php';