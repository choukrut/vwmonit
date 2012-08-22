
{foreach from=$servers.monit item=server}
  {if ($smarty.now-$server.lastXmlUpdate) gt $servers.maxXmlUpdateDelta}
    <div class='serverxmlerror'>
      <div class="name">{$server.name}</div> / Error : last XML update date greater than {$servers.maxXmlUpdateDelta} seconds
    </div>
  {else}
  	<div class='server'>

  		<div class="name"><a href="{$server.url}" target="_blank">{$server.name}</a></div>
      <div class="version">({$server.monitversion})</div>

      <div class="service">
        {foreach from=$server.services item=service}
    			{if $service.status == '0'}
    				{if $service.monitor == '0'}
    					{assign var='state' value='unmonitored'}
    				{else}
    					{assign var='state' value='running'}
    				{/if}
          {else if $service.status == '-1'}
              {assign var='state' value='absent'}
    			{else}
    				{assign var='state' value='failure'}
    			{/if}
          <a href="{$server.url}/{$service.name}" target="_blank">
      			<img src="images/{$state}.png"
                      title="{$service.name}" 
                      status="{$service.status}"
                      monitored="{$service.monitor}"
                      type="{$service.type}"
                      {if $service.type == 'host'}
                          infos="port: {$service.port}<br>protocol: {$service.protocol}<br>request: {$service.request}<br>response time: {$service.responsetime}"
                      {else if $service.type == 'process'}
                          infos="pid: {$service.pid}<br>uptime: {$service.uptime}<br>cpu usage: {$service.cpu}<br>memory usage: {$service.memory}"
                      {else if $service.type == 'file'}
                          infos=""
                      {/if}
                  >
            </a>
      	{/foreach}
      </div>
  	</div>
  {/if}
	<br>
{/foreach}
<br><br>