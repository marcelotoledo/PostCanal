<?php if(count($this->blogs) > 0) : ?>

<div id="feedarea">
<div id="feedareahead" class="containerhead">
    <div id="feedareatitle"><?php echo $this->translation()->feeds ?></div>

    <div id="feeddisplay">
    <b><?php echo $this->translation()->show_feeds ?>: </b>
    <span id="feeddspall" style="display:none">
        <?php echo $this->translation()->all ?> | 
        <a href="#" id="feeddspthreadedlnk"><?php echo $this->translation()->threaded ?></a>
    </span>
    <span id="feeddspthreaded" style="display:none">
        <a href="#" id="feeddspalllnk"><?php echo $this->translation()->all ?></a> | 
        <?php echo $this->translation()->threaded ?>
    </span>
    </div>

    <div id="articledisplay">
    <b><?php echo $this->translation()->show_articles ?>: </b>
    <span id="articledsplist" style="display:none">
        <?php echo $this->translation()->list ?> | 
        <a href="#" id="articledspexpandedlnk"><?php echo $this->translation()->expanded ?></a>
    </span>
    <span id="articledspexpanded" style="display:none">
        <a href="#" id="articledsplistlnk"><?php echo $this->translation()->list ?></a> | 
        <?php echo $this->translation()->expanded ?>
    </span>
    </div>

    <div id="feednavigation" style="display:none">
        <a href="#" id="articlepreviouslnk">&lt; <?php echo $this->translation()->previous ?></a> | 
        <a href="#" id="articlenextlnk"><?php echo $this->translation()->next ?> &gt;</a>
    </div>

    <div id="feedrefresh">
        <a href="#" id="feedrefreshlnk"><?php echo $this->translation()->refresh ?></a>
    </div>

    <div style="clear:both"></div>
</div>
<div id="feedlistarea"></div>
</div>

<div id="feeditemblank" style="display:none">
<div class="feeditem" feed=""></div>
<div class="feeditemarticles"></div>
</div>

<div id="articleblank" style="display:none">
<div class="article" article="" bound="no">
    <div class="articlequeue">
        <input type="checkbox"/>
    </div>
    <div class="articlelabel">
        <div class="articlefeed" style="display:none"></div>
        <div class="articletitle"></div>
    </div>
    <div class="articleinfo"></div>
    <div class="articlebuttons">
        <a class="viewlnk" href="#" target="_blank"><?php echo $this->translation()->view ?></a>
    </div>
    <div style="clear:both"></div>
</div>
<div class="articlecontent">
</div>
</div>

<div id="articlemoreblank" style="display:none">
    <div class="articlemore" older=""><center><?php echo $this->translation()->older ?></center></div>
</div>

<div id="queuearea">
<div id="queueareaheightbar">&nbsp;</div>
<div id="queueareahead" class="containerhead">
    <div id="queueareatitle"><?php echo $this->translation()->queue ?></div>

    <div id="queuepublication" style="display:none">
        <b><?php echo $this->translation()->publication ?>: </b>

        <a href="#" id="queuepublicationmanuallnk" style="display:none"><?php echo $this->translation()->manual ?></a>
        <span id="queuepublicationmanuallabel" style="display:none"><?php echo $this->translation()->manual ?></span> | 
        <a href="#" id="queuepublicationautomaticlnk" style="display:none"><?php echo $this->translation()->automatic ?></a>
        <span id="queuepublicationautomaticlabel" style="display:none"><?php echo $this->translation()->automatic ?></span>
    </div>

    <div id="queueinterval" style="display:none">
        <table><tr><td>
        <b><?php echo $this->translation()->interval ?>: </b>
        </td><td>
        <form>
        <select id="queueintervalsel">
            <option value="0">ASAP</option>
            <option value="300">5'</option>
            <option value="900">15'</option>
            <option value="1800">30'</option>
            <option value="3600">1h</option>
            <option value="10800">3h</option>
            <option value="43200">12h</option>
            <option value="86400">1D</option>
        </select>
        </form></td></tr></table>
    </div>

    <div id="queuefeeding" style="display:none"><!-- TODO -->
        <b><?php echo $this->translation()->feeding ?>: </b>

        <a href="#" id="queuefeedingmanuallnk" style="display:none"><?php echo $this->translation()->manual ?></a>
        <span id="queuefeedingmanuallabel" style="display:none"><?php echo $this->translation()->manual ?></span> | 
        <a href="#" id="queuefeedingautomaticlnk" style="display:none"><?php echo $this->translation()->automatic ?></a>
        <span id="queuefeedingautomaticlabel" style="display:none"><?php echo $this->translation()->automatic ?></span>
    </div>

    <div id="queueheightlnks">
        <a href="#" id="queueheightmin" class="queueheightlnk" style="display:none"><?php echo $this->translation()->minimize ?></a>
        <a href="#" id="queueheightmed" class="queueheightlnk"><?php echo $this->translation()->expand ?></a>
        <a href="#" id="queueheightmax" class="queueheightlnk" style="display:none"><?php echo $this->translation()->maximize ?></a>
    </div>

    <div style="clear:both"></div>
</div>
</div>

<div id="queuelistarea" style="display:none"></div>

<div id="entryblank" style="display:none">
<div class="entry" entry="" bound="no">
    <div class="entrypublish">
        <input type="checkbox"/>
    </div>
    <div class="entrylabel">
        <div class="entrytitle"></div>
    </div>
    <div class="entryinfo"></div>
    <div class="entrybuttons" style="display:none">
        <a class="editlnk" href="#" target="_blank"><?php echo $this->translation()->edit ?></a>
    </div>
    <div style="clear:both"></div>
</div>
<div class="entrycontent">
</div>
</div>

<?php endif ?>

<div id="noblogmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_blog ?>. <?php B_Helper::a(ucfirst($this->translation()->click_here), 'blog', 'add') ?> <?php echo $this->translation()->new_blog_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->close ?></a>
</div>
</div>
