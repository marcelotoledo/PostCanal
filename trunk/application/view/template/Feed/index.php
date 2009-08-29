<div id="tplbar" class="midct">
    <div id="tplbartt">Manage Feeds</div>
    <button id="addnewfeedbtn">ADD NEW FEED</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewfeedform" class="midct">
<form>
    <div class="form-row">
    URL<br/><input type="text" class="intxt intxt-full" id="feedurl">
    </div>
    <div class="form-bot">
    <button id="addsubmit">CONTINUE</button>
    <div class="inputmessage" id="addmessage"></div>
    </div>
</form>
</div>

<div id="feedtypefailedmsg" style="display:none">
    <?php echo $this->translation()->feed_type_failed ?>
</div>

<div id="feedoptionsform" style="display:none">
<form>
    <div class="form-row">
        <button id="optsubmit" type="button">SAVE</button>
    </div>
    <div class="inputmessage" id="optmessage"></div>
</form>
</div>

<div id="feedoptionblank" style="display:none">
    <div class="form-row inputfeedoption">
        <input name="inputfeedoption" type="radio" url="">
    </div>
</div>

<div id="feedlistarea" class="midct"></div>

<div id="feeditemblank">
<div class="feed" feed="" ord="">
    <div class="feedtop">
        <div class="feedtit"></div>
        <div class="feedurl"><span></span></div>
        <button class="feededitbtn">EDIT</button>
        <button class="feeddeletebtn">DELETE</button>
    </div>
    <div class="feedbot">
    <form>
        <div class="form-row">
        Title<br/><input type="text" name="title" class="intxt intxt-full">
        </div>
        <div class="form-bot">
        <button class="feedupdatebtn">SAVE</button>
        <button class="feedcancelbtn">CANCEL</button>
        </div>
    </form>
    </div>
</div>
</div>

<div id="feeddeleteblank" style="display:none">
    <div class="feeddeletemsg">
        <span class="deletemsgbig">Deleting</span><br/>
        <span class="deletemsgask">Are you sure?</span><br/>
        <form>
        <div class="inputcontainer">
            <button type="button" class="feeddeletey" name="feeddeletebtn">Yes delete</button>
            <button type="button" class="feeddeleten" name="feednodelbtn">Don't delete</button>
        </div>
        </form>
    </div>
</div>
