<?php
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
$metrics = array('kills','crew_vote','crew_eject','imp_vote','imp_eject');
foreach ($metrics as $metric) {
    $class = '';
    if ($metric == "crew_vote" || $metric == "crew_eject") $class = "class='badCrew'";
    if ($metric == "imp_vote" || $metric == "imp_eject") $class = "class='goodCrew'";
    if ($metric == "kills") $class = "class='murder'";
    $mData = \Tournaments\Tournament::getMostAllTime($metric);
    $tpl2 .= "<tr {$class} ><td>".ucwords(str_replace("_"," ",$metric))."</td>";
    $tpl2 .= "<td>".$mData->player->name."</td>";
    $tpl2 .= "<td>".$mData->count."</td></tr>";
}
$tpl2 .= "</table>";

    $sql = "select players.name, sum(crew_eject * -2) as crew_eject, sum(crew_vote * -1) as crew_vote, sum(imp_vote * 1) as imp_vote, sum(imp_eject * 3) as imp_eject, sum((crew_eject * -2) + (crew_vote * -1)) as voting_lost, sum((imp_vote *1) + (imp_eject *3)) as points_gained, sum((crew_eject * -2) + (crew_vote * -1) +(imp_vote *1) + (imp_eject *3)) as total_vote_points from voting join players on players.id = player_id  group by player_id;";
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



$tpl2 .= \Site\HTMLTools::tSort("votePoints", 1,'desc',1,0,1);

$__output = $tpl2;
