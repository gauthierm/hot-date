<?php

set_include_path('/so/packages/hot-date/work-gauthierm');

require_once 'HotDate/HotDateTime.php';

$interval = new HotDateInterval('P2D');
$date1 = new HotDateTime('20100603T123456');
$date2 = clone $date1;
$date2->add($interval);
$date3 = clone $date2;
$date3->sub($interval);
$date4 = clone $date3;
$date4->setTimestamp($date3->getTimestamp());

echo $date1->format('c'), "\n";
echo $date2->format('c'), "\n";
echo $date3->format('c'), "\n";
echo $date4->format('c'), "\n";

echo "\n";
echo unserialize(serialize($date4)), "\n";
echo "\n";

$date5 = new HotDateTime('20031101T184212');
echo $date1, "\n";
echo $date5, "\n";
$interval = $date1->diff($date5);
var_dump($interval);
