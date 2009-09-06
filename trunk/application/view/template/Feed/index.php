<div id="tplbar" class="midct">
    <div id="tplbartt">Manage Feeds</div>
    <button id="addnewfeedbtn">ADD NEW FEED</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewfeedform" class="midct">
<form>
    <div class="form-row">
    <p>URL</p><p><input type="text" class="intxt intxt-full" id="feedurl"></p>
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

<div id="feedoptionsform" class="midct" style="display:none">
<form>
    <div class="form-bot">
        <button id="optsubmit" type="button">SAVE</button>
    </div>
    <div class="inputmessage" id="optmessage"></div>
</form>
</div>

<div id="feedoptionblank" style="display:none">
    <div class="inputfeedoption">
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
        <p>Title</p><p><input type="text" name="title" class="intxt intxt-full"></p>
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
        <div class="form-bot">
            <button type="button" class="feeddeletey" name="feeddeletebtn">Yes delete</button>
            <button type="button" class="feeddeleten" name="feednodelbtn">Don't delete</button>
        </div>
        </form>
    </div>
</div>
