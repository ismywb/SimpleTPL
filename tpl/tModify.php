<?php
lRequired();
$game = new \Tournaments\Tournament($_REQUEST['tid']);
$game->modpack = $_GET['modpack'];
$game->Save();
header("Location: /round.html?tid=".$_GET['tid']."&round=".$_GET['round']);
die();
