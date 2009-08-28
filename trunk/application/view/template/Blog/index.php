<div id="tplbar" class="midct">
    <div id="tplbarch">Manage Blogs</div>
    <button id="addnewblogbtn">ADD NEW BLOG</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewblogform" class="midct">
<form>
    <div class="form-row">
    URL<br/><input type="text" class="intxt intxt-full" id="blogurl">
    </div>
    <div class="form-bot">
    <button id="addsubmit">CONTINUE</button>
    <div class="inputmessage" id="addmessage"></div>
    </div>
</form>
</div>

<div id="bloglistarea" class="midct"></div>

<div id="blogitemblank">
<div class="blog">
    <div class="blogtop">
        <div class="blogtit"></div>
        <div class="blogurl"><span>http://blog.marcelotoledo.org</span></div>
        <button class="blogeditbtn">EDIT</button>
        <button class="blogdeletebtn">DELETE</button>
    </div>
    <div class="blogbot">
    <form>
        <div class="form-row">
        Name<br/><input type="text" name="name" class="intxt intxt-full">
        </div>
        <div class="form-row">
        Username<br/><input type="text" name="username" class="intxt intxt-full">
        </div>
        <div class="form-row">
        Password<br/><input type="password" name="password" class="intxt intxt-full" style="display:none"><div class="donotchangepwd"><input type="checkbox" name="donotchangepwd" checked> keep unchanged</div>
        </div>
        <!--
        <div class="form-row">
        Keywords<br/><input type="text" name="keywords" class="intxt intxt-full">
        </div>
        -->
        <div class="form-bot">
        <button class="blogupdatebtn">SAVE</button>
        <button class="blogcancelbtn">CANCEL</button>
        </div>
    </form>
    </div>
</div>
</div>
