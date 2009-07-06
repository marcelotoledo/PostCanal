<h1><?php echo $this->translation()->manage_feeds ?></h1>

<div id="addnewfeedbtn"><h2><?php echo $this->translation()->add_new_feed ?></h2></div>

<div id="addnewfeedform" style="display:none">
<form>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->url ?></div>
        <input type="text" id="feedurl" size="40">
    </div>
    <div class="inputcontainer">
        <input id="addsubmit" type="button" value="<?php echo $this->translation()->continue ?>">
    </div>
    <div class="inputmessage" id="addmessage"></div>
</form>
</div>

<div id="feedtypefailedmsg" style="display:none">
    <?php echo $this->translation()->feed_type_failed ?>
</div>

<div id="feedoptionsform" style="display:none">
<form>
    <div class="inputcontainer">
        <input id="optsubmit" type="button" value="<?php echo $this->translation()->save ?>">
    </div>
    <div class="inputmessage" id="optmessage"></div>
</form>
</div>

<div id="feedoptionblank" style="display:none">
    <div class="inputcontainer inputfeedoption">
        <input name="inputfeedoption" type="radio" url="">
    </div>
</div>



<div id="feedlistarea"></div>

<div id="feeditemblank" style="display:none">
    <div class="feeditem" feed="" ord="">
        <div class="feeditemleft">
        <span class="feeditemname"></span><br/>
        <small><span class="feeditemurl"></span></small>
        </div>
        <div class="feeditemright">
        <a class="feededitlnk"><?php echo $this->translation()->edit ?></a>
        <a class="feeddeletelnk" style="display:none"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="feeditemeditform" feed="" style="display:none">
        <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->title ?></div>
            <input type="text" name="title" size="40" value="">
        </div>
        <div class="inputcontainer">
            <input type="button" name="feedupdatebtn" value="<?php echo $this->translation()->save ?>">
            <input type="button" name="feedcancelbtn" value="<?php echo $this->translation()->cancel ?>">
        </div>
        <div class="inputmessage"></div>
        </form>
    </div>
</div>

<div id="feeddeleteblank" style="display:none">
    <div class="feeddeletemsg">
        <span class="deletemsgbig"><?php echo $this->translation()->deleting ?></span><br/>
        <span class="deletemsgask"><?php echo $this->translation()->are_you_sure ?></span><br/>
        <form>
        <div class="inputcontainer">
            <input type="button" class="feeddeletey" name="feeddeletebtn" value="<?php echo $this->translation()->yes_delete ?>">
            <input type="button" class="feeddeleten" name="feednodelbtn" value="<?php echo $this->translation()->dont_delete ?>">
        </div>
        </form>
    </div>
</div>

