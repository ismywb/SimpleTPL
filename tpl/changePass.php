<?php
if (!\Login\User::isLoggedIn()) {
    $__output = '<h1>403</h1>';
    return;
}

if (isset($_POST['pass'])) {
    if ($_POST['pass'] != $_POST['pass2']) {
        define('ERR','Your new password did not match!');
    } else {
        $user = \Login\User::getCurrent();
        $user->pass = md5($_POST['pass']);
        $user->Save();
        define('ERR','Password has been updated! You must now login again!');
    }
}
if (!defined('ERR')) define('ERR','');
$user = \Login\User::getCurrent();
$err = ERR;
$tpl = '<h1>Welcome '.ucwords($user->username).'!</h1>';
   $tpl .= <<<end
    {$err}
<form action="/changePass.html" method="post">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" disabled="disabled" id="username" name="username" aria-describedby="emailHelp" value="{$user->username}">
  </div>
  <div class="form-group">
    <label for="pass">New Password</label>
    <input type="password" class="form-control" id="pass" name="pass" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="pass2">New Password</label>
    <input type="password" class="form-control" id="pass2" name="pass2" placeholder="Confirm Password">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
end;
$__output = $tpl;