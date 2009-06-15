<form action="{$link}" method="post">
<!-- Api Setup -->
<table>
  <tr>
    <th colspan="2">Corporation &amp; Account Creation</th>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;">To create Corporation &amp; Account you need to input some Api info.<br />
This is used for finding and selection a <b class="good">Character</b> and register that<br />character's <b class="good">Corporation</b> in EMPA to be the Corporation that EMPA manage.<br />
You can get your API info here:<br /><a href="http://myeve.eve-online.com/api/default.asp" target="_blank">EVE API Center</a></td>
  </tr>
  <tr>
    <td class="tableinfolbl">API User ID:</td>
    <td><input size="10" type="text" name="api_user_id" value="{$apiuserid}" /></td>
</tr>
  <tr>
    <td class="tableinfolbl">Full API Key:</td>
    <td><input class="input_text" type="text" name="api_key" value="{$apikey}" /></td>
  </tr>
</table>
<input type="hidden" name="getlist" value="true" />
{$inputHiddenPost}
<input type="submit" value="Get Character List" />
</form>