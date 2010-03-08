<div id="tplbar" class="midct">
    <div id="tplbartt">Manage your Sites</div>
    <button id="addnewblogbtn" style="margin-right:10px;">ADD NEW SITE</button>
    <small id="maxreached" style="display:none;line-height:18px;position:relative;top:5px;">(you have reached the maximum of <?php echo $this->session()->user_profile_quota_blog; ?> sites<br/>available in free plan)</small>
    <div id="tplbarclr"></div>
</div>

<div id="addnewblogform" class="midct">
<form>
    <div class="form-row">
    <p>URL</p><p><input type="text" class="intxt intxt-full" id="blogurl"></p>
    </div>
    <div id="supportlist">Today we support wordpress.com, wordpress.org, blogger, blogspot, tumblr and twitter.</div>
    <div class="form-bot">
    <button id="addsubmit">CONTINUE</button>&nbsp;
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
        <div class="form-row username-row">
        <p>Username</p>
        <p><input type="text" name="username" class="intxt intxt-full"></p>
        </div>
        <div class="form-row password-row">
        <p>Password</p><p><input type="password" name="password" class="intxt intxt-full" style="display:none"> <span class="whypwdquestion" style="display:none"> <a href="#"><small>why do you need my password?</small></a></span>
        <div class="donotchangepwd"><input type="checkbox" name="donotchangepwd" checked> keep unchanged</div>
        </p>
        </div>
        <p class="oauth-authorize-row"><a href="#" class="oauth-authorize-lnk">You need to authorize the PostCanal.com to use this site.</a></p>
        <p class="oauth-reauthorize-row">The PostCanal.com is already authorized to use this site. if the authorization is no longer valid, <a href="#" class="oauth-authorize-lnk">request a new authorization</a>.</p>
        <div class="password-notice newsite-notice" style="display:none">
        Your password is necessary to allow us publish content in your site. It is stored securely in our infrasctructure and will be used to content publication only.
        </div>
        <div class="wordpress-remote-publishing newsite-notice" style="display:none"><img src="/image/warning.png"> You must enable <b>Remote Publishing</b> in WordPress<br/><small>Go to your WordPress admin, click on Settings &rarr; Writing and check <b>XML-RPC</b> option</small></div>
        <div class="form-bot">
        <button class="blogupdatebtn">SAVE</button>&nbsp;
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
<div id="noblogmsg2" class="noblogmsg midct"><p>Congratulations! Now you have a <b>site</b> registered in <b>PostCanal.com</b>. Dont forget to provide an <b>username</b> and <b>password</b> to allow <b>PostCanal.com</b> publish content.</p></div>
<!--<div id="noblogmsg3" class="noblogmsg midct"><p>The next step is to register some <b>feeds</b> for this site. This can be done through <b>"Manage feeds"</b> link on the top menu.</p></div>-->
<div id="noblogmsg3" class="noblogmsg midct"><p>Now you can read and write through <b>"Reader/Writer"</b> link on the top menu.</p></div>
