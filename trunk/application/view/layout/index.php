<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<base href="<?php echo BASE_URL ?>" />

<title>PostCanal</title>

<script type="text/javascript" src="./jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/application.js"></script>
<link rel="stylesheet" href="./css/application.css" type="text/css" media="screen"/>

<script type="text/javascript"><?php $this->includeLayout('js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<style type="text/css"><?php $this->includeLayout('css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

</head>
<body>
<div id="container"><?php $this->includeTemplate() ?></div>
</body>
</html>
