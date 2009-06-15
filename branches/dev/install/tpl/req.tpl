<table>
  <tr>
    <th colspan="3">Requirement Check</th>
  </tr>
  <tr>
    <td class="tableinfolbl" style="text-align:center;">Require</td>
    <td class="tableinfolbl" style="text-align:center;">Result</td>
    <td class="tableinfolbl" style="text-align:center;">Status</td>
  </tr>
{foreach $req req}
  <tr>
    <td>{$req.require}</td>
    <td>{$req.result}</td>
  {if $req.check == 0}
    <td class="warning">{$req.status}</td>
  {else}
    <td class="good">{$req.status}</td>
  {/if}
  </tr>
{/foreach}
</table>
<br />
<table>
  <tr>
    <th colspan="2">Writable</th>
  </tr>
  <tr>
    <td colspan="2" class="tableinfolbl" style="text-align:center;">Checking file and folder write permissions.</td>
  </tr>
{foreach $write write}
  <tr>
    <td style="width:auto;">{$write.path}</td>
  {if $write.check == 0}
    <td class="warning">{$write.status}</td>
  {else}
    <td class="good">{$write.status}</td>
  {/if}
  </tr>
{/foreach}
</table><br />
{if $error > 0 || $chmodcheck > 0}
  {if $error > 0}
    <h2 class="warning">This web host does not support EMPA.<br />
                        Solution: Rent a web host that meets the requirement<br />
                        or if it's your own, then update/install the requirements.</h2>
  {/if} 
  {if $chmodcheck > 0}
    <h2 class="warning">Some files or folders was not writable.<br />
                        Chmod the files or folders correctly!</h2>
  {/if}
{else}
<form action="{$link}?funk=configdb" method="post">
  <input type="submit" value="Next" />
</form>
{/if}