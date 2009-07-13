<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<base href="<?php echo BASE_URL ?>" />

<title>PostCanal</title>

<script type="text/javascript" src="./jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./jquery/jquery-ui-1.7.1.custom.min.js"></script>
<?php if($this->request()->getAction()=='index') : ?>
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

<div id="topbar">
<div id="topleftbar"><nobr><ul>

<?php $bc = count($this->blogs) ?>

<li><a href="./blog"><?php echo $this->translation()->manage_blogs ?></a></li>
<li><a href="./feed"><?php echo $this->translation()->manage_feeds ?></a></li>
<li><a href="./reader"><?php echo $this->translation()->reader ?></a></li>
<li><a href="./queue"><?php echo $this->translation()->queue ?></a></li>
<li><a href="./profile/edit"><?php echo $this->translation()->edit_settings ?></a></li>

<li id="bloglstbar">
<?php if($bc==1) : ?>
    <b><?php echo $this->translation()->application_blog ?>: </b><i><?php echo $this->blogs[0]->name ?></i>
    <input type="hidden" id="currentblog" value="<?php echo $this->blogs[0]->hash ?>">
<?php elseif($bc > 1) : ?>
    <b><?php echo $this->translation()->blog ?>: </b>
    <select id="bloglstsel">
    <?php foreach($this->blogs as $c) : ?>
    <option value="<?php echo $c->hash ?>" <?php if($this->settings->blog->current == $c->hash) echo "selected"; ?>><?php echo $c->name ?></option>
    <?php endforeach ?>
    </select>
<?php endif ?>
</li>

</ul></nobr></div>
<div id="toprightbar"><nobr><ul>

<li id="profilebar"><?php echo $this->session()->user_profile_login_email ?></li>
<li><a href="./profile/logout"><?php echo $this->translation()->sign_out ?></a></li>

</ul></nobr></div>
</div>

<div id="maincontainer" style="display:none"><?php $this->includeTemplate() ?></div>

<div id="flashmessage" style="display:none"></div>

</body>
</html>
