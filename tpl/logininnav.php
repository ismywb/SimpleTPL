<?php
$tpl = '';
if (\Login\User::isLoggedIn()) {
$tpl = <<<end
          <li class="nav-item">
            <a class="nav-link" href="mapRotation.html">Generate Rotation</a>
          </li>
end;
}
die('123');
$__output = $tpl;