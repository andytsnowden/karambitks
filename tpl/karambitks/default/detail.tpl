<!-- header file --!>
{include file="header.tpl"}

Testing:
<table class="kb-table">
{foreach $attack at}
<tr>
	<td>{$at.killID}</td>
	<td>{$at.allianceID}</td>
	<td>{$at.allianceName}</td>
	<td>{$at.characterID}</td>
	<td>{$at.characterName}</td>
	<td>{$at.corporationID}</td>
	<td>{$at.corporationName}</td>
	<td>{$at.factionID}</td>
	<td>{$at.factionName}</td>
	<td>{$at.damageDone}</td>
	<td>{$at.finalblow}</td>
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


<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>