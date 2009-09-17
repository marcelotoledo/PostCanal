#!/usr/bin/env php
<?php

$options = getopt("l::p::u::w::h::");

if(array_key_exists('h', $options))
{
    printf("options are:\n");
    printf("%-20s%-20s\n", "l", "limit to l rows. default is 10");
    printf("%-20s%-20s\n", "p", "show only priority p. options are: N|W|E");
    printf("%-20s%-20s\n", "r", "reset log (delete base_log rows)");
    printf("%-20s%-20s\n", "u", "show only email (user) u");
    printf("%-20s%-20s\n", "w", "output width. default is 80");
    printf("%-20s%-20s\n", "h", "help");
    exit(0);
}

require "../application/console.php";

if(@$options['r']) { B_Model::execute("DELETE FROM base_log WHERE 1=1"); exit(1); }

$sql = "SELECT a.id, a.priority, a.message, CONCAT(b.login_email_local,'@',b.login_email_domain) AS login_email, DATE_FORMAT(a.created_at, '%y/%m/%d %H:%i:%s') as created_at FROM base_log AS a LEFT JOIN model_user_profile AS b ON a.data_user_profile_id = b.user_profile_id WHERE 1=1";

$limit = intval(@$options['l'] ? $options['l'] : 10);
$prior = @$options['p'];
$email = @$options['u'];
$width = intval(@$options['w'] ? $options['w'] : 80);

if($prior == "N") $sql.= " AND a.priority = " . E_NOTICE;
if($prior == "W") $sql.= " AND a.priority = " . E_WARNING;
if($prior == "E") $sql.= " AND a.priority = " . E_ERROR;
if($email) $sql.= " AND b.login_email = \"" . $email . "\"";

foreach(B_Model::select($sql . " ORDER BY a.id DESC LIMIT " . $limit) AS $l)
{
    $id = $l->id;
    $priority = $l->priority;
    if($priority == E_NOTICE)  $priority = "N";
    if($priority == E_WARNING) $priority = "W";
    if($priority == E_ERROR)   $priority = "E";
    $message = $l->message;
    $email = $l->login_email;
    $created = $l->created_at;

    printf("\n" . str_repeat("-", $width) . "\n\n");
    printf("%-14d%-4s%-" . ($width - 38) . "." . ($width - 38) . "s%-20s\n", $id, $priority, $email, $created);
    // printf(str_repeat("-", $width) . "\n");
    printf("\n%-80s\n", wordwrap($message, $width));
}

