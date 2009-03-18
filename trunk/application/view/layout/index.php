<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<?php B_Helper::script("spinner.js") ?>
<?php B_Helper::style("default.css") ?>
<?php B_Helper::style("index.css") ?>
<?php $this->renderTemplate('css', false) ?>
<?php $this->renderTemplate('js', false) ?>
</head>
<body>
<div id="container"><?php $this->renderTemplate('php', true) ?></div>
</body>
</html>
