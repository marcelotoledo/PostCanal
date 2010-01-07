<div id="tplbar" class="midct">
    <div id="tplbartt">All Subscribed Feeds</div>
    <div id="tplbaropt">
        <b>Show:</b> <a href="#" id="articleexpandedlnk">Expanded</a> <span id="articleexpandedlab">Expanded</span> | <a href="#" id="articlelistlnk">List</a> <span id="articlelistlab">List</span>
    </div>
    <div id="tplbarclr"></div>
</div>

<div id="subscribed" class="leftmenu">
    <h1>Subscribed Feeds <a href="#" id="chaddlnk"><img src="/image/dashboard/add.png"></a></h1>
    <div class="chlst" id="chlst">
        <div class="ch chf" id="challf" tag="@subscribed"><img src="/image/dashboard/folder.png"> <a href="#" id="chall" title="All Subscribed Feeds">All items</a><a href="#" class="chtog chopened">&nbsp;</a></div>
    </div>
    <div class="chbot">
        <a href="/feed"><b>Manage Feeds</b></a>
    </div>
</div>

<div id="writings" class="leftmenu">
    <h1>Writings <a href="#" id="wraddlnk"><img src="/image/dashboard/add.png"></a></h1>
    <div class="chlst" id="wrlst">
        <div class="ch chf" tag="@writings"><img src="/image/dashboard/folder.png"> <a href="#" id="wrall" title="All Writings">All items</a></div>
    </div>
</div>

<div id="arttop" class="midct">
    <div id="navbar">
        <?php if($this->browser_is_ie) : ?>
        <input type="button" class="button-ie navbtn" id="articleprev" value="PREVIOUS">
        <input type="button" class="button-ie navbtn" id="articlenext" value="NEXT">
        <?php else : ?>
        <button class="navbtn" type="button" id="articleprev">PREVIOUS</button>
        <button class="navbtn" type="button" id="articlenext">NEXT</button>
        <?php endif ?>
    </div>
    <div id="navclr"></div>
</div>

<div id="artlst" class="midct"></div>

<div id="feeditemblank">
<div class="ch chi" feed=""><img src="/image/dashboard/feed.png"> <a href="#" class="feeditemlnk"></a></div>
</div>

<div id="tagitemblank">
<div class="ch chf" tag=""><img src="/image/dashboard/folder.png"> <a href="#" class="tagitemlnk"></a><a href="#" class="chtog chopened">&nbsp;</a></div>
</div>

<div id="articleblank">
<div class="art art-cl" article="">
    <div class="sprites-img arttog arttog-un">&nbsp;</div>
    <div class="artlab">
        <span class="arttt"></span><br/><span class="artch"></span>
    </div>
    <div class="artdte"></div>
    <div class="artlnk"><a href="#" target="_blank" title="view original">&nbsp;</a></div>
    <div class="artedlnk" style="display:none"><a href="#">&nbsp;</a></div>
    <div class="artrmlnk" style="display:none"><a href="#">&nbsp;</a></div>
    <div class="artclr"></div>
</div>
</div>

<div id="contentblank">
<div class="artview">
    <h1></h1>
    <div class="artbody"></div>
</div>
</div>

<div id="editform">
<form>
    <div class="form-row">
        <p>Title</p><p><input type="text" name="writingtitle" value="" class="intxt intxt-full"></p>
    </div>
    <div class="form-row">
        <p>Content</p><p><textarea name="writingbody" id="writingbody" class="intxa intxa-full"></textarea></p>
    </div>
    <div class="form-bot" id="editform-bot">
        <button type="button" name="editformsave">SAVE</button>
        <button type="button" name="editformcancel">CANCEL</button>
    </div>
</form>
</div>

<div id="nofeedmsg" class="midct">There is no <b>feeds</b> registered for this <b>site</b>. To start reading feeds, you must register at least one. You can do this by clicking <b>"Manage Feeds"</b> link on top menu.</div>

<div id="chaddct" style="display:none">
    <small>Today we support RSS/Atom feeds and keywords.</small><br/>
    <input type="text" id="chaddinput"> <button id="chaddbtn">ADD</button> <button id="chaddccl">CANCEL</button>
    <br/><small>eg: http://news.google.com/?output=rss</small>
</div>
