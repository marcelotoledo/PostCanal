<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php $this->DefaultHelper()->script("jquery-1.2.6.min.js") ?>
<?php $this->DefaultHelper()->script("jquery.spinner.js") ?>
<?php $this->DefaultHelper()->script("simple_popup.js") ?>
<?php $this->DefaultHelper()->include_javascript() ?>
<?php $this->DefaultHelper()->include_stylesheet() ?>
<?php $this->DefaultHelper()->style("default.css") ?>
<?php $this->DefaultHelper()->style("dashboard.css") ?>
</head>
<body>
<div id="topbar">
    <div style="float:left">
        [<?php $this->DefaultHelper()->a("principal", "dashboard") ?>]
        [<?php $this->DefaultHelper()->a("perfil", "profile", "edit") ?>]
    </div>
    <div style="float:right">
        <?php $this->DefaultHelper()->sessionAttribute('user_profile_login_email') ?>
        [<?php $this->DefaultHelper()->a("sair", "profile", "logout") ?>]
    </div>
</div>
<div id="dashboardcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
