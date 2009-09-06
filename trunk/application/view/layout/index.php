<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<base href="<?php echo BASE_URL ?>" />

<title>PostCanal.com</title>

<script type="text/javascript" src="./jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/application.js"></script>
<link rel="stylesheet" href="./css/application.css" type="text/css" media="screen"/>

<?php if($this->request()->getController()=='tour') : ?>
<script type="text/javascript" src="./js/index/thickbox-compressed.js"></script>
<link rel="stylesheet" href="./css/index/thickbox.css" type="text/css" media="screen"/>
<script type='text/javascript' src='./ovp/swfobject.js'></script>
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
    <div class="toprowimg" id="toplogo">&nbsp;</div>
    <div id="topmenu">
        <!-- home -->
        <?php if($this->request()->getController()=='') : ?>
        <div class="menuitm toprowimg menubrd-ebd" id="menubrd">&nbsp;</div>
        <div class="menuitm menuitm-ebd">Home</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg menubrd-dbd" id="menubrd">&nbsp;</div>
        <div class="menuitm"><a class="menulnk" href="./">Home</a></div>
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
        <div class="menuitm"><a class="menulnk" href="./signup">Sign Up</a></div>
        <div class="menuitm toprowimg signuparw-dbd" id="signuparw">&nbsp;</div>
        <?php if($this->request()->getController()=='tour') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- quick tour -->
        <?php if($this->request()->getController()=='tour') : ?>
        <div class="menuitm menuitm-ebd">Quick Tour</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="./tour">Quick Tour</a></div>
        <?php if($this->request()->getController()=='plans') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- plans -->
        <?php if($this->request()->getController()=='plans') : ?>
        <div class="menuitm menuitm-ebd">Plans &amp; Prices</div>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-r">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="./plans">Plans &amp; Prices</a></div>
        <?php if($this->request()->getController()=='contact') : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-ebd-l">&nbsp;</div>
        <?php else : ?>
        <div class="menuitm toprowimg sepbrd sepbrd-dbd">&nbsp;</div>
        <?php endif ?>
        <?php endif ?>

        <!-- contact -->
        <?php if($this->request()->getController()=='contact') : ?>
        <div class="menuitm menuitm-ebd">Contact</div>
        <?php else : ?>
        <div class="menuitm"><a class="menulnk" href="./contact">Contact</a></div>
        <?php endif ?>

        <div class="menuitm" id="menursp">&nbsp;</div>
        <div id="menuitmclr"></div>
    </div>
    <div id="toprowclr"></div>
</div>
<div id="topclr"></div>

<div id="midrow"><?php $this->includeTemplate() ?></div>

<div id="btmrow">
    <div id="btmdsh">&nbsp;</div>
    <div id="btmtxt">
        Copyright (C) 2009, PostCanal.com Inc. &nbsp;&bull;&nbsp; <a href="#">Terms of Use</a> &nbsp;&bull;&nbsp; <a href="#">Privacy Policy</a>
    </div>
</div>

</div>
</body>
</html>
