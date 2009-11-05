<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>

<title>PostCanal.com</title>

<?php if($this->request()->getController()!='ouch') : ?>
<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/main.js?v=1257250269"></script>
<link rel="stylesheet" href="/css/main.css?v=1257250269" type="text/css" media="screen"/>
<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeLayout('default.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<?php endif ?>
<style type="text/css"><?php $this->includeLayout('default.css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

</head>
<body>
<div id="mainct">
<?php $this->includeTemplate('php') ?>
</div>

<?php if(B_Registry::get('view/googleAnalytics')=='true') : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-2530933-4");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif ?>

</body>
</html>
