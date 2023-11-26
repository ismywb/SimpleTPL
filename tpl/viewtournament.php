<?php

if (!isset($_REQUEST['round'])) {
    $_REQUEST['round'] = 1;   
}
$tpl = '';
/** */

//$__output = "Info for Tournament ID {$_REQUEST['tid']}!";
$game = new \Tournaments\Tournament($_REQUEST['tid']);
if ($game->public != 1 && !\Login\User::isLoggedIn()) {
    $__output = "<h1>Tournament Not Found!</h1>";
    return;
}
$pack = \Mods\Mod::getMod($game->modpack);
$data = array(
    'game_id'=> $_REQUEST['tid'],
    'round' => $_REQUEST['round']
    );
$round = null;
try {
$round = \Tournaments\Round::LoadBy($data);
} catch (\Exceptions\ItemNotFound $e) {}
$match = null;
try {
$match = \Tournaments\Match::LoadBy(array('tid'=>$data['game_id'],'round'=>$data['round']))[0];
//$tpl  = '<pre>'.print_r($game,1).print_r($match,1).'<br />test<br /></pre>';
} catch (\Exceptions\ItemNotFound $e) { }
$tpl .= <<<end
<h1 class="display-3">{$game->name}</h1>
<h1 class="display-4">ModPack: {$pack}</h1>
end;
$tpl .= <<<end
<table class="table" >
  <thead>
    <tr>
      <th scope="col">Crew Win</th>
      <th scope="col">Task Win</th>
      <th scope="col">Imposter Win</th>
      <th scope="col">Sabbo Win</th>
      <th scope="col">Neutral Win</th>
    </tr>
    </thead>
    <tbody>
end;
if ($match->is_crew_win == 1) {
    $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/yes.png" class="icon" /></td>';
    if ($match->is_task_win == 1) {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/yes.png" class="icon" /></td>';
    } else {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/none.png" class="icon" /></td>';
    }
} else {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/none.png" class="icon" /></td><td><img src="//static.scumscyb.org/img/none.png" class="icon" /></td>';
}
if ($match->is_crew_win == 0) {
    $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/yes.png" class="icon" /></td>';
    if ($match->is_sabo_win == 1) {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/yes.png" class="icon" /></td>';
    } else {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/none.png" class="icon" /></td>';
    }
} else {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/none.png" class="icon" /></td><td><img src="//static.scumscyb.org/img/none.png" class="icon" /></td>';
}
if ($match->is_crew_win == 2) {
    $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/yes.png" class="icon" /></td>';
} else {
        $tpl .= '      <td scope="col"><img src="//static.scumscyb.org/img/none.png" class="icon" /></td>';
}
$tpl .= '</tr></table>';
$tpl .= <<<end
<table class="table" id="voting">
  <thead>
    <tr>
      <th scope="col">Role</th>
      <th scope="col">Player</th>
      <th scope="col">Kills</th>
      <th scope="col">Imp Eject</th>
      <th scope="col">Imp Vote</th>
      <th scope="col">Crew Eject</th>
      <th scope="col">Crew Vote</th>
      <th scope="col">Is Crew</th>
      <th scope="col">Is Neutral</th>
      <th scope="col">Extra Points</th>
      <th scope="col">Tasks Done</th>
      <th scope="col">Round Points</th>
      <th scope="col">Total Points</th>
    </tr>
  </thead>
  <tbody>
end;
$i = 0;
$imp = array();
$crew = array();
$solo = array();
foreach($round as $r) {
  if ($r->is_crew == 1) $crew[] = $r;
  if ($r->is_crew == 0) $imp[] = $r;
  if ($r->is_crew == 2) $solo[] = $r;
}
$master = $imp;
foreach($crew as $c) {
  $master[] = $c;
}
foreach($solo as $s) {
  $master[] = $s;
}
//echo code(print_r($master,1));die;
foreach ($master as $r) {
//print_r($r);die;
     $i++;
     $r->populatePlayer();
     $r2 = print_r($r,1);
$class = 'crew';
     if ($r->isImp(0)) {
         $class = 'imp';
     } elseif ($r->isNeutral(0)) {
         $class = 'neutral';
     }
    $role = \Mods\Mod::getRole($game->modpack,$r->role);
    $tpl .= <<<end

    <tr class="is-{$class}" style="background-color: {$role["hex"]} !important;" >
      <td scope="col">{$role['name']}</td>
      <td scope="col">{$r->player_id->name}</td>
      <td scope="col">{$r->kills}</td>
      <td scope="col">{$r->imp_eject}</td>
      <td scope="col">{$r->imp_vote}</td>
      <td scope="col">{$r->crew_eject}</td>
      <td scope="col">{$r->crew_vote}</td>
      <td scope="col">{$r->isCrew()}</td>
      <td scope="col">{$r->isNeutral()}</td>
      <td scope="col">{$r->extra_points}<br /><code style=" background-color: black; color:  white; ">{$r->extra_points_why}</code></td>
      <td scope="col">{$r->tasksDone()}</td>
      <td scope="col">{$r->getPoints($data['game_id'],$data['round'])}</td>
      <td scope="col">{$game->getFinalPoints($r->player_id->id,$data['round'])}</td>
    </tr>
end;
    //$__output .= '<pre>'.print_r($r,1).'<br />'.$r->player_id->discord_tag.'<br />'.$r->getPoints($data['game_id'],$data['round']).'</pre><hr>';
}
/* **
$match = \Tournaments\Match::_LoadBySQL("SELECT * FROM `matches` WHERE `tid` = 1 AND `round` = 1");
$__output = print_r($match,1);

*/
$pdisable = '';
if ($data['round'] == 1) $pdisable = 'disabled';
$pround = max(1,($_REQUEST['round'] - 1));
$tpl .= <<<end
</table>
end;
$order = '					"ordering": false';

