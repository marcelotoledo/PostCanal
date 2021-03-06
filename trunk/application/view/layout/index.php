<?php 

/* browser detection */

$this->browser = L_Utility::browserInfo();
$this->browser_is_ie = (strpos($this->browser, 'msie')>0);
$this->browser_is_ie6 = ($this->browser_is_ie && floor(intval($this->browser)/10)==6);
$this->browser_is_firefox = (strpos($this->browser, 'firefox')>0);
$this->browser_is_safari = (strpos($this->browser, 'safari')>0);
$this->browser_is_opera = (strpos($this->browser, 'opera')>0);

?>
<html>
<head>

<?php     if($this->request()->getController()=='signup') : ?>
<title>Signup - PostCanal.com</title>
<?php elseif($this->request()->getController()=='tour') : ?>
<title>Tour - PostCanal.com</title>
<?php elseif($this->request()->getController()=='plans') : ?>
<title>Plans & Prices - PostCanal.com</title>
<?php elseif($this->request()->getController()=='support') : ?>
<title>Support - PostCanal.com</title>
<?php else : ?>
<title>PostCanal.com - We help you publish content</title>
<?php endif ?>
<meta name="description" content="Publish content with perfection! Increase visitors, revenues, quality of your Blog, Twitter and more!">
<meta name="keywords" content="postcanal, blog, feed, twitter, wordpress, blogger">

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://blog.postcanal.com/feed/" />

<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/main.js?v=1257250269"></script>
<link rel="stylesheet" href="/css/main.css?v=1257250269" type="text/css" media="screen"/>

<?php if($this->request()->getController()=='tour' ||
         $this->request()->getController()=='signup') : ?>
<script type="text/javascript" src="/thickbox/thickbox-compressed.js?v=1253526829"></script>
<link rel="stylesheet" href="/thickbox/thickbox.css?v=1253526829" type="text/css" media="screen"/>
<?php endif ?>

<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeLayout('index.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<style type="text/css"><?php $this->includeLayout('index.css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

</head>
<body>
<div id="mainct">

<div id="toprow">
    <div class="menuitm" id="menursp">&nbsp;</div>
    <div class="toprowimg" id="toplogo">
        <h1>PostCanal.com</h1>
        <h2>we help you publish content</h2>
        <div id="logobeta">beta</div>
        <div id="tagline">we help you publish content</div>
    </div>
    <div id="topmenu">
        <!-- home -->
        <?php if($this->request()->getController()=='') : ?>
        <div class="menuitm menuitm-ebd menuitm-left">Home</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm menuitm-left"><a class="menulnk" href="/">Home</a></div>
        <?php if($this->request()->getController()=='signup') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- sign up -->
        <?php if($this->request()->getController()=='signup') : ?>
        <div class="menuitm menuitm-ebd">Sign Up</div>
        <div class="menuitm toprowimg signuparw-ebd" id="signuparw">&nbsp;</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="/signup">Sign Up</a></div>
        <div class="menuitm toprowimg signuparw-dbd" id="signuparw">&nbsp;</div>
        <?php if($this->request()->getController()=='tour') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- quick tour -->
        <?php if($this->request()->getController()=='tour') : ?>
        <div class="menuitm menuitm-ebd">Tour</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="/tour">Tour</a></div>
        <?php if($this->request()->getController()=='support') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- plans -->
        <!--
        <?php if($this->request()->getController()=='plans') : ?>
        <div class="menuitm menuitm-ebd">Plans <span style="font-family:sans-serif;font-weight:normal">&amp;</span> Prices</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="/plans">Plans <span style="font-family:sans-serif;font-weight:normal">&amp;</span> Prices</a></div>
        <?php if($this->request()->getController()=='support') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>
        -->

        <!-- support -->
        <?php if($this->request()->getController()=='support') : ?>
        <div class="menuitm menuitm-ebd menuitm-right">Support</div>
        <?php else : ?>
        <div class="menuitm menuitm-right"><a class="menulnk" href="/support">Support</a></div>
        <?php endif ?>
        <div id="menuitmclr"></div>
    </div>
    <div id="toprowclr"></div>
</div>
<div id="topclr"></div>

<div id="midrow"><?php $this->includeTemplate('php') ?></div>

<div id="btmrow">
    <div id="btmdsh">&nbsp;</div>
    <div id="btmtxt">
        Copyright (C) 2009, PostCanal.com Inc. &nbsp;&bull;&nbsp; <a href="http://blog.postcanal.com" target="_blank">Blog</a> &nbsp;&bull;&nbsp; <a href="#">Terms of Use</a> &nbsp;&bull;&nbsp; <a href="#">Privacy Policy</a>
    </div>
</div>

</div>

<?php if(B_Registry::get('view/googleAnalytics')=='true') : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-2530933-4");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif ?>

</body>
</html>
