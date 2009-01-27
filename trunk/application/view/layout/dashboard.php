<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php $this->DefaultHelper()->script("jquery-1.2.6.min.js") ?>
<?php $this->DefaultHelper()->script("jquery.spinner.js") ?>
<?php $this->DefaultHelper()->script("simple_popup.js") ?>
<?php $this->DefaultHelper()->includeJavascript() ?>
<?php $this->DefaultHelper()->includeStyleSheet() ?>
<?php $this->DefaultHelper()->style("default.css") ?>
<?php $this->DefaultHelper()->style("dashboard.css") ?>
</head>
<body>
<div id="topbar">
    <div style="float:left">
        [<?php $this->DefaultHelper()->href("principal", "dashboard") ?>]
        [<?php $this->DefaultHelper()->href("perfil", "profile", "edit") ?>]
    </div>
    <div style="float:right">
        <?php $this->DefaultHelper()->sessionEmail() ?>
        [<?php $this->DefaultHelper()->href("sair", "profile", "logout") ?>]
    </div>
</div>
<div id="dashboardcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
