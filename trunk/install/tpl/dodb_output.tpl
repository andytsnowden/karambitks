  <tr>
    <td class="tableinfolbl" style="text-align: left; width:35%;">{$output.action}</td>
    <td class="notis">{$output.info}</td>
  {if $output.check == 1}
    <td class="good" style="width:18%;">{$output.status}</td>
  {else}
    <td class="warning" style="width:18%;">{$output.status}</td>
  {/if}
  </tr>
