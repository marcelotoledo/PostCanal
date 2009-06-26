<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>PostCanal</title>
<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/application.js"></script>
<link rel="stylesheet" href="/css/application.css" type="text/css" media="screen"/>
<?php $this->renderLayout('js', false) ?>
<?php $this->renderLayout('css', false) ?>
<?php $this->renderTemplate('js', false) ?>
<?php $this->renderTemplate('css', false) ?>
</head>
<body>
<div id="container"><?php $this->renderTemplate('php', true) ?></div>
</body>
</html>
