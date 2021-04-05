<?php

require_once 'vendor/autoload.php';
use App\Database\DatabaseConnection;

$table = "links";
try {
    $db = DatabaseConnection::getInstance();
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
    $sql ="CREATE table $table(
     id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
     shortified VARCHAR( 50 ) NOT NULL, 
     is_custom BOOLEAN NOT NULL DEFAULT 0,
     raw VARCHAR( 2048 ) NOT NULL);" ;
    $db->exec($sql);
    print("Created $table Table.\n");

} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}