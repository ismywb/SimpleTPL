<?php

if (!\Login\User::isLoggedIn()) {
    $__output = '<h1>403</h1>';
    return;
}

$tpl = '';
if (!empty($_POST['nick']) && !empty($_POST['tag'])) {
  define('SUCCESS',1);   
  $player = new \Players\Player();
  $player->name = $_POST['nick'];
  $player->discord_tag = $_POST['tag'];
  $player->Save();
} else {
    define('SUCCESS',0);
}
    $err = '';
    if (!SUCCESS && isset($_POST['nick'])) {
        $err = '<h3>User Add Failed!</h3>';
    }
    if (SUCCESS) $err = 'User Added Successfully!';
    $tpl = <<<end
    {$err}
<form action="/newplayer.html" method="post">
  <div class="form-group">
    <label for="nick">Display Name</label>
    <input type="text" class="form-control" id="nick" name="nick" aria-describedby="emailHelp" placeholder="Enter Display Name">
  </div>
  <div class="form-group">
    <label for="tag">Discord Tag</label>
    <input type="text" class="form-control" id="tag" name="tag" aria-describedby="emailHelp" placeholder="Enter Discord Tag">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
end;

$__output = $tpl;