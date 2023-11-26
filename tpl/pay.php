<?php
$hours = $_GET['h'];
$rate = 11.75;
$ot = ($hours - 40);
$gross = 0;
if ($ot > 0) {
  $hours = $hours - $ot;
  $gross = (($rate * 1.5) * $ot) + 
($hours * $rate);
} else 
  $gross = $hours * $rate;
// gross
$fed = 0.08756892;
$ftax = round($gross * $fed,2);


$ss = 0.062005012531328;
$srate = round($gross * $ss,2);

$med = 	0.014511278195489;
$mrate = round($gross * $med,2);

$state = 0.032305764411028;
$strate = round($gross * $state);

$atax = (($srate + $mrate + 
$strate + $ftax));		
$tpl = "<h1>Pay info:</h1><p>Hours: 
$hours</p>";
$tpl .= "<p>Rate: $rate</p>";
$tpl .= "<p>Gross: $gross</p>";
$tpl .= "<p>SS: $srate</p>";
$tpl .= "<p>Fed: $ftax</p>";
$tpl .= "<p>State: $strate</p>";
$tpl .= "<p>Med: $mrate</p>";
$tpl .= "<p>Total Taxes: $atax</p>";
$net1 = $gross - $atax;
$cs = min(($net1*.5),154.89);
$net = $net1 - $cs;
$tpl .= "<p>Net: $net1</p>";
$tpl .= "<p>Cs: $cs</p>";
$tpl .= "<p>Take home: $net</p>";
$__output = $tpl;
//die('<h1>'.$gross.'<br>'.$ftax.'<br>'.$srate);
