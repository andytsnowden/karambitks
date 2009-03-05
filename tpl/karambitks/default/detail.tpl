<!-- header file --!>
{include file="header.tpl"}

Testing:
{foreach $alist attacker}
{$attacker.characterName}<br />
{/foreach}
<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>