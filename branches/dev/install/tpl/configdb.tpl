<form action="{$link}?funk=dodb" method="post">
<!-- Database Setup -->
<table>
  <tr>
    <th colspan="2">Database Settings</th>
  </tr>
  <tr>
    <td class="tableinfolbl" style="width:25%;">Host:</td>
    <td><input type="text" name="DB_Host" value="localhost" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">Username:</td>
    <td><input type="text" name="DB_Username" value="empa" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">Password:</td>
    <td><input type="password" name="DB_Password" value="" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">Database:</td>
    <td><input type="text" name="DB_Database" value="kks" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">KKS Prefix:</td>
    <td><input type="text" name="Prefix[kks]" value="kks" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">Yapeal Prefix:</td>
    <td><input type="text" name="Prefix[yapeal]" value="yapeal" /></td>
  </tr>
  <tr>
    <td class="tableinfolbl">Eve DB Dump Prefix:</td>
    <td><input type="text" name="Prefix[db_dump]" value="eve" /></td>
  </tr>
</table>
<input type="submit" value="Next" />
</form>