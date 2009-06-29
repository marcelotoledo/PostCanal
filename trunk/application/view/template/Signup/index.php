<div id="signupform" style="display:block">
    <h1><?php echo $this->translation()->sign_up ?></h1>
    <div class="formcontainer">
    <form>

        <div id="emlnpasswd">
        <h2><?php echo $this->translation()->email_and_password ?></h2>
        <div class="inputleft">
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->email ?></div>
            <input type="text" name="email" size="40">
        </div>
        </div>
        <div class="inputright">
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->password ?></div>
            <input type="password" name="password" size="40">
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->repassword ?></div>
            <input type="password" name="passwordc" size="40">
        </div>
        </div>
        <div class="inputclear"></div>
        </div>

        <div id="persinfo">
        <h2><?php echo $this->translation()->personal_information ?></h2>
        <div class="inputleft">
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->name ?></div>
            <input type="text" name="name" size="40">
        </div>
        </div>
        <div class="inputright">
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->country ?></div>
            <select name="country" style="width:275px">
                <option></option>
            </select>
        </div>
        </div>
        <div class="inputclear"></div>
        </div>

        <div id="persinfo2">
        <div class="inputleft">
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->time_zone ?></div>
            <select name="timezone" style="width:275px">
                <option></option>
            </select>
        </div>
        </div>
        <div class="inputright">
            &nbsp;
        </div>
        <div class="inputclear"></div>
        </div>

        <div id="formbottom">
            <input type="button" value="<?php echo $this->translation()->create_account ?>">
        </div>
        
        <div id="formmessage" class="inputmessage">
        </div>
        <br/>
    </form>
    </div>
</div>
