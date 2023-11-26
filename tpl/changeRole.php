<?php
lRequired();
$round = \Tournaments\Round::LoadBy(array('game_id'=>$_REQUEST['tid'],'round'=>$_REQUEST['round'],'player_id'=>$_REQUEST['player']))[0];
$round->role = $_GET['role'];
/* */
$type = 1;
if ($_GET['role']!= 'null') {
$role = \Mods\Role::LoadBy(array('id'=>$_GET['role']))[0];
$type = $role->crew_type;
}
//print_r($role);die;
$round->is_crew = $type;

/* */
$round->Save();
    header("Location: /round.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}");
    die;

