<div id="queuecontainer">
    <div id="queueheader">
        <div id="queueheadertitle"><?php echo $this->translation()->queue ?></div>

        <div id="intervalcontainer">
            <table><tr><td>
                <b><?php echo $this->translation()->publication_interval ?></b>
            </td></tr><tr><td>
                <select id="pubinterval">
                    <option value="0"><?php echo $this->translation()->as_soon_as_possible ?></option>
                    <option value="300"><?php echo $this->translation()->_05_minutes ?></option>
                    <option value="900"><?php echo $this->translation()->_15_minutes ?></option>
                    <option value="1800"><?php echo $this->translation()->_30_minutes ?></option>
                    <option value="3600"><?php echo $this->translation()->_01_hour ?></option>
                    <option value="10800"><?php echo $this->translation()->_03_hours ?></option>
                    <option value="43200"><?php echo $this->translation()->_12_hours ?></option>
                    <option value="86400"><?php echo $this->translation()->_01_day ?></option>
                </select>
            </td></tr></table>
        </div>

        <div class="headersep">&nbsp;</div>
        <div id="enqueuecontainer">
            <table><tr><td>
                <b><?php echo $this->translation()->auto_enqueue ?></b>
            </td></tr><tr><td>
                <div id="enqueuelnkno" style="display:block"><a href="#">yes</a> - no</div>
                <div id="enqueuelnkyes" style="display:none">yes - <a href="#">no</a></div>
            </td></tr></table>
        </div>

        <div class="headersep">&nbsp;</div>
        <div class="queuepubbtn" id="queuepubplay" style="display:block">&#9835;<br/>play</div>
        <div class="queuepubbtn" id="queuepubpaused" style="display:none">||<br/>pause</div>
        <div class="headerlabel"><?php echo $this->translation()->publication ?>:</div>

        <div id="queueheaderclear"></div>
    </div>
    <div id="queuemiddle">
        <!-- entries -->
    </div>
</div>


<div id="entryblank" style="display:none">
    <div class="entry entryclosed" entry="" ord="" status="">
        <div class="entrydndhdr">
            <div class="entrydndhdrdec">&nbsp;</div>
            <div class="entrydndhdrdec">&nbsp;</div>
            <div class="entrydndhdrdec">&nbsp;</div>
        </div>
        <div class="entrybutton">
            <input type="checkbox" checked>
        </div>
        <div class="entryhead">
            <div class="entrytitle"><a href="#"><!-- title --></a></div>
        </div>
        <div class="entrylinks">
            <a class="entrydelete" href="#" target="_blank" style="display:none"><?php echo $this->translation()->delete ?></a>
        </div>
        <div class="entryinfo">
            <div class="entrydate"><!-- date --></div>
        </div>
        <div class="entryclear"></div>
    </div>
</div>

<div id="contentblank" style="display:none">
    <div class="content">
        <div class="contenttitle"><!-- title --></div>
        <div class="contentbody"><!-- body --></div>
    </div>
</div>

<div id="editformblank" style="display:none">
    <div class="editform">
    <form>
        <div class="inputcontainer">
            <input type="text" name="entrytitle" value="" class="editformtitle">
        </div>
        <div class="inputcontainer">
            <textarea name="entrybody" class="editformbody"></textarea>
        </div>
        <div class="inputcontainer">
            <input type="button" name="editformsave" value="<?php echo $this->translation()->save ?>" class="editformbutton">
            <input type="button" name="editformcancel" value="<?php echo $this->translation()->cancel ?>" class="editformbutton">
        </div>
    </form>
    </div>
</div>
