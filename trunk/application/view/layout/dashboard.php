<?php

/* helper shortcuts */

function ss ($s) { DefaultHelper::session($s); }
function tr ($s) { DefaultHelper::translation($s); }
function img_src ($s) { DefaultHelper::img_src($s); }
function url ($c=null, $a=null, $p=array()) { DefaultHelper::url($c, $a, $p); }
function _a_ ($l, $c=null, $a=null, $p=array()) { DefaultHelper::a($l, $c, $a, $p); }

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php DefaultHelper::script("jquery-1.2.6.min.js") ?>
<?php DefaultHelper::script("ab/spinner.js") ?>
<?php DefaultHelper::script("ab/alert.js") ?>
<?php DefaultHelper::include_javascript() ?>
<?php DefaultHelper::include_stylesheet() ?>
<?php DefaultHelper::style("default.css") ?>
<?php DefaultHelper::style("dashboard.css") ?>
</head>
<body>
<div id="topbar">
    <div style="float:left">
        [<?php _a_("principal", "dashboard") ?>]
        [<?php _a_("perfil", "profile", "edit") ?>]
    </div>
    <div style="float:right">
        <?php ss('user_profile_login_email') ?>
        [<?php _a_("sair", "profile", "logout") ?>]
    </div>
</div>
<div id="dashboardcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
