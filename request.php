<?php
/* 
 * DB Klasse 
 * 
 * DB Klasse, die sich unter den angegebenen Daten mit einem Server verbindet und eine DB auswählt 
 * PHP 5 
 * @copyright Copyright (c) 2010 burbot 
 * @author burbot 
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once('./lib/Dispatcher.php');
require_once('./lib/DatabaseManager.php');
require_once('./lib/Helper.php');

//
$dispatcher = new Lib_Dispatcher(new Lib_DatabaseManager(), new Lib_Helper());
$dispatcher->dispatchAction($_GET['action'], $_GET);