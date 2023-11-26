<?php
lRequired();
$user = \Login\User::getCurrent();
$__output = "Sorry {$user->username}, you do not have the required access for this page!";
