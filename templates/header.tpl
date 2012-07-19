<head>
  <title>VWmonit - {$servers.servicesfailure} | {$servers.servicesunmonitored} | {$servers.servicesrunning} | {$servers.basicservicesabsent}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="refresh" content="{$pageRefreshDelay}" />
  <!-- css -->
  <link type="text/css" rel="stylesheet" href="css/jquery.qtip.css" />
  <link type="text/css" rel="stylesheet" href="css/vwmonit.css" />
  <!-- js -->
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.qtip.min.js"></script>  
  <!-- include qtip infobox -->
  <script type="text/javascript">
  $(document).ready(function()
  {
    $('img').each (function()
    {
      $(this).qtip(
      {
        content: {
          text: 'monitored: ' + $(this).attr('monitored') + '<br>status: ' + $(this).attr('status') + '<br>' + $(this).attr('infos'),
          title: {
                text: '<img src="images/' + $(this).attr('type') + '.png" />&nbsp;' + $(this).attr('title'),
                button: true  }
        }, 
        position: {
          at: 'bottom center', // position the tooltip above the link
          my: 'top center',
          viewport: $(window), // keep the tooltip on-screen at all times
          effect: false // disable positioning animation
        },
        show: {
          event: 'mouseover',
          solo: true // only show one tooltip at a time
        },
        hide: 'unfocus',
        style: {
          classes: 'ui-tooltip-dark ui-tooltip-shadow'
        }
      })
    });
  });
  </script>
</head>