<?php
lRequired();
//define('AJAX',true);
if (!isset($_REQUEST['round'])) {
    $_REQUEST['round'] = 1;   
}
$_GET['round'] = $_REQUEST['round'];
$tpl = '';
/** */

//$__output = "Info for Tournament ID {$_REQUEST['tid']}!";
$game = new \Tournaments\Tournament($_REQUEST['tid']);

$data = array(
    'game_id'=> $_REQUEST['tid'],
    'round' => $_REQUEST['round']
    );
try {
$round = \Tournaments\Round::LoadBy($data);


$match = \Tournaments\Match::LoadBy(array('tid'=>$data['game_id'],'round'=>$data['round']))[0];
//$tpl  = '<pre>'.print_r($game,1).print_r($match,1).'<br />test<br /></pre>';
$pack = \Mods\Mod::getMod($game->modpack);
$packs = \Mods\Mod::getMods();
$dropdown = <<<end
<form action="tModify.html" action="get">
<input type="hidden" name="tid" value="{$data['game_id']}" />
<input type="hidden" name="round" value="{$_GET['round']}" />
<select name="modpack" onchange="this.form.submit()">
end;
foreach($packs as $mod) {
$current = "";
if ($mod->name == $pack) $current = " selected='yes'";
$dropdown .= "<option value='".$mod->id."' {$current} >{$mod->name}</option>";
}
$dropdown .= "</select>";
$tpl .= <<<end
<h1 class="display-3">{$game->name}</h1>
<h1 class="display-4">ModPack: {$dropdown}</h1>
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
    $tpl .= '      <td scope="col"><img src="/img/yes.png" class="icon" /></td>';
    if ($match->is_task_win == 1) {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&taskWin=0&do=win"><img src="/img/yes.png" class="icon" /></a></td>';
    } else {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&taskWin=1&do=win"><img src="/img/none.png" class="icon" /></a></td>';
    }
} else {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&crewWin=1&do=win"><img src="/img/none.png" class="icon" /></a></td><td><img src="/img/none.png" class="icon" /></td>';
}
if ($match->is_crew_win == 0) {
    $tpl .= '      <td scope="col"><img src="/img/yes.png" class="icon" /></td>';
    if ($match->is_sabo_win == 1) {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&saboWin=0&do=win"><img src="/img/yes.png" class="icon" /></a></td>';
    } else {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&saboWin=1&do=win"><img src="/img/none.png" class="icon" /></a></td>';
    }
} else {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&crewWin=0&do=win"><img src="/img/none.png" class="icon" /></a></td><td><img src="/img/none.png" class="icon" /></td>';
}
if ($match->is_crew_win == 2) {
    $tpl .= '      <td scope="col"><img src="/img/yes.png" class="icon" /></td>';
} else {
        $tpl .= '      <td scope="col"><a href="./modify.html?tid='.$_REQUEST['tid'].'&round='.$_REQUEST['round'].'&crewWin=2&do=win"><img src="/img/none.png" class="icon" /></a></td>';
}
$tpl .= '</tr></table>';
$tpl .= <<<end
<table class="table" id="voting">
  <thead>
    <tr>
      <th scope="col">&nbsp;</th>
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
  <tbody></form>
