<?php
$err = "";
if (isset($_POST["pack"])) {

  if (empty($_POST['name'])) {
    $err = "Name is empty!";
    define('ERR',1);
  }

$role = new \Mods\Role($_POST['rid']);
$role->name = $_POST['name'];
$role->mod_id = $_POST['pack'];
$role->crew_type = $_POST['roleType'];
$role->hex = $_POST['color'];
$role->Save();
$err = "Role Edited Successfully (".$_REQUEST['rid'].")!";

}
$role2 = new \Mods\Role($_REQUEST['rid']);
$impSel = $crewSel = $nSel = "";
if ($role2->crew_type == 1) $crewSel = "selected='true' ";
elseif ($role2->crew_type == 2) $nSel = "selected='true' ";
elseif ($role2->crew_type == 0) $impSel = "selected='true' ";

$modPackDropdown = \Mods\Mod::getPackDropDown($role2->mod_id);
   $tpl = <<<end
    {$err}
<form action="/editRole.html" method="post"><input type="hidden" name="rid" value="{$_REQUEST['rid']}" />
  <div class="form-group">
    <label for="username">Mod Pack</label>
<select class="form-control form-control-lg" name="pack">	{$modPackDropdown}</select>
  </div>
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" value="{$role2->name}" name="name">
  </div>

  <div class="form-group">
    <label for="color">Role Color</label>
    <input type="color" class="form-control" value="{$role2->hex}" id="color" name="color">
  </div>

  <div class="form-group">
    <label for="color">Role Type</label>
    <select class="form-control form-control-lg" name="roleType"><option {$cSel} value="1">Creamate</option><option {$impSel} value="0">Impostor</option><option {$nSel} value="2">Neutral</option></select>
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>
</form><form action="viewRoles.html" method="get"><input type="hidden" name="mid" value="{$role2->mod_id}">
  <button type="submit" class="btn btn-primary">Cancel and Go Back to Mod Pack</button>

</form>
end;

$__output = $tpl;
