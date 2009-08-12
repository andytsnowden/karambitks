<br />
{if $stop == 0}
<form action="{$link}" method="post">
<table>
  <tr>
    <th colspan="2">Character &amp; Corporation Select</th>
  </tr>
  {foreach $charinfo info}
  <tr>
    <td style="width: 15px; vertical-align:middle;">
      <input type="radio" name="charName" value="{$info.charName}" checked="checked" />
      <input type="hidden" name="charInfo[{$info.charName}][charId]" value="{$info.charId}" />
      <input type="hidden" name="charInfo[{$info.charName}][corpName]" value="{$info.corpName}" />
      <input type="hidden" name="charInfo[{$info.charName}][corpId]" value="{$info.corpId}" />
    </td>
    <td>
      <table class="notable" style="width: 100%;">
        <tr>
          <td style="width: 64px;"><img src="http://img.eve.is/serv.asp?s=64&amp;c={$info.charId}" width="64" height="64" /></td>
          <td>{$info.charName}</td>
          <td style="text-align:right;">{$info.corpName}</td>
          <td style="width: 64px;"><img src="http://www.evecorplogo.net/logo.php?id={$info.corpId}&amp;bgc=606060" width="64" height="64" />Install for Alliance/Faction? <input type="checkbox" id="allifact" name="allifact" /></td>
        </tr>
      </table>
    </td>
  </tr>
  {/foreach}
</table>
<br />
<table>
  <tr>
    <th colspan="2">Admin Registration</th>
  </tr>
  <tr>
    <td class="tableinfolbl">Password</td>
    <td><input type="password" name="regPass" /> Must be 6 - 24 character long</td>
  </tr>
  <tr>
    <td class="tableinfolbl">Retype Password</td>
    <td><input type="password" name="regCheckPass" /></td>
  </tr>
</table>
{$inputHiddenPost}
<input type="submit" value="Next" />
</form>
<br />
{else}
<h3 class="warning">{$errortext}</h3>
{/if}