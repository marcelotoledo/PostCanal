<h1><?php echo $this->translation()->manage_blogs ?></h1>

<div id="addnewblogbtn"><h2><?php echo $this->translation()->add_new_blog ?></h2></div>

<div id="addnewblogform" style="display:none">
<form>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->url ?></div>
        <input type="text" id="blogurl" size="40">
    </div>
    <div class="inputcontainer">
        <input id="addsubmit" type="button" value="<?php echo $this->translation()->continue ?>">
    </div>
    <div class="inputmessage" id="addmessage"></div>
</form>
</div>

<div id="blogtypefailedmsg" style="display:none">
    <?php echo $this->translation()->blog_type_failed ?>
</div>

<div id="bloglistarea"></div>

<div id="blogitemblank" style="display:none">
    <div class="blogitem" blog="">
        <div class="blogitemleft">
        <span class="blogitemname"></span><br/>
        <small><span class="blogitemurl"></span></small>
        </div>
        <div class="blogitemright">
        <a class="blogeditlnk"><?php echo $this->translation()->edit ?></a>
        <a class="blogdeletelnk" style="display:none"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="blogitemeditform" blog="" style="display:none">
        <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->name ?></div>
            <input type="text" name="name" size="40" value="">
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->username ?></div>
            <input type="text" name="username" size="40" value="">
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->password ?></div>
            <input type="password" name="password" size="40" value="" style="display:none">
            <div class="donotchangepwd"><input type="checkbox" name="donotchangepwd" checked><?php echo $this->translation()->keep_unchanged ?></div>
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->keywords ?></div>
            <input type="text" name="keywords" size="40" value="">
        </div>
        <div class="inputcontainer">
            <input type="button" name="blogupdatebtn" value="<?php echo $this->translation()->save ?>">
            <input type="button" name="blogcancelbtn" value="<?php echo $this->translation()->cancel ?>">
        </div>
        <div class="inputmessage"></div>
        </form>
    </div>
</div>

<div id="blogdeleteblank" style="display:none">
    <div class="blogdeletemsg">
        <span class="deletemsgbig"><?php echo $this->translation()->deleting ?></span><br/>
        <span class="deletemsgmed"><?php echo $this->translation()->proceding_will_delete ?></span><br/>
        <p><?php echo $this->translation()->no_content_from_your_blog ?></p>
        <span class="deletemsgask"><?php echo $this->translation()->are_you_sure ?></span><br/>
        <form>
        <div class="inputcontainer">
            <input type="button" class="blogdeletey" name="blogdeletebtn" value="<?php echo $this->translation()->yes_delete ?>">
            <input type="button" class="blogdeleten" name="blognodelbtn" value="<?php echo $this->translation()->dont_delete ?>">
        </div>
        </form>
    </div>
</div>