if ($_REQUEST['round'] == 10) $order = '					"order":[[ 12, "desc" ]]';
$tpl .= <<<end
		<script type="text/javascript">
			$(document).ready(function() {
				$("#voting").DataTable({
					"paging": false,
					"stateSave": false,
					"searching": false,
{$order}
				});
			});
		</script>
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item {$pdisable}">
      <a class="page-link" href="/viewtournament.html?tid={$_REQUEST['tid']}&round={$pround}" tabindex="-1">Previous</a>
    </li>
end;
$num = 1;
while ($num <= 10) {
    $disabled = '';
    $tid = $_REQUEST['tid'];
    if ($num == $_REQUEST['round'] ) $disabled = 'disabled';
    $tpl .= "    <li class='page-item {$disabled}'><a class='page-link ' href='/viewtournament.html?tid={$tid}&round={$num}'>{$num}</a></li>\n";
    $num++;
}
$nRound = min(10,($_REQUEST['round'] + 1));
$ndisable = '';
if ($data['round'] == 10) $ndisable = 'disabled';

$tpl .= <<<end
<li class="page-item {$ndisable}">
      <a class="page-link " href="/viewtournament.html?tid={$_REQUEST['tid']}&round={$nRound}">Next</a>
    </li>
  </ul>
</nav>
end;



$tpl2 = <<<end
<table class="table" >
  <thead>
    <tr>
      <th scope="col">Metric</th>
      <th scope="col">Player</th>
      <th scope="col">Count</th>
    </tr>
    </thead>
    <tbody>
end;
try {
    $metrics = array('kills','crew_vote','crew_eject','imp_vote','imp_eject');
    foreach ($metrics as $metric) {
        $class = '';
        $mData = $game->getMost($metric);
        if ($metric == "crew_vote" || $metric == "crew_eject") $class = "class='badCrew'";
        if ($metric == "imp_vote" || $metric == "imp_eject") $class = "class='goodCrew'";
        if ($metric == "kills") $class = "class='murder'";
        $tpl2 .= "<tr {$class} ><td>".ucwords(str_replace("_"," ",$metric))."</td>";
        $tpl2 .= "<td>".$mData->player->name."</td>";
        $tpl2 .= "<td>".$mData->count."</td></tr>";
    }
} catch (\Exceptions\ItemNotFound $e){}

$tpl2 .= "</table>";
    $sql = "select players.name, sum(crew_eject * -2) as crew_eject, sum(crew_vote * -1) as crew_vote, sum(imp_vote * 1) as imp_vote, sum(imp_eject * 3) as imp_eject, sum((crew_eject * -2) + (crew_vote * -1)) as voting_lost, sum((imp_vote *1) + (imp_eject *3)) as points_gained, sum((crew_eject * -2) + (crew_vote * -1) +(imp_vote *1) + (imp_eject *3)) as total_vote_points from voting join players on players.id = player_id where game_id = ".$_REQUEST['tid']."  group by player_id;";
    $data = \Tournaments\Round::_LoadBySQL($sql);
    $table = "\n<table class='table' id='votePoints'>\n\t<thead>\n\t\t<th>Player</th>\n\t\t<th>Crew Ejection Loss</th>\n\t\t<th>Crew Vote Loss</th>";
    $table .= "\n\t\t<th>Imp Vote Gain</th>";
    $table .= "\n\t\t<th>Imp Eject Gain</th>";
    $table .= "\n\t\t<th>Total Loss</th>";
    $table .= "\n\t\t<th>Total Gain</th>";
    $table .= "\n\t\t<th>Adjusted Points</th>";
    $table .= "\n\t</thead>\n\t<tbody>";
    foreach($data as $info) {
      $table .= "\n\t\t<tr>\n\t\t\t<td>".$info->name."</td>\n\t\t\t<td>".$info->crew_eject."</td>\n\t\t\t<td>".$info->crew_vote."</td>";
      $table .= "\n\t\t\t<td>".$info->imp_vote."</td>";
      $table .= "\n\t\t\t<td>".$info->imp_eject."</td>";
      $table .= "\n\t\t\t<td>".$info->voting_lost."</td>";
      $table .= "\n\t\t\t<td>".$info->points_gained."</td>";
      $table .= "\n\t\t\t<td>".$info->total_vote_points."</td>";

      $table .= "\n\t\t</tr>";
    }
    $table .= "\n\t</tbody>\n</table>";
    $tpl2 .= collapse('View Voting Breakdown',$table);


$tpl .= $tpl2;

$tpl .= \Site\HTMLTools::tSort("votePoints", 1,'desc',0,0,1);

$__output = $tpl;
