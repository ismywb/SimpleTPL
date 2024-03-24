<?php
lRequired();

if ($_REQUEST['do'] == 'toggle') {

    
    if ($_REQUEST['on'] == 'public') {
       try {
           $t =  new \Tournaments\Tournament($_REQUEST['tid']);
           $t->public = !$t->public;
           $t->Save();
       } catch (\Exceptions\ItemNotFound $e) {}
    }
    header("Location: ./manageT.html");
    die;
}

if (isset($_REQUEST['create'])) {
    $t = new \Tournaments\Tournament();
    $t->name = $_REQUEST['name'];
    $t->date = $_REQUEST['date'];
    $t->Save();
}
$tpl .= <<<end
<table id="tournaments" class="table">
  <thead>
    <tr>
      <th scope="col">Tournament Number</th>
      <th scope="col">Tournament Name</th>
      <th scope="col">Date</th>
      <th scope="col">Public</th>
      <th scope="col">Rounds</th>
    </tr>
    </thead>
    <tbody>
end;
try {
$tournaments = \Tournaments\Tournament::LoadBy();
foreach ($tournaments as $t) {
$data = explode("/",$t->date);
$date = $data[2].'/'.$data[0].'/'.$data[1];

    $tpl  .= <<<end
    <tr>
      <td><a href="./modify.html?tid={$t->id}&flag=removeT&confirm=0"><img src="/img/redminus.png" class="icon" /></a> {$t->id}</td>
      <td>{$t->name}</td>
      <td>{$date}</td>
      <td><a href="./manageT.html?tid={$t->id}&do=toggle&on=public">{$t->isPublic()}</a></td>
      <td><a href="./round.html?tid={$t->id}">Rounds</a></td>
    </tr>
end;
}
} catch (\Exceptions\ItemNotFound $e) {}
$tpl .= "</tbody></table>";
$tpl .= <<<end
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Add Tournament
  </a>
</p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">
end;
$tDate = date("m/d/Y");
    $tpl .= <<<end
<form action="./manageT.html" method="post">
  <div class="form-group">
  <input type="hidden" name="create" value="1" />
          <label for="date">Date</label>

        <input name="date" id="datepicker" width="276" />
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            showOnFocus: true,
             value: '{$tDate}'
        });
    </script>

    <input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp" placeholder="Enter Name">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
end;
$tpl .= \Site\HTMLTools::tSort("tournaments",2);
$tpl .= <<<end
</tbody></table>
  </div>
</div>
end;

$__output = $tpl;
