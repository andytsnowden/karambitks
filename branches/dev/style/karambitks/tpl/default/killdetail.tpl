<!-- header file --!>
{include file="header.tpl"}

<table class="kb-table">
{foreach $attack at}
<tr>
	<td>{$at.killID}</td>
	<td><a href="index.php?v=detail&id={$at.allianceID}&g=alliance">{$at.allianceName}</a></td>
	<td>{$at.characterID}</td>
	<td>{$at.characterName}</td>
	<td><img src="http://www.evecorplogo.net/logo.php?id={$at.corporationID}&amp;bgc=606060" width="32" height="32" alt="{$at.corporationName}"/></td>
	<td><a href="index.php?v=detail&id={$at.corporationID}&g=corp">{$at.corporationName}</a></td>
{if $at.factionID !=0}
    <td>{$at.factionID}</td>
	<td>{$at.factionName}</td>
{/if}
{if $at.allianceID !=0}
    <td>{$at.allianceID}</td>
	<td>{$at.allianceName}</td>
{/if}
	<td>{$at.damageDone}</td>
{if $at.finalblow eq 1}
    <td>yes</td>
{else}
    <td>No</td>
{/if}
	<td>{$at.shipType}</td>
	<td>{$at.weaponType}</td>
</tr>
{/foreach}
</table>

<table>
<tr>
	<td>kill time</td>
	<td>final blow</td>
	<td>solar name</td>
	<td>ally name</td>
	<td>char name</td>
	<td>corp name</td>
	<td>damagetaken</td>
	<td>factionname</td>
	<td>shiptypeid</td>
</tr>
<tr>
	<td>{$killTime}</td>
	<td>{$finalBlow}</td>
	<td>{$solarSystemName}</td>
	<td>{$allianceName}</td>
	<td>{$characterName}</td>
	<td>{$corporationName}</td>
	<td>{$damageTaken}</td>
	<td>{$factionName}</td>
	<td>{$shipTypeID}</td>
</tr>
</table>

<table class="kb-table">
{foreach $items item}
<tr>
	<td>{$item.killID}</td>
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