#!/usr/bin/env php
<?php

require "../application/console.php";


// ------------------------------------------------

$kwds = array();
$ktot = 0;
$ksum = 0;

$query = "SELECT keywords FROM model_aggregator_feed_article";

foreach(B_Model::select($query, array(), PDO::FETCH_OBJ) as $a)
{
    foreach(explode(" ", $a->keywords) as $k)
    {
        array_key_exists($k, $kwds) ? $kwds[$k]++ : $kwds[$k] = 1;
        $ksum++;
    }
    $ktot++;
}

arsort($kwds);

$fmin = round($ktot * 0.2);

// ------------------------------------------------

$zipf = array();

foreach(array_values($kwds) as $f)
{
    if($f >= $fmin)
    {
        array_key_exists($f, $zipf) ? $zipf[$f]++ : $zipf[$f] = 1;
    }
}

krsort($zipf);

$fmax = round(max(array_keys($zipf)) * 0.9);

// ------------------------------------------------

print_r($zipf);

echo "ktot = " . $ktot . "\n";
echo "fmin = " . $fmin . "\n";
echo "fmax = " . $fmax . "\n";
echo "ksum = " . $ksum . "\n";

// ------------------------------------------------

$kout = array();

foreach($kwds as $k => $f)
{
    if($f >= $fmin && $f <= $fmax)
    {
        $kout[] = $k;
    }
}

print_r($kout);
