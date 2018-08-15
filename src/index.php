<?php

require('IntervalService.php');

echo "Example 1 \r\n";
$example1 = [
    '1-10:15',
    '5-20:15',
    '2-8:45',
    '9-10:45',
];
$service = new  IntervalService();
foreach ($example1 as $v) {
    echo $service->add($v)->getResult()." \r\n";
}

echo "Example 2 \r\n";
$example2 = [
    '1-5:15',
    '20-25:15',
    '4-21:45',
    '3-21:15',
];
$service = new  IntervalService();
foreach ($example2 as $v) {
    echo $service->add($v)->getResult(). " \r\n";
}

