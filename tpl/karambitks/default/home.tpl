<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"/>
<title>{$kb_title}</title>
<link rel="stylesheet" type="text/css" href="{$style_url}/common.css"/>
<link rel="stylesheet" type="text/css" href="{$style_url}/{$theme_url}/style.css"/>
{$page_headerlines}
<script language="javascript" src="{$style_url}/generic.js"></script>
<!--[if lt IE 7]>
<script defer type="text/javascript" src="{$style_url}/generic.js"></script>
<![endif]-->
<style type="text/css">
</style>
</head>
<body {$on_load} style="height: 100%">
{$page_bodylines}
<div align="center" id="popup" style="display:none;
	position:absolute;
    top:217px; width:99%;
	z-index:3;
    padding: 5px;"></div>
	<table class="main-table" height="100%" align="center" bgcolor="#111111" border="0" cellspacing="1" style="height: 100%">
<tr style="height: 100%">
<td valign="top" height="100%" style="height: 100%">
<div id="header">
{if $bannerswf=='true'}
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="1000" HEIGHT="200" id="{$banner}" ALIGN="">
<PARAM NAME=movie VALUE="banner/{$banner}"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=black> <EMBED src="banner/{$banner}" quality=high bgcolor=black WIDTH="1000" HEIGHT="200" NAME="{$banner}" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED> </OBJECT>
{else}
{if $banner_link}
<a href="{$banner_link}">
<a href="?v=home"><img src="img/{$banner}" border="0"></a>
</a>
{else}
<a href="?v=home"><img src="img/{$banner}" border="0"></a>
{/if}
{/if}
</div>
{include file="menu.tpl"}
{$test}
{foreach $table sc}
  {$sc.shipclass} - {$sc.shipkill} - {$sc.shiploss}<br />
{/foreach}
<div class="counter"><sub>{$gen}s </sub></div>
</body>
</html>