end;
$i = 0;
$txt = array();
 foreach ($round as $r) {
     $i++;
     $r->populatePlayer();
     $r2 = print_r($r,1);
     $class = 'crew';
     if ($r->isImp(0)) {
         $class = 'imp';
     } elseif ($r->isNeutral(0)) {
         $class = 'neutral';
     }
     
     if ($_REQUEST['round'] == 10) {
         $total = $game->getFinalPoints($r->player_id->id,$data['round']);
         if (!isset($txt[$total])) {
             $txt[$total] = array();
         }
         $txt[$total][] = (object)array('tag'=>$r->player_id->discord_tag,'pts'=>$total);
     }
    $role = \Mods\Mod::getRole($game->modpack,$r->role);
    $roles = \Mods\Mod::getRoles($game->modpack);
    $drop = <<<end

<form method="get" action="changeRole.html">
<input type="hidden" name="round" value="{$_GET['round']}" /><input type="hidden" name="tid" value="{$_GET['tid']}" />
<input type="hidden" name="player" value="{$r->player_id->id}" />
<select  onchange="this.form.submit()" name="role">
end;
    $drop .= \Mods\Mod::getDropdown($game->modpack,$r->role)."</select></form>";
    $tpl .= <<<end

    <tr class="is-{$class}	" style="background-color: {$role["hex"]} !important;">
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&flag=remove&pid={$r->player_id->id}&confirm=0&round={$_REQUEST['round']}"><img src="/img/redminus.png" class="icon" /></a> ${i}.</td>
      <td scope="col">{$drop}</td>
      <td scope="col">{$r->player_id->name}</td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=kills&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->kills}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=kills&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=ieject&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->imp_eject}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=ieject&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=ivote&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->imp_vote}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=ivote&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=ceject&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->crew_eject}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=ceject&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=cvote&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->crew_vote}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=cvote&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=toggle&flag=iscrew&player={$r->player_id->id}">{$r->isCrew()}</a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=toggle&flag=isneutral&player={$r->player_id->id}">{$r->isNeutral()}</a></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=minus&flag=extrapoints&player={$r->player_id->id}"><img class='icon' src="/img/minus.png" /></a>&nbsp;{$r->extra_points}&nbsp;<a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=plus&flag=extrapoints&player={$r->player_id->id}"><img class='icon' src="/img/plus.png" /></a><br /><code style=" background-color: black; color:  white; ">{$r->extra_points_why}</code>

      <p>
  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample{$r->player_id->id}" role="button" aria-expanded="false" aria-controls="collapseExample{$r->player_id->id}">
    Change
  </a>
</p>
<div class="collapse" id="collapseExample{$r->player_id->id}">
  <div class="card card-body"><form action="modify.html" method="get"><input type="hidden" name="tid" value="{$_REQUEST['tid']}" /><input type="hidden" name="round" value="{$_REQUEST['round']}" /><input type="hidden" name="player" value="{$r->player_id->id}" />
  <input type="hidden" name="do" value="editnote" /><input type="text" name="why" value="{$r->extra_points_why}" /></form>
    </div>
</div></td>
      <td scope="col"><a href="./modify.html?tid={$_REQUEST['tid']}&round={$_REQUEST['round']}&do=toggle&flag=tdone&player={$r->player_id->id}">{$r->tasksDone()}</a></td>
      <td scope="col">{$r->getPoints($data['game_id'],$data['round'])}</td>
      <td scope="col">{$game->getFinalPoints($r->player_id->id,$data['round'])}</td>
    </tr>
end;
    //$__output .= '<pre>'.print_r($r,1).'<br />'.$r->player_id->discord_tag.'<br />'.$r->getPoints($data['game_id'],$data['round']).'</pre><hr>';
}
krsort($txt);
$t2 = <<<end
***RANKINGS***

end;

$count = 0;
$rankings = array();
foreach($txt as $place) {
       $count++;
        if (is_array($place)) {
            $rankings[$count][] =  $place;
        }

       $rankings[$count] =  $place;
}
krsort($rankings);
foreach($rankings as $place=>$players) {
    if ($place > 3) {
        $t2 .= "{$place}th: ";
    } elseif ($place == 3) {
        $t2 .= "{$place}rd: ";
    } elseif ($place == 2) {
        $t2 .= "{$place}nd: ";
    } elseif ($place == 1) {
        $t2 .= "{$place}st: ";
    }
    if (count($players) > 1) {
        $t2 .= '@'.$players[0]->tag;
        unset($players[0]);
        foreach ($players as $p) {
            $t2 .= " & @{$p->tag}";
        }
        $t2 .= " - (**{$players[1]->pts}**)\n";
    } else { 
        $t2 .= "@{$players[0]->tag} - (**{$players[0]->pts}**)\n";
    }
    
    
}
//echo code(print_r($game->getMost('imp_vote'),1));
//echo "<code>".$t2."</code>";//print_r($rankings,1)."</pre>";
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
} catch (\Exceptions\ItemNotFound $e) {}
$tpl .= <<<end
		<script type="text/javascript">
			$(document).ready(function() {
				$("#voting").DataTable({
				    "paging": false,
					"searching": true,
				    "stateSave": true,
					"order": [[ 2, "asc" ]]
				});
			});
		</script>
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item {$pdisable}">
      <a class="page-link" href="/round.html?tid={$_REQUEST['tid']}&round={$pround}" tabindex="-1">Previous</a>
    </li>
