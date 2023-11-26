<?php

if (!\Login\User::isLoggedIn()) {
    $__output = '<h1>403</h1>';
    return;
}

$total_matches = 10;
$minimum_rot = 2;
$max_rot = 3;
$tpl = '<h1>Map Rotation</h1><table class="table table-bordered"><thead><th>Map</th><th>Games on Map</th><tbody>';
/*
<tr>
      <th scope="row">Vote Crewmate</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$CrewVote.'</td>
      <td>0</td>
    </tr>';
    */

$maps = array('Skeld','Mira','Polus','Airship');
if (isset($_GET['sub'])) $maps[] = 'Submerged';
$numDone = 0;
while ($numDone < $total_matches) {
  if (count($maps) > 1) {
    $num = rand(0,count($maps)-1);
    $map = $maps[$num];
    $games = rand($minimum_rot, $max_rot);
    if ($numDone + $games >= $total_matches) {
        $games = (($total_matches - $numDone) - 1);
    }
    $tpl .= "<!--\n\n".print_r($maps,1)."\n\n-->";
    unset($maps[$num]);
    $maps = array_values($maps);
    $numDone += $games;
    $tpl .= '<tr><td>'.$map.'</td><td>'.$games.'</td></tr>'."\n";
  } else {
    $gamesLeft = ($total_matches - $numDone);
    $tpl .= '<tr><td>'.$maps[0].'</td><td>'.$gamesLeft.'</td></tr>'."\n";  
    $numDone = $total_matches;
  }
}
$tpl .= '</tbody></table>';
$__output = $tpl;
