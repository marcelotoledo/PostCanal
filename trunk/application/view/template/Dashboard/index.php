<?php if(count($this->blogs) > 0) : ?>

<div id="feedarea">
<div id="feedareahead" class="containerhead">
    <div class="title"><?php echo $this->translation()->feeds ?></div>

    <div class="feeddisplay">
    <b><?php echo $this->translation()->feeds_display ?>: </b>
    <span class="feedsdspall" style="display:none">
        <?php echo $this->translation()->feeds_display_all ?> | 
        <a id="feeddsplnkthr"><?php echo $this->translation()->feeds_display_threaded ?></a>
    </span>
    <span class="feedsdspthr" style="display:none">
        <a id="feeddsplnkall"><?php echo $this->translation()->feeds_display_all ?></a> | 
        <?php echo $this->translation()->feeds_display_threaded ?>
    </span>
    </div>

    <div class="articledisplay">
    <b><?php echo $this->translation()->articles_display ?>: </b>
    <span class="articledsplst" style="display:none">
        <?php echo $this->translation()->articles_display_list ?> | 
        <a id="articledsplnkexp"><?php echo $this->translation()->articles_display_expanded ?></a>
    </span>
    <span class="articledspexp" style="display:none">
        <a id="articledsplnklst"><?php echo $this->translation()->articles_display_list?></a> | 
        <?php echo $this->translation()->articles_display_expanded ?>
    </span>
    </div>

    <div class="feednavigation" style="display:none">
        <a id="articlepreviouslnk">&lt; <?php echo $this->translation()->previous ?></a> | 
        <a id="articlenextlnk"><?php echo $this->translation()->next ?> &gt;</a>
    </div>

    <div class="feedrefresh">
        <a id="feedrefreshlnk"><?php echo $this->translation()->refresh ?></a>
    </div>

    <div style="clear:both"></div>
</div>
<div id="feedlistarea">
</div>
</div>

<div id="queuearea">
<div id="queueareahctrlbar">&nbsp;</div>
<div id="queueareahead" class="containerhead">
    <div class="title"><?php echo $this->translation()->queue ?></div>

    <div id="queuerunning">
        <b><?php echo $this->translation()->running ?>: </b>

        <a id="queuerunningpauselnk" style="display:none"><?php echo $this->translation()->pause ?></a>
        <span id="queuerunningpauselabel" style="display:none"><?php echo $this->translation()->pause ?></span> | 
        <a id="queuerunningplaylnk" style="display:none"><?php echo $this->translation()->play ?></a>
        <span id="queuerunningplaylabel" style="display:none"><?php echo $this->translation()->play ?></span>
    </div>

    <div id="queuespawning" style="display:none">
        <table><tr><td>
        <b><?php echo $this->translation()->spawning ?>: </b>
        </td><td>
        <form>
        <select id="queuespawningsel">
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

    <div class="queuehctrllnks">
        <a id="queuehctrlmin" class="queuehctrllnk" style="display:none"><?php echo $this->translation()->minimize ?></a>
        <a id="queuehctrlexp" class="queuehctrllnk"><?php echo $this->translation()->expand ?></a>
        <a id="queuehctrlmax" class="queuehctrllnk" style="display:none"><?php echo $this->translation()->maximize ?></a>
    </div>

    <div style="clear:both"></div>
</div>
<div id="queuelistarea" style="display:none">
queue list area
</div>
</div>

<?php endif ?>
