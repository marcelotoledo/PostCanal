<div class="midct midct-half">
    <div class="midct-top">&nbsp;</div>
    <div class="midct-cen">
        <div id="signup-ct">
            <div class="form-title">E-mail and Password</div>
            <div class="form-row">
            E-mail<br/><input type="text" class="intxt intxt-full" name="email" id="input-email">
            </div>
            <div>
                <div class="form-row form-row-lft">
                Password<br/><input type="password" class="intxt intxt-half" name="password" id="input-password">
                </div>
                <div class="form-row form-row-rgt">
                Re-type password<br/><input type="password" class="intxt intxt-half" name="confirm" id="input-confirm">
                </div>
                <div class="form-row-x"></div>
            </div>
            <br/>
            <div class="form-title">Personal Information</div>
            <div class="form-row">
            Name<br/><input type="text" class="intxt intxt-full" name="name" id="input-name">
            </div>
            <div>
                <div class="form-row form-row-lft">
                Country<br/>
                <select class="insel insel-half" name="country" id="input-country">
                    <option value="">&nbsp;</option>
                    <?php foreach($this->territory as $k => $v) : ?>
                        <option value="<?php echo $k ?>"><?php echo $v ?></option>
                    <?php endforeach ?>
                </select>
                </div>
                <div class="form-row form-row-rgt">
                Timezone<br/>
                <select class="insel insel-half" name="timezone" id="input-timezone">
                    <option value="">&nbsp;</option>
                </select>
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