end;
$num = 1;
while ($num <= 10) {
    $disabled = '';
    $tid = $_REQUEST['tid'];
    if ($num == $_REQUEST['round'] ) $disabled = 'disabled';
    $tpl .= "    <li class='page-item {$disabled}'><a class='page-link ' href='/round.html?tid={$tid}&round={$num}'>{$num}</a></li>\n";
    $num++;
}
$nRound = min(10,($_REQUEST['round'] + 1));
$ndisable = '';
if ($data['round'] == 10) $ndisable = 'disabled';

$tpl .= <<<end
<li class="page-item {$ndisable}">
      <a class="page-link " href="/round.html?tid={$_REQUEST['tid']}&round={$nRound}">Next</a>
    </li>
  </ul>
</nav>
end;

$tpl .= <<<end
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Add Player
  </a>
</p>
end;
if ($_REQUEST['round'] == 10) {
    $tpl .= collapse('View Ranking Text',code($t2));
    $sql = "Select  sum(crew_vote) as crew_vote, sum(crew_eject) as crew_eject, players.name from voting join players on players.id = player_id where game_id = '".$_REQUEST['tid']."' group by player_id;";
    $data = \Tournaments\Round::_LoadBySQL($sql);
    $table = "\n<table class='table' id='tieBreaker'>\n\t<thead>\n\t\t<th>Player</th>\n\t\t<th>Crew Ejection</th>\n\t\t<th>Crew Vote</th>\n\t</thead>\n\t<tbody>";
    foreach($data as $info) {
      $table .= "\n\t\t<tr>\n\t\t\t<td>".$info->name."</td>\n\t\t\t<td>".$info->crew_eject."</td>\n\t\t\t<td>".$info->crew_vote."</td>\n\t\t</tr>";
    }
    $table .= "\n\t</tbody>\n</table>";
    $tpl .= collapse('View TieBreaking',$table);

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
    $tpl .= collapse('View Voting Breakdown',$table);


}


$tpl .= <<<end
<div class="collapse" id="collapseExample">
  <div class="card card-body">
<table class="table" id="players">
  <thead>
    <tr>
      <th scope="col">Player ID</th>
      <th scope="col">Player Name</th>
      <th scope="col">Player Tag</th>
      <th scope="col">Add Player</th>
    </tr>
    </thead>
    <tbody>
end;
$players = \Players\Player::LoadBy();
foreach ($players as $player) {
    $tpl .= <<<end
    <tr>
      <td>{$player->id}</td>
      <td>{$player->name}</td>
      <td>{$player->discord_tag}</td>
      <td><a href="/add.html?tid={$_REQUEST['tid']}&pid={$player->id}">Add Player</a></td>
    </tr>
end;
}
$tpl .= <<<end
</tbody></table>
  </div>
</div>
	<script type="text/javascript">
			$(document).ready(function() {
				$("#players").DataTable({
				    "paging": true,
				    "stateSave": true,
					"searching": true,
					"order": [[ 1, "asc" ]]
				});
			});
		</script>
end;
$tpl .= \Site\HTMLTools::tSort("tieBreaker", 1,'desc',0,0,1);
$tpl .= \Site\HTMLTools::tSort("votePoints", 1,'desc',0,0,1);
$tpl .= "<!--\n\n".print_r($game->getPlayers(),1)."\n\n-->";
$__output = $tpl;
