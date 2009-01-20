<?php $helper = new DefaultHelper($this) ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Autoblog</title>
<script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script> 
<?php $helper->includeJavascript() ?>
<?php $helper->includeStyleSheet() ?>
<style type="text/css" media="screen">@import url("/css/main.css");</style>
</head>
<body>
<div id="maincontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
