<?php
require 'functions.php';
require 'Database.php';
// require 'router.php';

$config = require 'config.php';
$db = new Database($config['database']);

$notes = $db->query("select * from notes")->fetchAll();

foreach( $notes as $note ){
    echo "<li>" . $note['body']. "</li>";
}

