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
<?php B_Helper::script("dcontainer.js") ?>
<?php endif ?>
<?php B_Helper::script("spinner.js") ?>
<?php B_Helper::script("alert.js") ?>
<?php B_Helper::style("default.css") ?>
<?php B_Helper::style("dashboard.css") ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
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

<div id="main">
<?php $this->renderTemplate() ?>
</div>

</body>
</html>
