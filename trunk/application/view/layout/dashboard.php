<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<?php if($this->request->getAction() == "index") : ?>
<script type="text/javascript" src="/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="/jquery/ui/jquery-ui-1.7.custom.min.js"></script>
<style type="text/css" media="screen">@import url("/jquery/ui/css/custom-theme/jquery-ui-1.7.custom.css");</style>
<?php endif ?>
<?php B_Helper::script("spinner.js") ?>
<?php B_Helper::script("dialog.js") ?>
<?php B_Helper::style("application.css") ?>
<?php B_Helper::style("dashboard.css") ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
</head>
<body>

<div id="topbar">
<div id="topleftbar"><nobr>
<span><?php B_Helper::a("principal", "dashboard") ?></span>
<span><?php B_Helper::a("perfil", "profile", "edit") ?></span>
<span><?php B_Helper::a("blogs", "blog") ?></span>
<span><?php B_Helper::a("feeds", "feed") ?></span>
<span id="bloglstbar">
<?php if(($i = count($this->blogs)) && $i == 1 && is_array($this->blogs)) : ?>
    <b>blog: </b><i><?php echo $this->blogs[0]->name ?></i>
    <input type="hidden" id="blogcur" value="<?php echo $this->blogs[0]->hash ?>">
<?php elseif($i > 1) : ?>
    <b>blogs: </b>
    <select name="bloglst">
    <?php foreach($this->blogs as $c) : ?>
    <option value="<?php echo $c->hash ?>"><?php echo $c->name ?></option>
    <?php endforeach ?>
    </select>
<?php endif ?>
</span>
</nobr></div>
<div id="toprightbar"><nobr>
<span><?php echo $this->session->user_profile_login_email ?></span>
<span><?php B_Helper::a("sair", "profile", "logout") ?></span>
</nobr></div>
</div>

<?php if($this->request->getAction() == "index") : ?>
<?php $this->renderTemplate() ?>
<?php else : ?>
<div id="main">
<?php $this->renderTemplate() ?>
</div>
<?php endif ?>

</body>
</html>
