<!-- header file --!>
{include file="header.tpl"}

{foreach $table sc}
  {$sc.shipclass} - {$sc.shipkill} - {$sc.shiploss}<br />
{/foreach}

<!-- footer file --!>
{include file="footer.tpl"}
</body>
</html>