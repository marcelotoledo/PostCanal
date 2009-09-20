<?php $this->has_update_email_to = (strlen($this->profile->update_email_to)>0) ?>
<div id="tplbar" class="midct">
    <div id="tplbartt">Edit Settings</div>
    <div id="tplbarclr"></div>
</div>

<div class="tabhead midct" id="edittab">
    <div class="tabitem tabitem-selected" related="tabpersonal"><?php echo $this->translation()->tab_personal_information ?></div>
    <div class="tabitem" related="tabpassword"><?php echo $this->translation()->tab_password ?></div>
    <div class="tabitem" related="tabemail"><?php echo $this->translation()->tab_email ?></div>
    <div class="tabitem" related="tabquota" style="display:none"><?php echo $this->translation()->tab_quota ?></div>
</div>

<div class="tabgroup midct" id="edittabgroup">
<div class="tabcontainer" id="tabpersonal" style="display:block">
    <div class="form-row">
        <p>Name</p><p><input type="text" id="name" value="<?php echo $this->profile->name ?>" class="intxt intxt-full"></p>
    </div>
    <div class="form-row">
        <p>Country</p><p>
        <select id="local_territory" class="insel insel-full">
        <?php foreach($this->territory as $k => $v) : ?>
        <option value="<?php echo $k ?>" <?php if($k==$this->profile->local_territory) : ?>selected<?php endif ?>><?php echo $v ?></option>
        <?php endforeach ?>
        </select>
        </p>
    </div>
    <div class="form-row">
        <p>Time Zone</p><p>
        <select id="local_timezone" disabled class="insel insel-full">
        </select></p>
    </div>
    <div class="form-bot">
        <button id="editsubmit" type="button">SAVE</button>
    </div>
    <div class="inputmessage" id="editmessage"></div>
</div>

<div class="tabcontainer" id="tabpassword" style="display:none">
    <div class="form-row">
        <p>Current password</p><p><input type="password" id="currentpwd" class="intxt intxt-full"></p>
    </div>
    <div class="form-row">
        <p>New password</p><p><input type="password" id="newpwd" class="intxt intxt-full"></p>
    </div>
    <div class="form-row">
        <p>Retype new password</p><p><input type="password" id="confirmpwd" class="intxt intxt-full"></p>
    </div>
    <div class="form-bot">
        <button id="pwdchangesubmit" type="button">SAVE</button>
    </div>
    <div class="inputmessage" id="pwdchangemessage"></div>
</div>

<div class="tabcontainer" id="tabemail" style="display:none">
    <div class="form-row">
        <p>Email</p>
        <p>
        <input type="text" id="neweml" class="intxt intxt-full" value="<?php echo ($this->has_update_email_to) ? $this->profile->update_email_to : ($this->profile->login_email) ?>">
        &nbsp;&nbsp;&nbsp;
        <span id="emlvermsg">
        <?php if(!$this->has_update_email_to || $this->profile->update_email_to==($this->profile->login_email)) : ?>
        <?php echo $this->translation()->verified ?>
        <?php else : ?>
        <?php echo $this->translation()->not_verified ?>
        <?php endif ?>
        </span>
        </p>
    </div>
    <div class="form-bot">
        <button id="emlchangesubmit" type="button">SAVE</button>
    </div>
    <div class="inputmessage" id="emlchangemessage"></div>
</div>

<div class="tabcontainer" id="tabquota" style="display:none">
    <table id="quotatable">
        <tr>
            <th>Sites</th>
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

</div>
