{if $step1 > 0}
<table>
  <tr>
    <th colspan="2">Registration Failed</th>
  </tr>
  {foreach $regchecks regcheck}
  <tr>
    <td class="tableinfolbl" style="width:20%;">{$regcheck.action}:</td>
    <td class="warning">{$regcheck.status}</td>
  </tr>
  {/foreach}
</table>
{else}
<table>
  <tr>
    <th colspan="3">Registration Progress</th>
  </tr>
  {foreach $outputs output}
  <tr>
    <td class="tableinfolbl" style="text-align: left; width:35%;">{$output.action}</td>
    <td class="notis">{$output.info}</td>
    {if $output.check == 1}
    <td class="good" style="width:15%;">{$output.status}</td>
    {else}
    <td class="warning" style="width:15%;">{$output.status}</td>
    {/if}
  </tr>
  {/foreach}
</table>
  {if $step2 == 0}
<table>
  <tr>
    <th colspan="3">Create Config Files</th>
  </tr>
    {foreach $inis ini}
  <tr>
    <td class="tableinfolbl" style="text-align: left; width:35%;">{$ini.action}</td>
    <td class="notis">{$ini.info}</td>
      {if $ini.check == 1}
    <td class="good" style="width:15%;">{$ini.status}</td>
      {else}
    <td class="warning" style="width:15%;">{$ini.status}</td>
      {/if}
  </tr>
    {/foreach}
</table>
  {/if}
{/if}
{if $stop == 0}
<hr />
<h2>Setup is done.</h2>
You can now setup a cronjob on:<br />
<b class="good">empa.php</b><br />
to start getting data from eve api center<br />
<br />
Press the finish button to go to EMPA main site.
<form action="{$link}" method="post">
{$inputHiddenPost}<input type="submit" value="Finish" />
</form><br />
{else}
<hr />
<h2 class="warning">Setup Failed</h2>
{$inputHiddenPost}<a href="javascript:history.go(-1)">Go Back</a><br />
{/if}