<div id="tplbar" class="midct" style="display:<?php echo ($this->total_feeds>0) ? 'block' : 'none' ?>">
    <div id="tplbartt">All items</div>
    <div id="tplbaropt">
        <b>Show:</b> <a href="#" id="articleexpandedlnk">Expanded</a> <span id="articleexpandedlab">Expanded</span> | <a href="#" id="articlelistlnk">List</a> <span id="articlelistlab">List</span>
    </div>
    <div id="tplbarclr"></div>
</div>

<div id="midmenu" style="display:<?php echo ($this->total_feeds>0) ? 'block' : 'none' ?>">
    <h1>Subscribed Feeds</h1>
    <div class="chlst" id="chtop">
        <div class="ch" id="chall">&raquo; <a href="#" id="chall">All items</a></div>
    </div>
    <div class="chlst" id="chlst"></div>
</div>

<div id="arttop" class="midct" style="display:<?php echo ($this->total_feeds>0) ? 'block' : 'none' ?>">
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

<div id="artlst" class="midct" style="display:<?php echo ($this->total_feeds>0) ? 'block' : 'none' ?>"></div>

<div id="feeditemblank">
<div class="ch" feed="">&raquo; <a href="#" class="feeditemlnk"></a></div>
</div>

<div id="articleblank">
<div class="art art-cl" feed="" article="" entry="">
    <div class="sprites-img arttog arttog-un">&nbsp;</div>
    <div class="artlab">
        <span class="arttt"></span><br/><span class="artch"></span>
    </div>
    <div class="artdte"></div>
    <div class="artlnk"><a href="#" target="_blank" title="view original">&nbsp;</a></div>
    <div class="artclr"></div>
</div>
</div>

<div id="contentblank">
<div class="artview">
    <h1></h1>
    <div class="artbody"></div>
</div>
</div>

<div id="nofeedmsg" class="midct">There is no <b>feeds</b> registered for this <b>site</b>. To start reading feeds, you must register at least one. You can do this by clicking <b>"Manage Feeds"</b> link on top menu.</div>
