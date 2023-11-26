<?php
$tpl = '';
if (isset($_POST['username'])) {
    if (\Login\User::login($_POST['username'],$_POST['password'])) {
        header("Location: /index.html");
        die;
    } else {
        header("Location: /login.html?err=1");
        die;
    }
} else {
    $err = '';
    if (isset($_GET['err'])) {
        $err = '<h3>Login Failed!</h3>';
    }
    $tpl = <<<end
    {$err}
<form action="/login.html" method="post">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp" placeholder="Enter email">
  </div>
  <div class="form-group">
    <label for="pass">Password</label>
    <input type="password" class="form-control" id="pass" name="password" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
end;
}
$__output = $tpl;