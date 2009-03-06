<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php AB_Helper::script("jquery-1.2.6.min.js") ?>
<?php AB_Helper::script("ab/spinner.js") ?>
<?php AB_Helper::script("ab/alert.js") ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
<?php AB_Helper::style("default.css") ?>
<?php AB_Helper::style("dashboard.css") ?>
</head>
<body>
<div id="topbar">
    <div style="float:left">
        [<?php AB_Helper::a("principal", "dashboard") ?>]
        [<?php AB_Helper::a("perfil", "profile", "edit") ?>]
    </div>
    <div style="float:right">
        <?php $this->translation->user_profile_login_email ?>
        [<?php AB_Helper::a("sair", "profile", "logout") ?>]
    </div>
</div>
<div id="dashboardcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
