<?php $helper = new DefaultHelper($this) ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Blotomate</title>
<script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="/js/jquery.spinner.js"></script>
<script type="text/javascript" src="/js/simple_popup.js"></script>
<?php $helper->includeJavascript() ?>
<?php $helper->includeStyleSheet() ?>
<style type="text/css" media="screen">@import url("/css/default.css");</style>
<style type="text/css" media="screen">@import url("/css/index.css");</style>
</head>
<body>
<div id="indexcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
