<div id="tplbar" class="midct">
    <div id="tplbartt">Manage your Sites</div>
    <button id="addnewblogbtn">ADD NEW SITE</button>
    <div id="tplbarclr"></div>
</div>

<div id="addnewblogform" class="midct">
<form>
    <div class="form-row">
    <p>URL</p><p><input type="text" class="intxt intxt-full" id="blogurl"></p>
    </div>
    <div class="form-bot">
    <button id="addsubmit">CONTINUE</button>
    <button id="addcancel">CANCEL</button>
    <div class="inputmessage" id="addmessage"></div>
    <div id="blogtypefailedmsg">This <b>site</b> is not supported.</div>
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
        <span class="deletemsgtny">no content from your site</span><br/>
        <span class="deletemsgask">Are you sure?</span><br/>
        <form>
        <div class="form-bot">
            <button type="button" class="blogdeletey" name="blogdeletebtn">Yes delete</button>
            <button type="button" class="blogdeleten" name="blognodelbtn">Don't delete</button>
        </div>
        </form>
    </div>
</div>

<!-- tutorials -->
<div id="noblogmsg0" class="noblogmsg midct">There is no <b>site</b> registered on your account. To start using <b>PostCanal.com</b>, you must register one first. You can do this by clicking <b>"add new site"</a></b> button.</div>
<div id="noblogmsg1" class="noblogmsg midct">Now you can provide the <b>URL</b> of a site  where you already have account, eg: http://youraccount.wordpress.com.</div>
<div id="noblogmsg2" class="noblogmsg midct"><p>Congratulations! Now you have a <b>site</b> registered in <b>PostCanal.com</b>. Dont forget to provide an <b>username</b> and <b>password</b> to allow <b>PostCanal.com</b> publish content.</p><p>The next step is to register some <b>feeds</b> for this site. This can be done through <b>"Manage feeds"</b> link on the top menu.</p></div>
