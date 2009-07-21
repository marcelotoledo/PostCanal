<div id="leftcontainer">
    <div id="allitems" class="leftgrouptitle cursorpointer">
        <?php echo $this->translation()->all_items ?>
    </div>
    <div id="subscribedfeeds" class="leftgrouptitle">
        <?php echo $this->translation()->subscribed_feeds ?>
    </div>
    <div id="subscribedfeedslist" class="leftgroupcontainer">
        <!-- feeds -->
    </div>
</div>

<div id="feeditemblank" style="display:none">
    <div class="feeditem" feed=""><a href="#" class="feeditemlnk"><!-- name --></a></div>
</div>

<div id="rightcontainer">
    <div id="rightheader">
        <div id="rightheadertitle"><!-- feed --></div>
        <div id="rightheaderbuttons">
            <b><?php echo $this->translation()->show ?></b>: 
            <a id="articleexpandedlnk" href="#"><?php echo $this->translation()->expanded ?></a>
            <span id="articleexpandedlab" style="display:none"><?php echo $this->translation()->expanded ?></span>
            -
            <a id="articlelistlnk" href="#" style="display:none"><?php echo $this->translation()->list ?></a>
            <span id="articlelistlab"><?php echo $this->translation()->list ?></span>
        </div>
        <div id="rightheaderclear"></div>
    </div>
    <div id="rightmiddle">
        <!-- articles -->
    </div>
    <div id="rightfooter">
        <button id="articleprev" class="articlenavbutton"><?php echo $this->translation()->previous_item ?></button>
        <button id="articlenext" class="articlenavbutton"><?php echo $this->translation()->next_item ?></button>
    </div>
</div>


<div id="articleblank" style="display:none">
    <div class="article articleclosed" feed="" article="" entry="">
        <div class="articlebutton">
            <input type="checkbox">
        </div>
        <div class="articlehead">
            <div class="articlesource"><!-- feed --></div>
            <div class="articletitle"><a href="#"><!-- title --></a></div>
        </div>
        <div class="articlelinks">
            <a class="articleview" href="#" target="_blank">&gt;&gt;</a>
        </div>
        <div class="articleinfo">
            <div class="articledate"><!-- date --></div>
        </div>
        <div class="articleclear"></div>
    </div>
</div>

<div id="contentblank" style="display:none">
    <div class="content">
        <div class="contentauthor" style="display:none"><!-- author --></div>
        <div class="contenttitle"><!-- title --></div>
        <div class="contentbody"><!-- body --></div>
    </div>
</div>
