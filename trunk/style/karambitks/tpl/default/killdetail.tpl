<!-- header file --!>
{include file="header.tpl"}
<!--Attackers Table-->
<table class="kb-table" border="1">
<tr>
<th>Alliance/Faction</th><th></th><th>Attacker Name</th><th></th><th></th><th>Corporation</th><th>Damage</th><th>Ship Type</th><th>Weapon Type</th>
</tr>
{foreach $attack at}
{if $at.finalBlow eq 1}
<tr bgcolor="003366">
{else}
<tr>
{/if}
	{if $at.allianceID !=0}
        <td><a href="index.php?v=detail&id={$at.allianceID}&g=alliance">{$at.allianceName}</a></td>
    {/if}
    
	<td><img src="?v=character&ID={$at.characterID}" alt="{$at.characterName}" height="32" width="32"/></td>
	<td>{$at.characterName}</td>
	<td><img src="http://www.evecorplogo.net/logo.php?id={$at.corporationID}&amp;bgc=606060" width="32" height="32" alt="{$at.corporationName}"/></td>
	<td><img src="img/logos/icons_alliance_png/32_32/icon{$at.icon}" alt="{$at.allianceName}" height="32" width="32" /> </td>
	
	<td><a href="index.php?v=detail&id={$at.corporationID}&g=corp">{$at.corporationName}</a></td>
	<td>{$at.damageDone}</td>
	<td>{$at.shipType}</td>
	<td>{$at.weaponType}</td>
</tr>
{/foreach}
</table>
<!--End Attackers Table-->

<!--Victim Table-->
<table border="1">
<tr>
    <td rowspan="3"><img src="?v=character&ID={$characterID}" alt="{$characterName}" height="64" width="64"/></td>
    <td rowspan="3"><img src="http://www.evecorplogo.net/logo.php?id={$corporationID}&amp;bgc=606060" width="64" height="64" alt="{$corporationName}"/></td>
    <td>Victim</td><td>{$characterName}</td>
</tr>
    
<tr>
    <td>Corporation</td>
    <td><a href="index.php?v=detail&id={$corporationID}&g=corp">{$corporationName}</a></td>
</tr>
{if $allianceID !=0}
<tr>
    <td>Aliance</td>
    <td><a href="index.php?v=detail&id={$allianceID}&g=alliance">{$allianceName}</a></td>
<tr>
{elseif $factionID !=0}
<tr>
    <td>Faction</td>
    <td>{$factionName}</td>
</tr>
{else}
    <tr><td></td><td></td>
{/if}
<tr>
    <td rowspan="3"><img src="img/types/shiptypes_png/64_64/{$shipTypeID}.png" alt="{$shipTypeName}" width="64" height="64" />
    <td rowspan="3">
    <!--Alliance Logo Start-->
    {if $icon eq NULL}
        <img src="img/logos/icons_alliance_png/64_64/icon01_01.png" alt="{$allianceName}" height="64" width="64" />
    {elseif $allianceID !=0}
        <img src="img/logos/icons_alliance_png/64_64/icon{$icon}" alt="{$allianceName}" height="64" width="64" />
    {else}
    {/if}
    <!--Alliance Loglo End-->
     </td>
    <td>Location</td>
    <td colspan="2">{$solarSystemName}</td>
</tr>
<tr>
    <td>Date</td>
	<td colspan="2">{$killTime}</td>
</tr>
<tr>
	<td>Ship Type:</td>
	<td>{$shipTypeName}</td>
</tr>
<tr>
    <td>Damage</td>
    <td>{$damageTaken}</td>
</tr>
</table>
<!--End Victim Table-->

<table class="kb-table">
{foreach $items item}
<tr>
    <td><img src="img/icons/icons_items_png/32_32/icon{$item.icon}.png" alt="{$item.typeName}" width="32" height="32" /></td>
	<td>{$item.typeName}</td>
	<td>{$item.qtyDropped}</td>
	<td>{$item.qtyDestroyed}</td>
</tr>
{/foreach}
</table>

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>