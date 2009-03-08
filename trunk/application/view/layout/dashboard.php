<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php B_Helper::script("jquery-1.2.6.min.js") ?>
<?php B_Helper::script("ab/spinner.js") ?>
<?php B_Helper::script("ab/alert.js") ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
<?php B_Helper::style("default.css") ?>
<?php B_Helper::style("dashboard.css") ?>
</head>
<body>
<div id="topbar">
    <div style="float:left">
        [<?php B_Helper::a("principal", "dashboard") ?>]
        [<?php B_Helper::a("perfil", "profile", "edit") ?>]
    </div>
    <div style="float:right">
        <?php echo $this->session->user_profile_login_email ?>
        [<?php B_Helper::a("sair", "profile", "logout") ?>]
    </div>
</div>
<div id="dashboardcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
