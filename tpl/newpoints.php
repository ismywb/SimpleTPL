<?php
$tpl = '<h1>Voting Points</h1><table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">&nbsp;</th>
      <th scope="col">Imposter</th>
      <th scope="col">Crew</th>
      <th scope="col">Neutral</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Vote Crewmate</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$CrewVote.'</td>
      <td>0</td>
    </tr>
    <tr>
      <th scope="row">Eject Crewmate</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$CrewEjection.'</td>
      <td>0</td>
    </tr>
    <tr>
      <th scope="row">Vote Imposter</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$ImpVote.'</td>
      <td>'.\Tournaments\Round::$ImpVote.'</td>
    </tr>
    <tr>
      <th scope="row">Eject Imposter</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$ImpEjection.'</td>
      <td>'.\Tournaments\Round::$ImpEjection.'</td>
    </tr>
    <tr>
      <th scope="row">Kill</th>
      <td>'.\Tournaments\Round::$ImpKillPoints.'</td>
      <td>'.\Tournaments\Round::$ImpKillPoints.'</td>
      <td>'.\Tournaments\Round::$ImpKillPoints.'</td>
    </tr>
  </tbody>
</table>
<h1>Win Points</h1><table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">&nbsp;</th>
      <th scope="col">Impostor + Neutral (Killing)</th>
      <th scope="col">Crew</th>
      <th scope="col">Nutral (Non-Killing)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Game Win</th>
      <td>'.\Tournaments\Round::$ImpWinPoints.'</td>
      <td>'.\Tournaments\Round::$CrewWin.'</td>
      <td>'.\Tournaments\Round::$CrewWin.'</td>
    </tr>
    <tr>
      <th scope="row">Sabatoge Win</th>
      <td>'.\Tournaments\Round::$ImpSaboBonus.'</td>
      <td>0</td>
      <td>0</td>
    </tr>
    <tr>
      <th scope="row">Task Win</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$CrewTaskWin.'</td>
      <td>0</td>
    </tr>
    <tr>
      <th scope="row">Tasks Done</th>
      <td>0</td>
      <td>'.\Tournaments\Round::$crewTasksDone.'</td>
      <td>0</td>
    </tr>
  </tbody>
</table>';

try {
  $data = \Points\Cats::LoadBy();
  foreach($data as $cat) {
    $data2 = array();
    try {
      $data2 = \Points\Points::LoadBy(array("cid"=>$cat->id));
 //\Tournaments\Round::_LoadBySQL("select * from pointrows where cid = '".$cat->id."'");
    } catch (\Exceptions\ItemNotFound $e) {}
    $delete = '';
    $tpl .= "<h1>{$delete}{$cat->name}</h1>";
    if (count($data2) >0) {
    $tpl .= <<<end
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">Role</th>
      <th scope="col">Info</th>
    </tr>
  </thead>
  <tbody>
end;
    foreach ($data2 as $row) {
      $tpl .= '    <tr>
      <td scope="row">'.$row->name.'</td>
      <td>'.$row->info.'</td>
    </tr>';
    }
    // LOAD VALUES
    $tpl .= "</tbody></table>";
  }


}

} catch (\Exceptions\ItemNotFound $e) {}


$__output = $tpl;
