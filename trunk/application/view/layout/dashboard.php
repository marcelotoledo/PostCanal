<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>PostCanal</title>
<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/jquery/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="/fckeditor/fckeditor.js"></script>
<?php B_Helper::style("application.css") ?>
<?php B_Helper::script("application.js") ?>
<?php $this->renderLayout('css', true) ?>
<?php $this->renderLayout('js', true) ?>
<?php $this->renderTemplate('css', false) ?>
<?php $this->renderTemplate('js', false) ?>
</head>
<body>

<div id="topbar">
<div id="topleftbar"><nobr><ul>

<li><?php B_Helper::a($this->translation()->dashboard, "dashboard") ?></li>
<li><?php B_Helper::a($this->translation()->profile, "profile", "edit") ?></li>
<li><?php B_Helper::a($this->translation()->blogs, "blog") ?></li>
<li><?php B_Helper::a($this->translation()->feeds, "feed") ?></li>
<li id="bloglstbar">
<?php if(($i = count($this->blogs)) && $i == 1) : ?>
    <b><?php echo $this->translation()->application_blog ?>: </b><i><?php echo $this->blogs[0]->name ?></i>
    <input type="hidden" id="currentblog" value="<?php echo $this->blogs[0]->hash ?>">
<?php elseif($i > 1) : ?>
    <b><?php echo $this->translation()->blog ?>: </b>
    <select id="bloglstsel">
    <?php foreach($this->blogs as $c) : ?>
    <option value="<?php echo $c->hash ?>" <?php if($this->session()->profile_preference['current_blog'] == $c->hash) echo "selected"; ?>><?php echo $c->name ?></option>
    <?php endforeach ?>
    </select>
<?php endif ?>
</li>

</ul></nobr></div>
<div id="toprightbar"><nobr><ul>

<li id="profilebar"><b><?php echo $this->translation()->profile ?>: </b><?php echo $this->session()->user_profile_login_email ?></li>
<li><?php B_Helper::a($this->translation()->logout, "profile", "logout") ?></li>

</ul></nobr></div>
</div>

<div id="maincontainer">
<?php $this->renderTemplate() ?>
</div>

<div id="noblogmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_blog ?>. <?php B_Helper::a(ucfirst($this->translation()->click_here), 'blog', 'add') ?> <?php echo $this->translation()->new_blog_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->close ?></a>
</div>
</div>

<div id="nofeedmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_feed ?>. <?php B_Helper::a(ucfirst($this->translation()->click_here), 'feed', 'index') ?> <?php echo $this->translation()->new_feed_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->close ?></a>
</div>
</div>

</body>
</html>
