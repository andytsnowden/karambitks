<!-- header file --!>
{include file="header.tpl"}

Testing:
<table class="kb-table">
{foreach $attack at}
<tr>
	<td>{$at.killID}</td>
	<td><a href="index.php?v=detail&id={$at.allianceID}&g=alliance">{$at.allianceName}</a></td>
	<td>{$at.characterID}</td>
	<td>{$at.characterName}</td>
	<td><a href="index.php?v=detail&id={$at.corporationID}&g=corp">{$at.corporationName}</a></td>
	<td>{$at.factionID}</td>
	<td>{$at.factionName}</td>
	<td>{$at.damageDone}</td>
	<!--<td>{$at.finalblow}</td>-->
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