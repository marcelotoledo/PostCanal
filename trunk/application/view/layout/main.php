<?php $helper = new DefaultHelper($this) ?>
<html>
<head>
<title>Autoblog</title>
<script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script> 

<?php $helper->includeJavascript() ?>

</head>
<body>
<?php $this->renderTemplate() ?>
</body>
</html>
