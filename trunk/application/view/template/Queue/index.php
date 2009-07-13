<div id="queuecontainer">
    <div id="queueheader">
        <div id="queueheadertitle"><?php echo $this->translation()->queue ?></div>

        <div id="intervalcontainer">
            <table><tr><td>
                <div class="headerlabel"><?php echo $this->translation()->publication_interval ?></div>
            </td></tr><tr><td>
                <select id="pubinterval">
                    <option></option>
                </select>
            </td></tr></table>
        </div>

        <div class="headersep">&nbsp;</div>
        <div id="enqueuecontainer">
            <table><tr><td>
                <div class="headerlabel"><?php echo $this->translation()->auto_enqueue ?></div>
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
        <!-- articles -->
    </div>
</div>


<div id="articleblank" style="display:none">
    <div class="article articleclosed" feed="" article="">
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
