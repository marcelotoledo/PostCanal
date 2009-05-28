<?php if(count($this->blogs) > 0) : ?>

<div id="feedarea">
<div id="feedareahead" class="containerhead">
    <div id="feedareatitle"><?php echo $this->translation()->feeds ?></div>

    <div id="feeddisplay">
    <b><?php echo $this->translation()->show_feeds ?>: </b>
    <span id="feeddspall" style="display:none">
        <?php echo $this->translation()->all ?> | 
        <a href="#" id="feeddspthrlnk"><?php echo $this->translation()->threaded ?></a>
    </span>
    <span id="feeddspthr" style="display:none">
        <a href="#" id="feeddspalllnk"><?php echo $this->translation()->all ?></a> | 
        <?php echo $this->translation()->threaded ?>
    </span>
    </div>

    <div id="articledisplay">
    <b><?php echo $this->translation()->show_articles ?>: </b>
    <span id="articledsplst" style="display:none">
        <?php echo $this->translation()->list ?> | 
        <a href="#" id="articledspexplnk"><?php echo $this->translation()->expanded ?></a>
    </span>
    <span id="articledspexp" style="display:none">
        <a href="#" id="articledsplstlnk"><?php echo $this->translation()->list ?></a> | 
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

<div id="queuearea">
<div id="queueareahctrlbar">&nbsp;</div>
<div id="queueareahead" class="containerhead">
    <div id="queueareatitle"><?php echo $this->translation()->queue ?></div>

    <div id="queuepublication">
        <b><?php echo $this->translation()->publication ?>: </b>

        <a href="#" id="queuepublicationmanuallnk" style="display:none"><?php echo $this->translation()->manual ?></a>
        <span id="queuepublicationmanuallabel" style="display:none"><?php echo $this->translation()->manual ?></span> | 
        <a href="#" id="queuepublicationautomaticlnk" style="display:none"><?php echo $this->translation()->automatic ?></a>
        <span id="queuepublicationautomaticlabel" style="display:none"><?php echo $this->translation()->automatic ?></span>
    </div>

    <div id="queueinterval" style="display:none">
        <table><tr><td>
        <b><?php echo $this->translation()->spawning ?>: </b>
        </td><td>
        <form>
        <select id="queueintervalsel">
            <option value="0"><?php echo $this->translation()->asap ?></option>
            <option value="300">5'</option>
            <option value="900">15'</option>
            <option value="1800">30'</option>
            <option value="3600">1h</option>
            <option value="10800">3h</option>
            <option value="43200">12h</option>
        </select>
        </form></td></tr></table>
    </div>

    <div id="queuefeeding">
        <b><?php echo $this->translation()->feeding ?>: </b>

        <a href="#" id="queuefeedingmanuallnk" style="display:none"><?php echo $this->translation()->manual ?></a>
        <span id="queuefeedingmanuallabel" style="display:none"><?php echo $this->translation()->manual ?></span> | 
        <a href="#" id="queuefeedingautomaticlnk" style="display:none"><?php echo $this->translation()->automatic ?></a>
        <span id="queuefeedingautomaticlabel" style="display:none"><?php echo $this->translation()->automatic ?></span>
    </div>

    <div id="queuehctrllnks">
        <a href="#" id="queuehctrlmin" class="queuehctrllnk" style="display:none"><?php echo $this->translation()->minimize ?></a>
        <a href="#" id="queuehctrlexp" class="queuehctrllnk"><?php echo $this->translation()->expand ?></a>
        <a href="#" id="queuehctrlmax" class="queuehctrllnk" style="display:none"><?php echo $this->translation()->maximize ?></a>
    </div>

    <div style="clear:both"></div>
</div>
<div id="queuelistarea" style="display:none">
queue list area
</div>
</div>

<?php endif ?>
