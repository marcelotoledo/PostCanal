<div id="tplbar" class="midct">
    <div id="tplbartt">Queue</div>
    <div id="tplbaropt">
        <b>Publication:</b> <button class="queuepubbtn" id="queuepubplay">active</button><button class="queuepubbtn" id="queuepubpause" style="display:none">paused</button>
        <img>
        <b>Publication Interval:</b>
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
    </div>
    <div id="tplbarclr"></div>
</div>

<div id="noentrymsg" class="midct">There is no <b>entry</b> in queue for this <b>site</b>. To start queueing entries, you must check <button id="checkicon" disabled><div class="sprites-img">&nbsp;</div></button> in any article available on <b>Reader</b>.</div>

<div id="etylst" class="midct"></div>

<div id="entryblank">
<div class="ety ety-cl" entry="" ord="" status="">
    <div class="entrydndhdr">&nbsp;</div>
    <div class="sprites-img etytog etytog-ck">&nbsp;</div>
    <div class="etylab">
        <a href="#"><span class="etytt"></span></a>
    </div>
    <div class="etydte"></div>
    <div class="sprites-img etyedlnk"><a href="#">&nbsp;</a></div>
    <div class="etyclr"></div>
</div>
</div>

<div id="contentblank">
<div class="etyview">
    <h1></h1>
    <div class="etybody"></div>
</div>
</div>

<div id="editform">
<form>
    <div class="form-row">
        <p>Title</p><p><input type="text" name="entrytitle" value="" class="intxt intxt-full"></p>
    </div>
    <div class="form-row">
        <p>Content</p><p><textarea name="entrybody" id="entrybody" class="intxa intxa-full"></textarea></p>
    </div>
    <div class="form-bot" id="editform-bot">
        <button type="button" name="editformsave">SAVE</button>
        <button type="button" name="editformcancel">CANCEL</button>
    </div>
</form>
</div>
