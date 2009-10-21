<div class="midct midct-full">
    <div class="midct-top">&nbsp;</div>
    <div class="midct-cen">
        <div id="signup-ct">
            <div class="form-title">Sign Up</div>
            <div class="form-row">
            <p>Name</p><p><input type="text" class="intxt intxt-full" name="name" id="input-name" value="<?php echo $this->request()->name ?>"></p>
            </div>
            <div class="form-row">
            <p>Email</p><p><input type="text" class="intxt intxt-full" name="email" id="input-email" value="<?php echo $this->request()->email ?>" <?php if(strlen($this->request()->email)>0) echo "disabled"; ?>></p>
            </div>
            <div class="form-row">
            <p>Password</p><p><input type="password" class="intxt intxt-full" name="password" id="input-password"></p>
            </div>
            <div class="form-row">
            <p>Re-type password</p><p><input type="password" class="intxt intxt-full" name="confirm" id="input-confirm"></p>
            </div>
            <div class="form-row">
            <p>Country</p><p>
            <select class="insel insel-full" name="country" id="input-country">
                <option value="">&nbsp;</option>
                <?php foreach($this->territory as $k => $v) : ?>
                    <option value="<?php echo $k ?>"><?php echo $v ?></option>
                <?php endforeach ?>
            </select></p>
            </div>
            <div class="form-row">
            <p>Timezone</p><p>
            <select class="insel insel-full" name="timezone" id="input-timezone">
                <option value="">&nbsp;</option>
            </select></p>
            </div>
            <div id="recaptcha-row">
            <?php require_once 'recaptcha/recaptchalib.php';
            echo recaptcha_get_html(B_Registry::get('recaptcha/publicKey')); ?>
            </div>
            <div class="form-bot">
                <button type="button" id="submit-register">CREATE ACCOUNT</button>
            </div>
            <div id="register-msg"></div>
        </div>
    </div>
    <div class="midct-bot">&nbsp;</div>
</div>
<!--
<div class="rgtct">
    <div class="rgtct-ttl">Sign Up</div>
    <div class="rgtct-lgt">
        If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.
    </div>
    <div class="rgtct-hrd">
        <h1>FAQ</h1>
        <div class="faq-qst">Q: Lorem ipsum dolor sit amet?</div>
        <div class="faq-ans">A: If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</div>
        <div class="faq-lnk"><a href="#">Other questions</a></div>
    </div>
</div>
-->
<div id="midclear"></div>

<a class="thickbox" href="/signup/invitation?KeepThis=true&height=500&width=640&TB_iframe=true&modal=true" id="invitationlnk"></a>
