</table>
<table>
  <tr>
    <td colspan="3" class="tableinfolbl" style="text-align: center;">Total Time: {$totaltime}s</td>
  </tr>
</table>
{if $stop == 0}
<hr />
<h2>Database setup is done.</h2>
<form action="{$link}" method="post">
{$inputHiddenPost}<input type="hidden" name="siteSalt" value="{$siteSalt}" />
<input type="submit" value="Next" />
</form><br />
{else}
<hr />
<h2>Database setup was not completed.</h2><br />
You might have mistyped some info.<br />
<a href="javascript:history.go(-1)">Go Back</a><br />
{/if}