<h1><?php echo $this->translation()->edit_settings ?></h1>

<div class="tabhead" id="edittab">
    <div class="tabitem tabitem-selected" related="tabpersonal"><?php echo $this->translation()->tab_personal_information ?></div>
    <div class="tabitem" related="tabpassword"><?php echo $this->translation()->tab_password ?></div>
    <div class="tabitem" related="tabemail"><?php echo $this->translation()->tab_email ?></div>
    <div class="tabitem" related="tabquota"><?php echo $this->translation()->tab_quota ?></div>
</div>

<div class="tabgroup" id="edittabgroup">
<div class="tabcontainer" id="tabpersonal" style="display:block">
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->name ?></div>
        <input type="text" id="name" value="<?php echo $this->profile->name ?>" size="40">
    </div>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->country ?></div>
        <select id="local_territory" style="width:350px">
        <?php foreach($this->territory as $k => $v) : ?>
        <option value="<?php echo $k ?>" <?php if($k==$this->profile->local_territory) : ?>selected<?php endif ?>><?php echo $v ?></option>
        <?php endforeach ?>
        </select>
    </div>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->time_zone ?></div>
        <select id="local_timezone" disabled style="width:350px">
        </select>
    </div>
    <br/>
    <div class="inputcontainer">
        <input id="editsubmit" type="button" value="<?php echo $this->translation()->save ?>">
    </div>
    <div class="inputmessage" id="editmessage"></div>
</div>

<div class="tabcontainer" id="tabpassword" style="display:none">
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->current_password ?></div>
        <input type="password" id="currentpwd" size="40">
    </div>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->new_password ?></div>
        <input type="password" id="newpwd" size="40">
    </div>
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->retype_new_password ?></div>
        <input type="password" id="confirmpwd" size="40">
    </div>
    <div class="inputcontainer">
        <input id="pwdchangesubmit" type="button" value="<?php echo $this->translation()->save ?>">
    </div>
    <div class="inputmessage" id="pwdchangemessage"></div>
</div>

<div class="tabcontainer" id="tabemail" style="display:none">
    <div class="inputcontainer">
        <div class="inputlabel"><?php echo $this->translation()->email ?></div>
        <input type="text" id="neweml" size="40" value="<?php echo $this->profile->update_email_to ?>">
        &nbsp;&nbsp;&nbsp;
        <?php if($this->profile->update_email_to==($this->profile->login_email_local . '@' . $this->profile->login_email_domain)) : ?>
        <?php echo $this->translation()->verified ?>
        <?php else : ?>
        <?php echo $this->translation()->not_verified ?>
        <?php endif ?>
    </div>
    <div class="inputcontainer">
        <input id="emlchangesubmit" type="button" value="<?php echo $this->translation()->save ?>">
    </div>
    <div class="inputmessage" id="emlchangemessage"></div>
</div>

<div class="tabcontainer" id="tabquota" style="display:none">
    <table id="quotatable">
        <tr>
            <th><?php echo $this->translation()->blog ?></th>
            <td><?php echo $this->blog_total ?> / <?php echo $this->session()->user_profile_quota_blog ?></td>
        </tr>
        <tr>
            <th><?php echo $this->translation()->feed ?></th>
            <td><?php echo $this->feed_total ?> / <?php echo $this->session()->user_profile_quota_feed ?></td>
        </tr>
        <tr>
            <th><?php echo $this->translation()->publication_period ?></th>
            <td><?php echo $this->publication_period_total ?> / <?php echo $this->session()->user_profile_quota_publication_period ?> (<?php echo $this->publication_period ?>)</td>
        </tr>
    </table>
</div>

</div>
