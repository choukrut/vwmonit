<?php

date_default_timezone_set('Europe/Paris');

require ("./include/config.inc.php");
require ("./include/functions.inc.php");
require ("./include/smarty/libs/Smarty.class.php");


$nbXml = 0;

// all servers informations
$servers = array();
$servers["hosts"] = 0;
$servers["totalservices"] = 0;
$servers["servicesrunning"] = 0;
$servers["servicesfailure"] = 0;
$servers["servicesunmonitored"] = 0;
$servers["basicservicesabsent"] = 0;
$servers["maxXmlUpdateDelta"] = 500;

// parse all monit xml files 
$xmls = opendir($monitXml);
while ($file = readdir($xmls)) {
	if ($file != "." && $file != "..") 
	{
		$filename = $monitXml."/".$file;
		$servers = getHostInfos($filename,$servers);
		$nbXml++;
	}
}
closedir($xmls);

ksort($servers["monit"]);
$servers["hosts"] = $nbXml;
//echo "<pre>".print_r($servers,1)."</pre>";
//echo "<pre>".print_r($xml,1)."</pre>";

// display the servers informations
$tpl = new Smarty();
$tpl->assign('servers',$servers);
$tpl->assign('pageRefreshDelay',$pageRefreshDelay);
$tpl->display("index.tpl");


?>