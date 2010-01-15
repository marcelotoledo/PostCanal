<div id="tplbar" class="midct">
    <div id="tplbartt">Manage Feeds</div>
    <button id="addnewfeedbtn">ADD NEW FEED</button>
    <button id="importfeedbtn">IMPORT FEEDS</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewfeedform" class="midct">
<form>
    <div class="form-row">
    <p>URL</p><p><input type="text" class="intxt intxt-full" id="feedurl"></p>
    </div>
    <div id="supportlist">Today we support RSS and Atom feeds.</div>
    <div class="form-bot">
    <button id="addsubmit">CONTINUE</button>&nbsp;
    <button id="addcancel">CANCEL</button>
    <div class="inputmessage" id="addmessage"></div>
    </div>
</form>
</div>

<div id="importfeedform" class="midct">
<form action="/feed/opml" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="blog" value="" id="importblog">
    <div class="form-row">
    <p>OPML File Upload</p><p><input type="file" class="intxt intxt-full" name="opmlfile" id="opmlfile"></p>
    </div>
    <div class="form-bot">
    <button id="importsubmit">CONTINUE</button>&nbsp;
    <button id="importcancel">CANCEL</button>
    <div class="inputmessage" id="importmessage"></div>
    </div>
</form>
</div>

<div id="feedtypefailedmsg" style="display:none">
    <?php echo $this->translation()->feed_type_failed ?>
</div>

<div id="feedoptionsform" class="midct" style="display:none">
<form>
    <div class="form-bot">
        <button id="optsubmit" type="button">SAVE</button>&nbsp;
        <button id="optcancel">CANCEL</button>
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
        <div class="feeddndhdr">&nbsp;</div>
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
        <div class="form-row">
        <p>Folders <span class="form-row-tip">(eg: Business, Enternainment, Sports)</span></p><p><input type="text" name="folders" class="intxt intxt-full"></p>
        </div>
        <div class="form-bot">
        <button class="feedupdatebtn">SAVE</button>&nbsp;
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

<!-- tutorials -->
<div id="nofeedmsg0" class="nofeedmsg midct">There is no <b>feeds</b> registered for this <b>site</b>. To start reading feeds, you must register at least one. You can do this by clicking <b>"add new feed"</a></b> button.</div>
<div id="nofeedmsg1" class="nofeedmsg midct">Now you can provide the <b>URL</b> of a site or RSS where you usually read news, posts, etc., eg: http://news.google.com/?output=rss.</div>
<div id="nofeedmsg2" class="nofeedmsg midct"><p>Congratulations! Now you have added a <b>feed</b> to this site. Now you can start reading feeds by clicking on <b>Reader/Writer</b> link on the top menu.</div>
