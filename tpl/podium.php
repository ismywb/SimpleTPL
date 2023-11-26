<?php
$tpl = "\n\t\t<table id='podium' class='table table-hover'>\n\t\t\t<thead>\n\t\t\t\t<tr>\n\t\t\t\t\t<th scope='col'>Player</th>\n\t\t\t\t\t<th scope='col'>First Place Wins</th>\n\t\t\t\t\t<th scope='col'>Second Place Wins</th>\n\t\t\t\t\t<th scope='col'>Third Place Wins</th>\n\t\t\t\t</tr>\n\t\t\t</thead>\n\t\t\t<tbody>";
$winners = \Tournaments\Podium::getPlayers();
foreach ($winners as $winner) {
    $tpl .= "\n\t\t\t\t<tr>\n\t\t\t\t\t<th scope='row'>".$winner->name."</th>\n\t\t\t\t\t<td>".$winner->first()."</td>\n\t\t\t\t\t<td>".$winner->second()."</td>\n\t\t\t\t\t<td>".$winner->third()."</td>\n\t\t\t\t</tr>";
}
$tpl .= "\n\t\t\t</tbody>\n\t\t</table>\n";
$tpl .= "\t\t".'<script type="text/javascript">'."\n\t\t\t".'$(document).ready(function() {'."\n\t\t\t\t".'$("#podium").DataTable({'."\n\t\t\t\t".'    "paging": false,'."\n\t\t\t\t\t".'"searching": false,'."\n\t\t\t\t\t".'"order": [[ 1, "desc" ]]'."\n\t\t\t\t".'});'."\n\t\t\t".'});'."\n\t\t".'</script>'."\n";
$__output = $tpl;