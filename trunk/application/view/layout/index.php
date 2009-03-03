<?php

/* helper shortcuts */

function ss ($s) { DefaultHelper::session($s); }
function tr ($s) { DefaultHelper::translation($s); }
function img_src ($s) { DefaultHelper::img_src($s); }
function url ($c=null, $a=null, $p=array()) { DefaultHelper::url($c, $a, $p); }

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
<?php DefaultHelper::style("index.css") ?>
</head>
<body>
<div id="indexcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
