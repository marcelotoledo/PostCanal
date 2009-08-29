<?php $bc = count($this->blogs); ?>
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
<script type="text/javascript" src="./jquery/jquery.simplemodal-1.3.min.js"></script>
<script type="text/javascript" src="./fckeditor/fckeditor.js"></script>
<?php endif ?>
<script type="text/javascript" src="./js/application.js"></script>
<link rel="stylesheet" href="./css/application.css" type="text/css" media="screen"/>

<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeLayout('dashboard.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<style type="text/css"><?php $this->includeLayout('dashboard.css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

</head>
<body>
<div id="mainct">

<div id="toprow">
    <div class="sprites-img" id="leftlogo">&nbsp;</div>
    <div id="rightmenu">
        <div id="menutop">
            <div class="menutopfmt" id="menutopleft">
                <?php if($this->request()->getController()=='blog') : ?>
                Manage Blogs
                <?php else : ?>
                <a href="./blog">Manage Blogs</a>
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
                <?php if($bc>0) : ?>
                Working on:
                <select id="bloglstsel">
                <?php foreach($this->blogs as $b) : ?>
                <option value="<?php echo $b->hash ?>" <?php if($this->settings->blog->current == $b->hash) echo "selected"; ?>><?php echo $b->name ?></option>
                <?php endforeach ?>
                </select>
                &nbsp;
                <img>
                <?php endif ?>
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
</body>
</html>
