<!-- header file --!>
{include file="header.tpl"}
<table width="190" class="kb-shipclass" align="left" border="1">
{foreach $table sc}
<tr><td>{$sc.shipclass}</td><td>{$sc.shipkill}</td><td>{$sc.shiploss}</td></tr>
{/foreach}
</table>

<table width="750" border="1" class="kb-table" align="right">
{foreach $chartl char}
<tr><td>{$char.charactername}</td><td>{$char.stats}</td></tr>
</table>
{/foreach}

<table width="750" border="1" class="main-table kb-table" align="right">
<tr><th>Ship Type</th><th>Victim</th><th>Final Blow</th><th>System Name</th><th>Time</th></tr>
{foreach $recent kill}
<tr onClick="window.location.href='index.php?v=detail&kid={$kill.killID}';" style="cursor: pointer;"><td>{$kill.shiptype}</td><td><b>{$kill.victimName}</b> <br />Corporation: {$kill.vcorpName}<br />Alliance: {$kill.valliName}</td><td><b>{$kill.killerName}</b><br />Corporation: {$kill.kcorpName}<br />Alliance: {$kill.kalliNmae}</td><td>{$kill.solarSystemName} ({$kill.security})</td><td>{$kill.killTime}</td></tr>
{/foreach}
</table>

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>