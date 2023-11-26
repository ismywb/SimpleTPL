<?php
define('AJAX',1);
$type = 'F';
if (isset($_GET['type'])) $type = $_GET['type'];
date_default_timezone_set('America/New_York');
echo '&lt;t:'.strtotime($_GET['t']).':'.$type.'>';
