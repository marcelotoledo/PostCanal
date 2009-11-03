<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<base href="<?php echo BASE_URL ?>" />

<title>PostCanal.com</title>

<script type="text/javascript" src="/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/jquery/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="/js/application.js?v=1253528538"></script>
<link rel="stylesheet" href="/css/application.css?v=1253528121" type="text/css" media="screen"/>
<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>

<style type="text/css">
*
{
	padding: 0;
	margin: 0;
}
body
{
	text-align: center;
	font-family: "Trebuchet MS", Verdana, Geneva, Arial, Helvetica, sans-serif;
}
p
{
    padding: 5px 0px 5px 0px;
}
img
{
	border: 0;
}
a
{
    text-decoration: none;
    color: #2780b1;
}
a:hover
{
    color: #164a66;
}
h1
{
    font-size: 1.8em;
    margin-bottom: 15px;
}
h2
{
    font-size: 1.4em;
    margin-bottom: 10px;
}
hr
{
    border: 0;
    border-top: 1px solid gray;
    margin-top: 25px;
    margin-bottom: 25px;
}
div.item-title
{
    float: left;
    border-bottom: 1px dotted silver;
    width: 600px;
    height: 25px;
    line-height: 25px;
}
div.item-control
{
    float: left;
    font-size: 0.8em;
    height: 25px;
    line-height: 25px;
}
div.item-clear
{
    clear: left;
}

input, select
{
    height: 30px;
    padding: 2px 0px 2px 0px;
}
input, textarea
{
    width: 500px;
    font-size: 1em;
}
input, select, textarea
{
    padding: 2px 0px 2px 0px;
}
textarea
{
    scroll: auto;
}
button
{
    width: 200px;
    height: 30px;
    font-size: 0.9em;
}

td, th
{
    border: 2px inset gray;
    padding: 5px;
    font-size: 0.8em;
}
th
{
    font-weight: bold;
    font-size: 1em;
}

#mainct
{
    text-align: left;
    margin: 25px;
}
</style>

</head>
<body>
<div id="mainct">

<?php $this->includeTemplate('php') ?>

</body>
</html>
