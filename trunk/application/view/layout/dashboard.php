<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php B_Helper::script("jquery-1.2.6.min.js") ?>
<?php B_Helper::script("base/spinner.js") ?>
<?php B_Helper::script("base/alert.js") ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
<?php B_Helper::style("default.css") ?>
<?php B_Helper::style("dashboard.css") ?>
</head>
<body>

<div id="mbar"><nobr>
<span><?php B_Helper::a("principal", "dashboard") ?></span>
<span><?php B_Helper::a("perfil", "profile", "edit") ?></span>
</nobr></div>

<p id="ubar">
<span><?php echo $this->session->user_profile_login_email ?></span>
<span><?php B_Helper::a("sair", "profile", "logout") ?></span>
</p>

<div class="bsep" style="left: 0pt;"></div>

<?php if($this->request->getAction() == "index") : ?>
<div id="main">
<?php else : ?>
<div id="subpage">
<?php endif ?>
<?php $this->renderTemplate() ?>
</div>

</body>
</html>
