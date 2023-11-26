<?php
lRequired();
$err = "";
if (isset($_POST["pack"])) {

  if (empty($_POST['name'])) {
    $err = "Name is empty!";
    define('ERR',1);
  }

$role = new \Mods\Role();
$role->name = $_POST['name'];
$role->mod_id = $_POST['pack'];
$role->crew_type = $_POST['roleType'];
$role->hex = $_POST['color'];
$role->Save();
$err = "Role Created Successfully!";
}
$color = "ff8e8e";
$modPackDropdown = \Mods\Mod::getPackDropDown();
   $tpl = <<<end
    {$err}
<form action="/createRole.html" method="post">
  <div class="form-group">
    <label for="username">Mod Pack</label>
<select class="form-control form-control-lg" name="pack">	{$modPackDropdown}</select>
  </div>
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name">
  </div>

  <div class="form-group">
    <label for="color">Role Color</label>
    <input type="color" class="form-control" value="#{$color}" id="color" name="color">
  </div>

  <div class="form-group">
    <label for="color">Role Type</label>
    <select class="form-control form-control-lg" name="roleType"><option value="1">Crewmate</option><option value="0">Impostor</option><option value="2">Neutral</option></select>
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>

</form>
end;

$__output = $tpl;
