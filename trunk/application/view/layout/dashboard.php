<?php $this->browser = L_Utility::browserInfo() ?>
<?php $this->browser_is_ie = (strpos($this->browser, 'msie')>0) ?>
<?php $this->browser_is_ie6 = ($this->browser_is_ie && floor(intval($this->browser)/10)==6) ?>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<base href="<?php echo BASE_URL ?>" />

<title>PostCanal.com</title>

<script type="text/javascript" src="./jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./jquery/jquery-ui-1.7.1.custom.min.js"></script>
<?php if($this->request()->getController()=='queue' &&
         $this->request()->getAction()=='index') : ?>
<script type="text/javascript" src="./ckeditor/ckeditor.js?v=1253526829"></script>
<?php endif ?>
<script type="text/javascript" src="./js/application.js?v=1253526829"></script>
<link rel="stylesheet" href="./css/application.css?v=1253526829" type="text/css" media="screen"/>

<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeLayout('dashboard.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<style type="text/css"><?php $this->includeLayout('dashboard.css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

</head>
<body>
<div id="mainct">

<div id="toprow">
    <div class="sprites-img" id="leftlogo">
        <div id="logobeta">beta</div>
        <div id="tagline">we help you publish content</div>
    </div>
    <div id="rightmenu">
        <div id="menutop">
            <div class="menutopfmt" id="menutopleft">
                <?php if($this->request()->getController()=='site') : ?>
                Manage your Sites
                <?php else : ?>
                <a href="./site">Manage your Sites</a>
                <?php endif ?>
            </div>
            <div class="menutopfmt" id="menutopright">
                <?php if($this->request()->getController()=='profile') : ?>
                Edit Settings
                <?php else : ?>
                <a href="./profile/edit">Edit Settings</a>
                <?php endif ?>
                <img class="fakespcimg">
                <a href="./profile/logout">Sign Out</a>
            </div>
            <div id="menutopclr"></div>
        </div>
        <div id="menubot">
            <div class="menubotfmt" id="menubotleft">
                Working on:
                <select id="bloglstsel">
                <?php foreach($this->blogs as $b) : ?>
                <option value="<?php echo $b->hash ?>" <?php if($this->settings->blog->current == $b->hash) echo "selected"; ?>><?php echo (strlen($b->name) > 80) ? (substr($b->name, 0, 80) . '...') : $b->name ?></option>
                <?php endforeach ?>
                </select>
                &nbsp;
                <img>
                <?php if($this->request()->getController()=='feed') : ?>
                Manage Feeds
                <?php else : ?>
                <a href="./feed">Manage Feeds</a>
                <?php endif ?>
                <img>
                <?php if($this->request()->getController()=='reader') : ?>
                Reader
                <?php else : ?>
                <a href="./reader">Reader</a>
                <?php endif ?>
                <img>
                <?php if($this->request()->getController()=='queue') : ?>
                Queue
                <?php else : ?>
                <a href="./queue">Queue</a>
                <?php endif ?>
            </div>
            <div class="menubotfmt" id="menubotright">
                <a href="#">Upgrade</a>
            </div>
            <div id="menubotclr"></div>
        </div>
    </div>
</div>

<?php $this->includeTemplate() ?>

</div>
<div id="flashmessage"></div>
<div id="b_txtoverflow-buffer"></div>

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
