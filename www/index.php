<?php
$db = "db";
if (isset($_GET['db'])) $db = $_GET['db'];
define('ADB', $db);
new \Site\Page();
die();

