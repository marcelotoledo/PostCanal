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
<?php AB_Helper::style("index.css") ?>
</head>
<body>
<div id="indexcontent"><?php $this->renderTemplate('php', true) ?></div>
</body>
</html>
