<div id="tplbar" class="midct">
    <div id="tplbartt">Queue <span></span></div>
    <div id="tplbaropt">
        <b>Publication:</b> <button class="queuepubbtn" id="queuepubplay">paused</button><button class="queuepubbtn" id="queuepubpause" style="display:none">active</button>
        <img>
        <b>Publication Interval:</b>
        <select id="pubinterval">
        <option value="0">ASAP</option>
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

<div id="noentrymsg" class="midct">There is no <b>entry</b> in queue for this <b>site</b>. To start queueing entries, you must check&nbsp;<button id="checkicon" disabled><div class="sprites-img">&nbsp;</div></button> in any article available on <b>Reader/Writer</b>.</div>

<div id="etylst" class="midct"></div>

<div id="entryblank">
<div class="ety ety-cl" entry="" status="">
    <div class="entrydndhdr">&nbsp;</div>
    <div class="sprites-img etytog etytog-ck">&nbsp;</div>
    <div class="etylab">
        <a href="#"><span class="etytt"></span></a>
    </div>
    <div class="etynow"><a href="#" title="publish this item now!">&nbsp;</a></div>
    <div class="etydte"></div>
    <div class="artlnk"><a href="#" target="_blank" title="view original">&nbsp;</a></div>
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

<div id="confirmationform">
<div id="confirmationbody">
<h1>Register confirmation</h1>
<p>You need to confirm your subscription in order to activate PostCanal.com publishing.<p>
<p>Make sure you received an email with a link to confirm your subscription. After this, you will be able to publish using PostCanal.com.</p>
<p>If you have'nt received any message yet, use the form below to send a new confirmation request (remember to check your spam folder).</p>
<br/>
<div id="confirmationbtns">
<button id="confirmationsend">SEND AGAIN</button>&nbsp;&nbsp;<button id="confirmationcancel">CANCEL</button></p>
</div>
</div>
</div>
