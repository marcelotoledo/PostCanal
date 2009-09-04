<div id="tplbar" class="midct">
    <div id="tplbartt">Manage Blogs</div>
    <button id="addnewblogbtn">ADD NEW BLOG</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewblogform" class="midct">
<form>
    <div class="form-row">
    <p>URL</p><p><input type="text" class="intxt intxt-full" id="blogurl"></p>
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
        <div class="blogurl"><span></span></div>
        <button class="blogeditbtn">EDIT</button>
        <button class="blogdeletebtn">DELETE</button>
    </div>
    <div class="blogbot">
    <form>
        <div class="form-row">
        <p>Name</p><p><input type="text" name="name" class="intxt intxt-full"></p>
        </div>
        <div class="form-row">
        <p>Username</p><p><input type="text" name="username" class="intxt intxt-full"></p>
        </div>
        <div class="form-row">
        <p>Password</p><p><input type="password" name="password" class="intxt intxt-full" style="display:none"><div class="donotchangepwd"><input type="checkbox" name="donotchangepwd" checked> keep unchanged</div></p>
        </div>
        <!--
        <div class="form-row">
        <p>Keywords</p><p><input type="text" name="keywords" class="intxt intxt-full"></p>
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

<div id="blogdeleteblank" style="display:none">
    <div class="blogdeletemsg">
        <span class="deletemsgbig">Deleting</span><br/>
        <span class="deletemsgmed">proceding will delete</span><br/>
        <span class="deletemsgtny">no content from your blog</span><br/>
        <span class="deletemsgask">Are you sure?</span><br/>
        <form>
        <div class="form-bot">
            <button type="button" class="blogdeletey" name="blogdeletebtn">Yes delete</button>
            <button type="button" class="blogdeleten" name="blognodelbtn">Don't delete</button>
        </div>
        </form>
    </div>
</div>
