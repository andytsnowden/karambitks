<!-- header file --!>
{include file="header.tpl"}


<!--End Page Navigation-->

<div id="shipclass-tb"> <!--Shipclass Table Start-->
<table width="190" class="kb-shipclass" align="left" border="1">
<tr><th>Ship Class</th><th>K</th><th>L</th></tr>
{foreach $table sc}
<tr><td>{$sc.shipclass}</td><td>{$sc.shipkill}</td><td>{$sc.shiploss}</td></tr>
{/foreach}
</table>
</div> <!--Shipclass Table End-->

<!--Page Navigation-->
<div id="pnav-tb">
<table width="190" class="kb-shipclass" align="left" border="1"><tr><td>
<a href="?w={$pvsweek}">Previous Week</a><br />
<a href="?w={$nxtweek}">Next Week</a><br />
</td></tr>
</table>
</div>

<div id="content"> <!--Content Start-->

<div id="kb-tb"> <!--Killboard Table Start-->
<table width="750" border="1" class="kb-table" align="right" cellspacing="1">
{foreach $chartoplist char}
<tr><td><img src="?v=character&ID={$char.characterID}" alt="{$char.characterName}" width="33" height="32"/>{$char.characterName}</td><td>{$char.stats}</td></tr>
{/foreach}
<!--Kill Table Row Begin - {$kill.killID}-->
<tr><td colspan="2"><img src="?v=dailykill&w={$week}&y={$year}" alt="Daily Kills/Losses"/></td></tr>
</table>
</div> <!--Killboard Table End-->

<div id="main-tb"> <!--Main Table Start-->
<table width="750" class="main-table kb-table" align="right">
<!--Kill Table Heading-->
<tr class="kb-table-header">
    <th class="kb-table-header" align="center" colspan="2">Ship Type</th><th colspan="3">Victim</th><th>Final Blow</th><th>System Name</th><th>Time</th>
</tr>
<!--Begin Kill Data-->
{foreach $recent kill}
<tr class="kb-table-row-odd" onClick="window.location.href='index.php?v=killdetail&kid={$kill.killID}';" style="cursor: pointer;">
    <td>
    <img src="img/types/shiptypes_png/32_32/{$kill.shipTypeID}.png" alt="{$kill.shipType}" width="32" height="32" />
    </td>
	<td>
	<b>{$kill.shiptype}</b>
	</td>
    <!-- Victim Corporation Logo-->
    <td>
    <img src="http://www.evecorplogo.net/logo.php?id={$kill.vcorpID}&amp;bgc=606060" width="32" height="32" />
    </td>
    <!-- Victim Alliance Logo-->
    <td>
{if $kill.icon eq NULL}
        <img src="img/logos/icons_alliance_png/32_32/icon01_01.png" alt="{$kill.valliName}" height="32" width="32" />
    {elseif $kill.valliID !=0}
        <img src="img/logos/icons_alliance_png/32_32/icon{$kill.icon}" alt="{$kill.valliName}" height="32" width="32" />
    {else}
    {/if}
    </td>
    <!--Victim Information-->
    <td>
        <b>{$kill.victimName}</b> <br />
        {$kill.vcorpName}<br />
        <b>{$kill.valliName}</b><br />
    </td>
    <!--Attacker Information-->
    <td>
        <b>{$kill.killerName}</b><br />
        {$kill.kcorpName}<br />
        <b>{$kill.kalliNmae}</b>
    </td>
    <!--Kill Solarsystem Information-->
    <td>{$kill.solarSystemName} ({$kill.security})</td>
    <!--Kill timestamp-->
    <td>{$kill.killTime}</td>
</tr>
{/foreach}
<!--End of Kill Data-->
</table>
</div> <!--Main Table End-->

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>