<?php
lRequired(4);
/*
if (!\Login\User::isLoggedIn()) {
    $__output = '<h1>403</h1>';
    return;
}
*/


if (isset($_GET['add'])) {
    try {
        $player = new \Players\Player($_GET['pid']);
        if ($_GET['add'] == 1) $player->first = $player->first + 1;
        if ($_GET['add'] == 2) $player->second = $player->second + 1;
        if ($_GET['add'] == 3) $player->third = $player->third + 1;
        $player->Save();
        header("Location: /addWin.html");
        die();
    } catch (\Exceptions\ItemNotFound $e) {}
}

if (isset($_GET['del'])) {
    try {
        $player = new \Players\Player($_GET['pid']);
        if ($_GET['del'] == 1) $player->first = $player->first - 1;
        if ($_GET['del'] == 2) $player->second = $player->second - 1;
        if ($_GET['del'] == 3) $player->third = $player->third - 1;
        $player->Save();
        header("Location: /addWin.html");
        die();
    } catch (\Exceptions\ItemNotFound $e) {}
}

// First we want to loop over all players!
$players = \Players\Player::LoadBy();
$tpl = <<<end
<table id='podium' class="table">
  <thead>
    <tr>
      <th scope="col">Nick</th>
      <th scope="col">First</th>
      <th scope="col">Second</th>
      <th scope="col">Third</th>
    </tr>
  </thead>
  <tbody>
end;
foreach ($players as $player) {
    $tpl .= '<tr>
		<td>'.$player->name.'</td>
		<td>
			<a href="./addWin.html?pid='.$player->id.'&add=1">Add First ('.$player->first.')</a>
			 (<a href="./addWin.html?pid='.$player->id.'&del=1">Del</a>)
		</td>
		<td>
			<a href="./addWin.html?pid='.$player->id.'&add=2">Add Second ('.$player->second.')</a>
			 (<a href="./addWin.html?pid='.$player->id.'&del=2">Del</a>)</td>
		<td>
			<a href="./addWin.html?pid='.$player->id.'&add=3">Add Third ('.$player->third.')</a>
			 (<a href="./addWin.html?pid='.$player->id.'&del=3">Del</a>)
		</td></tr>';
}
$tpl .= '</table>';
$tpl .= "\t\t".'<script type="text/javascript">'."\n\t\t\t".'$(document).ready(function() {'."\n\t\t\t\t".'$("#podium").DataTable({'."\n\t\t\t\t".'    "paging": true,'."\n\t\t\t\t\t".'"searching": true,'."\n\t\t\t\t\t".'"order": [[ 0, "asc" ]]'."\n\t\t\t\t".'});'."\n\t\t\t".'});'."\n\t\t".'</script>'."\n";

$__output = $tpl; //'<pre>'.print_r($players,1).'</pre>';
