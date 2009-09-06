<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<base href="<?php echo BASE_URL ?>" />

<script type="text/javascript" src="./jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/application.js"></script>
<link rel="stylesheet" href="./css/application.css" type="text/css" media="screen"/>

<script type="text/javascript"><?php $this->includeLayout('general.js') ?></script>
<script type="text/javascript"><?php $this->includeLayout('index.js') ?></script>
<script type="text/javascript"><?php $this->includeTemplate('js') ?></script>
<style type="text/css"><?php $this->includeLayout('index.css') ?></style>
<style type="text/css"><?php $this->includeTemplate('css') ?></style>

<h1>Welcome to PostCanal Signup</h1>

<p id="invitetit">Currently, we are in beta version and accepting registration from invited users only. If you were not invited yet, please enter your name and e-mail address below, and we will contact you in few days.</p>

<div id="inviteform">
<form>
    <div class="form-row">
    <p>Name</p>
    <p><input type="text" name="name" value="" class="intxt" id="input-name" size="40"></p>
    </div>

    <div class="form-row">
    <p>E-mail</p>
    <p><input type="text" name="email" value="" class="intxt" id="input-email" size="40"></p>
    </div>

    <div class="form-bot">
    <p><button type="button" id="invitemebtn">INVITE-ME!</button></p>
    </div>
</form>
</div>

<div id="invitemsg" style="display:none">
    <p>Thank you for your interest. We will contact you in few days.</p>
    <p><a href="#" id="gototour">Click here</a> to learn more about <b>PostCanal</b>!</p>
</div>

</body>
</html>
