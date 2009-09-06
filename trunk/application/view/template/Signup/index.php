<div class="midct midct-half">
    <div class="midct-top">&nbsp;</div>
    <div class="midct-cen">
        <div id="signup-ct">
            <div class="form-title">E-mail and Password</div>
            <div class="form-row">
            <p>E-mail</p><p><input type="text" class="intxt intxt-full" name="email" id="input-email" value="<?php echo $this->request()->email ?>"></p>
            </div>
            <div>
                <div class="form-row form-row-lft">
                <p>Password</p><p><input type="password" class="intxt intxt-half" name="password" id="input-password"></p>
                </div>
                <div class="form-row form-row-rgt">
                <p>Re-type password</p><p><input type="password" class="intxt intxt-half" name="confirm" id="input-confirm"></p>
                </div>
                <div class="form-row-x"></div>
            </div>
            <br/>
            <div class="form-title">Personal Information</div>
            <div class="form-row">
            <p>Name</p><p><input type="text" class="intxt intxt-full" name="name" id="input-name" value="<?php echo $this->request()->name ?>"></p>
            </div>
            <div>
                <div class="form-row form-row-lft">
                <p>Country</p><p>
                <select class="insel insel-half" name="country" id="input-country">
                    <option value="">&nbsp;</option>
                    <?php foreach($this->territory as $k => $v) : ?>
                        <option value="<?php echo $k ?>"><?php echo $v ?></option>
                    <?php endforeach ?>
                </select></p>
                </div>
                <div class="form-row form-row-rgt">
                <p>Timezone</p><p>
                <select class="insel insel-half" name="timezone" id="input-timezone">
                    <option value="">&nbsp;</option>
                </select></p>
                </div>
                <div class="form-row-x"></div>
            </div>
            <br/>
            <div class="btn" id="btn-create">
                <div class="btn-brd btn-l">&nbsp;</div>
                <div class="btn-bg  btn-c"><a href="#" id="submit-register">CREATE ACCOUNT</a></div>
                <div class="btn-brd btn-r">&nbsp;</div>
                <div class="btn-x"></div>
            </div>
            <br/>
            <div id="register-msg"></div>
        </div>
    </div>
    <div class="midct-bot">&nbsp;</div>
</div>
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
<div id="midclear"></div>

<a class="thickbox" href="./signup/invitation?KeepThis=true&height=480&width=640&TB_iframe=true&modal=true" id="invitationlnk"></a>
