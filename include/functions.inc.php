<?php
// ---------------------------------------------------------------
// function which parse monit xml and put them into $servers array
// ---------------------------------------------------------------
function getHostInfos($xmlFile,$servers)
{
	require ('config.inc.php');

	// variables
	$services = array ("Hosts", "Processes", "Files");
	
	// open xml file and search specific xml nodes
	$xml = new SimpleXMLElement($xmlFile,Null, TRUE);
//	echo "<pre>".print_r($xml,1)."</pre>";

	// get the monit version
	$monitVersion=$xml->xpath('//monit/server/version');
	$v = empty($monitVersion) ? $xml->xpath('//monit/@version') : $xml->xpath('//monit/server') ;
	$monitVersion = (string)$v[0]->version;

	// get monit services
	$monitServer=$xml->xpath('//monit/server');
	$monitFiles=getInfoByMonitVersion($xml,$monitVersion,'files');
	$monitProcesses=getInfoByMonitVersion($xml,$monitVersion,'processes');
	$monitHosts=getInfoByMonitVersion($xml,$monitVersion,'hosts');
	$monitSystem=getInfoByMonitVersion($xml,$monitVersion,'system');

	// get monit id
	$id = getInfoByMonitVersion($xml,$monitVersion,'name');

	$servers['monit'][$id]['id'] = getInfoByMonitVersion($xml,$monitVersion,'id');
	$servers['monit'][$id]['name'] = getInfoByMonitVersion($xml,$monitVersion,'name');
	$servers['monit'][$id]['monitversion'] = $monitVersion;
	$servers['monit'][$id]['uptime'] = secondsToString((string)$monitServer[0]->uptime);
	$servers['monit'][$id]['url'] =  "http://".$servers['monit'][$id]['name'].".visible-web.net:".(string)$monitServer[0]->httpd->port;

	$l = 0;
	foreach ($services as $service) {
		foreach (${"monit".$service} as $s) {	
			// get common services informations
			$h = getInfoByMonitVersion($s,$monitVersion,'serviceName');
			$servers['monit'][$id]['services'][$h]['name'] = getInfoByMonitVersion($s,$monitVersion,'serviceName'); //(string)$s->name;
			$servers['monit'][$id]['services'][$h]['status'] = (string)$s->status;
			$servers['monit'][$id]['services'][$h]['monitor'] = (string)$s->monitor;

			// get the service status (running, failure, unmonitored or absent)
			(string)$s->status == 0 ? (  (string)$s->monitor == 0 ? $servers["servicesunmonitored"] ++ : $servers["servicesrunning"] ++ ) : $servers["servicesfailure"] ++;

			// get specific services informations
			switch ($service) {
				case 'Hosts':
					$servers['monit'][$id]['services'][$h]['type'] = "host";
					$servers['monit'][$id]['services'][$h]['port'] = (string)$s->port->port;
					$servers['monit'][$id]['services'][$h]['request'] = (string)$s->port->request;
					$servers['monit'][$id]['services'][$h]['protocol'] = (string)$s->port->protocol;
					$servers['monit'][$id]['services'][$h]['responsetime'] = (string)$s->port->responsetime;
					break;
				case 'Processes':
					$servers['monit'][$id]['services'][$h]['type'] = "process";
					$servers['monit'][$id]['services'][$h]['pid'] = (string)$s->pid;
					$servers['monit'][$id]['services'][$h]['memory'] = (string)$s->memory->percent;
					$servers['monit'][$id]['services'][$h]['cpu'] = (string)$s->cpu->percent;
					$servers['monit'][$id]['services'][$h]['uptime'] = secondsToString((string)$s->uptime);
					break;
				case 'Files':
					$servers['monit'][$id]['services'][$h]['type'] = "file";
					break;
				default:
					break;
			}
			$l++;
		}
	}

	// check if basic services are present
	foreach ($basicServices as $basicService ) {
		if ( ! isset($servers['monit'][$id]['services'][$basicService]) ) {
			$servers['monit'][$id]['services'][$basicService]['status'] = "-1";
			$servers['monit'][$id]['services'][$basicService]['name'] = $basicService;
			$servers['monit'][$id]['services'][$basicService]['type'] = "basicabsent";	
			$servers["basicservicesabsent"] ++;
			$l++;
		}
	}

	$servers['monit'][$id]['totalservices'] = $l;
	$servers["totalservices"] += $l;
	$servers['monit'][$id]['lastXmlUpdate']=filemtime($xmlFile);

return $servers;
}



// -----------------------------------------------------------------------------------
// function for specific info according the monit version due to the xml specification
// -----------------------------------------------------------------------------------
function getInfoByMonitVersion($xml,$monitVersion,$whichInfo)
{
	switch ($whichInfo) {
		case 'id':
			if ( $monitVersion == "5.0.3" ) { $monitServer = $xml->xpath('//monit/server');	$info = (string)$monitServer[0]->id; }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $info = (string)$xml[0]['id']; }
			break;
		case 'name':
			if ( $monitVersion == "5.0.3" ) { $monitSystem=$xml->xpath('//monit/service[@type="5"]'); $info = (string)$monitSystem[0]->name; }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $monitSystem=$xml->xpath('//monit/services/service[type="5"]'); $info = (string)$monitSystem[0]['name']; }
			break;
		case 'files':
			if ( $monitVersion == "5.0.3" ) { $info = $xml->xpath('//monit/service[@type="2"]'); }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $info = $xml->xpath('//monit/services/service[type="2"]'); }
			break;
		case 'processes':
			if ( $monitVersion == "5.0.3" ) { $info = $xml->xpath('//monit/service[@type="3"]'); }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $info = $xml->xpath('//monit/services/service[type="3"]'); }
			break;
		case 'hosts':
			if ( $monitVersion == "5.0.3" ) { $info = $xml->xpath('//monit/service[@type="4"]'); }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4"  ) { $info = $xml->xpath('//monit/services/service[type="4"]'); }
			break;
		case 'system':
			if ( $monitVersion == "5.0.3" ) { $info = $xml->xpath('//monit/service[@type="5"]'); }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $info = $xml->xpath('//monit/services/service[type="5"]'); }
			break;
		case 'serviceName':
			if ( $monitVersion == "5.0.3" ) { $info = (string)$xml->name; }
			elseif ( $monitVersion == "5.3.2" || $monitVersion == "5.4" ) { $info = (string)$xml['name']; }
			break;
		default:
			break;
	}

	return $info;
}



// -----------------------------------------------------------------------
// function which convert seconds into a String
// -----------------------------------------------------------------------
function secondsToString($d)
{
    $periods = array( 'd' => 86400,
                      'h' => 3600,
                      'm' => 60,
                      's' => 1 );
    $parts = array();
    foreach ( $periods as $name => $dur )
    {
        $div = floor( $d / $dur );
         if ( $div == 0 )
                continue;
         else
                $parts[] = $div . $name;
         $d %= $dur;
    }
    $last = array_pop( $parts );
    if ( empty( $parts ) )
        return $last;
    else
        return join( ', ', $parts ) . " and " . $last;
}


?>