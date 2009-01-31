<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<?php $this->DefaultHelper()->script("jquery-1.2.6.min.js") ?>
<?php $this->DefaultHelper()->script("ab/spinner.js") ?>
<?php $this->DefaultHelper()->script("ab/alert.js") ?>
<?php $this->DefaultHelper()->include_javascript() ?>
<?php $this->DefaultHelper()->include_stylesheet() ?>
<?php $this->DefaultHelper()->style("default.css") ?>
<?php $this->DefaultHelper()->style("index.css") ?>
</head>
<body>
<div id="indexcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
