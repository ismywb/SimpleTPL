<?php
lRequired();

if (isset($_POST['name'])) {
	$mod = new \Mods\Mod();
	$mod->name = $_POST['name'];
	$mod->Save();
	define('CREATED',1);
}

$err = '';
if (defined('CREATED')) $err = "Modpack Successfully created!";
$tpl = <<<end
{$err}
<form action="/createMod.html" method="post">
  <div class="form-group">
  <input type="hidden" name="create" value="1" />
          <label for="date">Mod Name</label>
    <input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp" placeholder="Enter Name">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

end;
$__output = $tpl;
