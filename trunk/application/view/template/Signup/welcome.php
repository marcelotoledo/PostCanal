<div id="signupform" style="display:block">
    <h1><?php echo $this->translation()->sign_up ?></h1>
    <div class="formcontainer">
    <form>

        <div id="welcomemsg">
        <div class="inputleft">
            <h2><?php echo $this->translation()->welcome_to_postcanal ?></h2>
            <p><?php echo $this->translation()->welcome_msg_normal ?></p>
            <small><?php echo $this->translation()->welcome_msg_small ?></small>
        </div>
        <div class="inputright">
            <h2><?php echo $this->translation()->youre_using_free_plan ?></h2>
            <?php echo $this->translation()->free_plan_msg ?>
        </div>
        <div class="inputclear"></div>
        </div>

        <div id="formbottom">
            <input id="signin_button" type="button" value="<?php echo $this->translation()->sign_in ?>">
        </div>
        
        <br/>
    </form>
    </div>
</div>
