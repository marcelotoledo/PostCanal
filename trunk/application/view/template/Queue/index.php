<div id="tplbar" class="midct">
    <div id="tplbartt">Queue</div>
    <div id="tplbaropt">
        <b>Publication:</b> <button class="queuepubbtn" id="queuepubplay">&#9658;<br/>paused</button><button class="queuepubbtn" id="queuepubpause" style="display:none">||<br/>playing</button>
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

<div id="etylst" class="midct"></div>

<div id="entryblank">
<div class="ety ety-cl" entry="" ord="" status="">
    <div class="entrydndhdr">
        <div class="entrydndhdrdec">&nbsp;</div>
        <div class="entrydndhdrdec">&nbsp;</div>
        <div class="entrydndhdrdec">&nbsp;</div>
        <div class="entrydndhdrdec">&nbsp;</div>
    </div>
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
        Title<br/><input type="text" name="entrytitle" value="" class="intxt intxt-full">
    </div>
    <div class="form-row">
        Content<br/><textarea name="entrybody" class="intxa intxa-full" disabled></textarea>
    </div>
    <div class="form-bot" id="editform-bot">
        <button type="button" name="editformsave">SAVE</button>
        <button type="button" name="editformcancel">CANCEL</button>
    </div>
</form>
</div>
