<?php
lRequired(10);
$filen = str_replace('.','_',$_GET['file']);
$filen = str_replace('/','-',$filen);
$file = basePath.'tpl/'.$filen;
if (file_exists($file.'.php')) {
highlight_file($file.'.php');
die;
}
