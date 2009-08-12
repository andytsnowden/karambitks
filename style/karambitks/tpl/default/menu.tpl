<div id="navbar"> <!--Navbar Start-->
<table class="navigation" width="100%" height="25" border="0" cellspacing="1">
<tr class="kb-table-row-odd">
{foreach $menu mu}
<td width="{$menu_w}" align="center"><a class="link" style="display: block;" href="{$mu.link}">{$mu.text}</a></td>
{/foreach}
</tr>
</table>
</div> <!--Navbar End-->