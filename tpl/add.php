<?php
lRequired();
$player = new \Players\Player($_REQUEST['pid']);
$i = 0;
while($i < 10) {

    $i++;
    try {
        $match = \Tournaments\Match::LoadBy(array("tid"=>$_REQUEST['tid'],"round"=>$i));
    } catch (\Exceptions\ItemNotFound $e) {
        $match = new \Tournaments\Match();
        $match->tid = $_REQUEST['tid'];
        $match->round = $i;
        $match->Save();
    }
    $addM = new \Tournaments\Round();
    $addM->player_id = $player->id;
    $addM->game_id = $_REQUEST['tid'];
    $addM->round = $i;
    $addM->Save();
}

header("Location: /round.html?tid=".$_REQUEST['tid']);
die;
