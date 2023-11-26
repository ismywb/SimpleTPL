<?php
$packs = \Mods\Mod::getMods();
$tpl .= <<<end
<table id="viewT" class="table">
  <thead>
    <tr>
      <th scope="col">ModPack ID</th>
      <th scope="col">ModPack Name</th>
      <th scope="col">View Roles</th>
    </tr>
    </thead>
    <tbody>
end;

try {
    foreach ($packs as $t) {
        $tpl  .= <<<end
        <tr>
          <td>{$t->id}</td>
          <td>{$t->name}</td>
          <td><a href="/viewRoles.html?mid={$t->id}">View Roles</a></td>
        </tr>
end;
    }
} catch  (\Exceptions\ItemNotFound $e) {}
$tpl .= "</tbody></table>";
$tpl .= \Site\HTMLTools::tSort("viewT",2);
$__output = $tpl;

