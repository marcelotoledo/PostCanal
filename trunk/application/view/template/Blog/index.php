<div class="subcontainer">

<h1><?php echo $this->translation()->blogs ?></h1>

<div id="blogaddlnkdiv">
    <?php B_Helper::a($this->translation()->blog_add, 'blog', 'add') ?>
</div>

<div id="bloglistarea">
    <?php foreach($this->blogs_ as $b): ?>
    <div class="blogitem" blog="<?php echo $b->hash ?>">
        <div class="blogitemleft">
        <span class="blogitemname"><?php echo $b->name ?></span><br/>
        <small><span class="blogitemurl"><?php echo $b->blog_url ?></span></small>
        </div>
        <div class="blogitemright">
        <a class="blogeditlnk" blog="<?php echo $b->hash ?>"><?php echo $this->translation()->edit ?></a>
        <a class="blogdeletelnk" blog="<?php echo $b->hash ?>"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="blogitemeditform" blog="<?php echo $b->hash ?>" style="display:none">
        <form>
        <table>
            <tr>
            <th><?php echo $this->translation()->blog_name ?>:</th>
            <td><input type="text" name="blog_name" value="<?php echo $b->name ?>"></td>
            </tr>
            <tr>
            <th><?php echo $this->translation()->blog_username ?>:</th>
            <td><input type="text" name="blog_username" value="<?php echo $b->blog_username ?>"></td>
            </tr>
            <tr>
            <th><?php echo $this->translation()->blog_password ?>:</th>
            <td>
                <input type="password" name="blog_password" value="">
                <input type="button" name="blogupdatebtn" value="<?php echo $this->translation()->update ?>" blog="<?php echo $b->hash ?>">
                <input type="button" name="blogcancelbtn" value="<?php echo $this->translation()->cancel ?>" blog="<?php echo $b->hash ?>">
            </td>
            </tr>
        </table>
        </form>
    </div>
    <?php endforeach ?>
</div>

</div>
