<!-- header file --!>
{include file="header.tpl"}
<table width="150" border="1">
{foreach $table sc}
<tr><td>{$sc.shipclass}</td><td>{$sc.shipkill}</td><td>{$sc.shiploss}</td></tr>
{/foreach}
</table>


<table border="1" width="750">
<tr><th>Ship Type</th><th>Victim</th><th>Final Blow</th><th>System Name</th><th>Time</th></tr>
{foreach $recent kill}
<tr><td>{$kill.shiptype}</td><td>{$kill.victimName}</td><td>{$kill.killerName}</td><td>{$kill.solarSystemID}</td><td>{$kill.killTime}</td></tr>
{/foreach}
</table>

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>