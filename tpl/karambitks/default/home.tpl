<HTML>
<BODY>
TEST
{$test}
{foreach $table sc}
  {$sc.shipclass} - {$sc.shipkill} - {$sc.shiploss}<br />
{/foreach}
</BODY>
</HTML>