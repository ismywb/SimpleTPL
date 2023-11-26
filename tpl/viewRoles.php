<?php
$mod = new \Mods\Mod($_GET['mid']);
$name = $mod->name;
function getRoleList($type = -1) {
  $randID = rand(1000000,9999999);
  $packs = \Mods\Mod::getRoles($_GET['mid'],$type);
  $tpl .= <<<end
<table id="viewT{$randID}" class="table">
  <thead>
    <tr>
      <th scope="col">Role ID</th>
      <th scope="col">Role Name</th>
      <th scope="col">Role Team</th>
      <th scope="col">Role Color</th>
      <th scope="col">Edit Role</th>

    </tr>
    </thead>
    <tbody>
end;

  try {
      foreach ($packs as $t) {
  	$team = "Crewmate";
  	if ($t->crew_type == 2) $team = "Neutral";
 	elseif ($t->crew_type == 0) $team = "Impostor";
        $tpl  .= <<<end
        <tr style="background-color: {$t->hex} !important;">
          <td>{$t->id}</td>
          <td>{$t->name}</td>
          <td>{$team}</td>
          <td style="text-transform: uppercase;">{$t->hex}</td>
          <td style="background-color: white !important;"><a href="/editRole.html?rid={$t->id}">Edit Role</a></td>
        </tr>
end;
    }
} catch  (\Exceptions\ItemNotFound $e) {}
$tpl .= "</tbody></table>";
$tpl .= \Site\HTMLTools::tSort("viewT{$randID}",2);
return $tpl;
}
$imps = getRoleList(0);
$crew = getRoleList(1);
$neutral = getRoleList(2);

$tpl = <<<end
<center><h1>{$name}</h1></center>
<h2>Impostors</h2>
{$imps}
<h2>Crewmates</h2>
{$crew}
<h2>Neutrals</h2>
{$neutral}
end;
$__output = $tpl;

