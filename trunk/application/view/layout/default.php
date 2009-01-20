<?php $helper = new DefaultHelper($this) ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Autoblog</title>
<script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script> 
<?php $helper->includeJavascript() ?>
<?php $helper->includeStyleSheet() ?>
<style type="text/css" media="screen">@import url("/css/default.css");</style>
</head>
<body>
<div id="topbar">
    <div style="float:left">[<a href="<?php echo BASE_URL ?>">principal</a>]</div>
    <div style="float:right">[<a href="<?php echo BASE_URL ?>/profile/logout">sair</a>]</div>
</div>
<div id="defaultcontent"><?php $this->renderTemplate() ?></div>
</body>
</html>
