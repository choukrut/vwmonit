<div class="summary">
		<span class="host">hosts <b>{$servers.hosts}</b></span>
		{* <span>total <b>{$servers.totalservices}</b></span> *}
		{if $servers.servicesfailure != '0' }<span class="failure">services failed <b>{$servers.servicesfailure}</b></span>{/if}
		{if $servers.servicesunmonitored != '0' }<span class="unmonitored">services unmonitored <b>{$servers.servicesunmonitored}</b></span>{/if}
		<span class="running">services running <b>{$servers.servicesrunning}</b></span>
		{if $servers.basicservicesabsent != '0' }<span class="absent">services absent <b>{$servers.basicservicesabsent}</b></span>{/if}
</div>