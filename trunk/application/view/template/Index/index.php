<div id="logo">
    <span>POSTCANAL</span>
</div>

<div class="rightform" id="loginform" style="display:block">
    <h1><?php echo $this->translation()->sign_in ?></h1>
    <div class="formcontainer">
    <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->email ?></div>
            <input type="text" name="email" size="20">
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->password ?></div>
            <input type="password" name="password" size="20">
        </div>
        <div class="inputlinks">
            <a href="#" id="pwdlnk"><?php echo $this->translation()->forgot_password ?></a>
        </div>
        <div class="inputbuttons">
            <input id="loginsubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </div>
        <div class="inputmessage">
        </div>
    </form>
    </div>
    <div class="bottomlnk">
        <a href="./signup"><?php echo $this->translation()->not_a_member ?></a>
    </div>
</div>

<div class="rightform" id="recoveryform" style="display:none">
    <h1><?php echo $this->translation()->forgot_password ?></h1>
    <div class="formcontainer">
    <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->email ?></div>
            <input type="text" name="email" size="20">
        </div>
        <div class="inputbuttons">
            <input id="recoverysubmit" type="button" value="<?php echo $this->translation()->retrieve ?>">
        </div>
        <div class="inputmessage">
        </div>
    </form>
    </div>
    <div class="bottomlnk">
        <a href="#" id="siglnk"><?php echo $this->translation()->remembered_let_me_sign_in ?></a>
    </div>
</div>

<div class="rightform" id="recoverysent" style="display:none">
    <h1><?php echo $this->translation()->retrieved_password ?></h1>
    <div class="formcontainer">
        <span class="msgbig"><?php echo $this->translation()->retrieved_msg_big ?></span><br/>
        <span class="msgsmall"><?php echo $this->translation()->retrieved_msg_small ?></span>
    </div>
    <div class="bottomlnk">
        <a href="#" id="siglnk2"><?php echo $this->translation()->retrieved_let_me_sign_in ?></a>
    </div>
</div>

<div style="clear:both"></div>
