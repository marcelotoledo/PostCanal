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

<div id="page0">
<h1>PostCanal.com is invite only (for now)</h1>
<p class="invitemsg">Currently we are in beta version and accepting registration from invited users only. If haven't been invited, please leave your name and email address below and we will invite you in the near future.</p>
<div id="inviteform">
<form>
    <div class="form-row">
    <p>Name</p>
    <p><input type="text" name="name" value="" class="intxt" id="input-name" size="40"></p>
    </div>

    <div class="form-row">
    <p>Email</p>
    <p><input type="text" name="email" value="" class="intxt" id="input-email" size="40"></p>
    </div>

    <div class="form-bot">
        <button type="button" id="invitemebtn">INVITE-ME!</button>
    </div>

    <div id="learnmoremsg">
        <p>If you have been invited, just use the url you received to gain access to PostCanal.com!</p>
    </div>
</form>
</div>
</div>

<div id="page1">
<h1>Thank you!</h1>
<p class="invitemsg">We'll send you invite in the near future.</p> 
</div>

<div id="bottom">
    <button type="button" id="cancelbtn">GO BACK HOME</button>
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
