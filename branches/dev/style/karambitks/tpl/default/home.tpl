<!-- header file --!>
{include file="header.tpl"}
<table width="190" class="kb-shipclass" align="left" border="1">
<tr><th>Ship Class</th><th>K</th><th>L</th></tr>
{foreach $table sc}
<tr><td>{$sc.shipclass}</td><td>{$sc.shipkill}</td><td>{$sc.shiploss}</td></tr>
{/foreach}
</table>

<table width="750" border="1" class="kb-table" align="right">
{foreach $chartoplist char}
<tr><td>{$char.characterName}</td><td>{$char.stats}</td></tr>
{/foreach}
<tr><td colspan="2"><img src="?v=dailykill&w={$week}&y={$year}" alt="Daily Kills/Losses"/></td></tr>
</table>

<table width="750" border="1" class="main-table kb-table" align="right">
<tr><th>Ship Type</th><th></th><th>Victim</th><th>Final Blow</th><th>System Name</th><th>Time</th></tr>
{foreach $recent kill}
<tr onClick="window.location.href='index.php?v=killdetail&kid={$kill.killID}';" style="cursor: pointer;"><td>{$kill.shiptype}</td><td><img src="http://www.evecorplogo.net/logo.php?id={$kill.vcorpID}&amp;bgc=606060" width="32" height="32" /></td><td><b>{$kill.victimName}</b> <br />Corporation: {$kill.vcorpName}<br />Alliance: {$kill.valliName}</td><td><b>{$kill.killerName}</b><br />Corporation: {$kill.kcorpName}<br />Alliance: {$kill.kalliNmae}</td><td>{$kill.solarSystemName} ({$kill.security})</td><td>{$kill.killTime}</td></tr>
{/foreach}
</table>

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>