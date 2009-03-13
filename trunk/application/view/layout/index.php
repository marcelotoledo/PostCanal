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
<?php B_Helper::style("index.css") ?>
</head>
<body>
<div id="indexcontent"><?php $this->renderTemplate('php', true) ?></div>
</body>
</html>
