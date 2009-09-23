<div class="midct midct-full">
    <div class="midct-top">&nbsp;</div>
    <div class="midct-cen">
        <div id="pwdchange-ct">
        <div class="conf-title">Password update</div>

        <?php if(is_object($this->profile)) : ?>

            <div id="pwdform"><form>
<input type="hidden" id="email" value="<?php echo $this->profile->login_email ?>">
<input type="hidden" id="user" value="<?php echo $this->profile->hash ?>">
            <div class="form-row">
            <p>Email</p><p><input type="text" class="intxt intxt-full" value="<?php echo $this->profile->login_email ?>" disabled></p>
            </div>
            <div class="form-row">
            <p>Password</p><p><input type="password" class="intxt intxt-full" id="password" value=""></p>
            </div>
            <div class="form-row">
            <p>Confirm password</p><p><input type="password" class="intxt intxt-full" id="passwordc"></p>
            </div>
            <br/>
            <div class="form-bot">
                <button type="button" id="pwdchangesubmit">CHANGE MY PASSWORD</button>
            </div>
            <div id="pwdchange-msg"></div>
            </form></div>

            <div id="changenotice" style="display:none">
                <p>Password was successfully updated. To continue using PostCanal.com, click Home and sign in.</p>
            </div>

        <?php else : ?>

        <p>This URL is no longer valid.</p>

        <?php endif ?>

    </div>
    </div>
    <div class="midct-bot">&nbsp;</div>
</div>
