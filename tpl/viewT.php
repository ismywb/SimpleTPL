<?php
$tpl .= <<<end
<table id="viewT" class="table">
  <thead>
    <tr>
      <th scope="col">Tournament Number</th>
      <th scope="col">Tournament Name</th>
      <th scope="col">Date</th>
      <th scope="col">Rounds</th>
    </tr>
    </thead>
    <tbody>
end;

try {
    $tournaments = null;
    if (\Login\User::isLoggedIn()) {
        $tournaments = \Tournaments\Tournament::LoadBy();
    } else {
        $tournaments = \Tournaments\Tournament::LoadBy(array('public'=>1));
    }
    foreach ($tournaments as $t) {
$data = explode("/",$t->date);
$date = $data[2].'/'.$data[0].'/'.$data[1];

        $tpl  .= <<<end
        <tr>
          <td>{$t->id}</td>
          <td>{$t->name}</td>
          <td>{$date}</td>
          <td><a href="/viewtournament.html?tid={$t->id}">Rounds</a></td>
        </tr>
end;
    }
} catch  (\Exceptions\ItemNotFound $e) {}
$tpl .= "</tbody></table>";
$tpl .= \Site\HTMLTools::tSort("viewT",2);
$__output = $tpl;
