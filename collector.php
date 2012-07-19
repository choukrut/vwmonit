<?php

require ("./include/config.inc.php");

$outputDir = $monitXml;

if ($_SERVER["REQUEST_METHOD"] != "POST") 
	exit("Need a POST request");

$xml = file_get_contents("php://input");

//get the monit server id
if (!preg_match('[id(?:>|=")([0-9a-f]{32})[<"]]', $xml, $m)) exit("ID");
	$id = $m[1];

if (preg_match("[<event]", $xml)) exit("Event");  # Events not supported yet.

file_put_contents("$outputDir/$id.xml", $xml);
?>OK
