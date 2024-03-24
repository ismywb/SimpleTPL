<?php
lRequired(0);

if (isset($_GET['flag'])) {
define('redir',1);
  if ($_GET['flag'] == "del") {
    $point = new \Points\Points($_GET['id']);
    $point->Delete();
  }

  if ($_GET['flag'] == "delcat") {
    $cat = new \Points\Cats($_GET['id']);
    $cat->Delete();
  }

}

if (isset($_POST['create'])) {
define('redir',1);

  if ($_POST['create'] == "cat") {
    $cat = new \Points\Cats();
    $cat->name = $_POST['name'];
    $cat->Save();
  }

  if ($_POST['create'] == "role") {
    $cat = new \Points\Points();
    $cat->name = $_POST['name'];
    $cat->cid = $_POST['cid'];
    $cat->info = $_POST['info'];
    $cat->Save();
  }

}
if (defined('redir')) {
define('AJAX',1);
header("Location: /managepoints.html");
die;
}
$tpl = '';
try {
  $data = \Points\Cats::LoadBy();
  foreach($data as $cat) {
    $data2 = array();
    try {
      $data2 = \Points\Points::LoadBy(array("cid"=>$cat->id));
 //\Tournaments\Round::_LoadBySQL("select * from pointrows where cid = '".$cat->id."'");
    } catch (\Exceptions\ItemNotFound $e) {}
    $delete = '<a href="/managepoints.html?id='.$cat->id.'&flag=delcat"><img src="/img/redminus.png" class="icon" /></a>';
    if (count($data2) > 0) $delete = '';
    $tpl .= "<h1>{$delete}{$cat->name}</h1>";
    if (count($data2) >0) {
    $tpl .= <<<end
<table class="table table-bordered">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th scope="col">Role</th>
      <th scope="col">Info</th>
    </tr>
  </thead>
  <tbody>
end;
    foreach ($data2 as $row) {
      $tpl .= '    <tr><td><center>
<a href="/managepoints.html?id='.$row->id.'&flag=del"><img src="/img/redminus.png" class="icon" /></a></center></td>
      <td scope="row">'.$row->name.'</td>
      <td>'.$row->info.'</td>
    </tr>';
    }
    // LOAD VALUES
    $tpl .= "</tbody></table>";
  }

$tpl .= <<<end
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#addRole{$cat->id}" role="button" aria-expanded="false" aria-controls="addRole{$cat->id}">
    Add Role
  </a>
</p>
<div class="collapse" id="addRole{$cat->id}">
  <div class="card card-body">
<form action="/managepoints.html" method="post">
  <div class="form-group">
  <input type="hidden" name="create" value="role" />
  <input type="hidden" name="cid" value="{$cat->id}" />
    <input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp" placeholder="Enter Name">
    <input type="text" class="form-control" id="info" name="info" aria-describedby="emailHelp" placeholder="Enter Point Info">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form></div></div>
end;

}
} catch (\Exceptions\ItemNotFound $e) {}

$tpl .= <<<end
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Add Category
  </a>
</p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">
<form action="/managepoints.html" method="post">
  <div class="form-group">
  <input type="hidden" name="create" value="cat" />
    <input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp" placeholder="Enter Name">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form></div></div>

<h1><a href="/newpoints.html">See points page</a></h1>
end;


$__output = $tpl;
