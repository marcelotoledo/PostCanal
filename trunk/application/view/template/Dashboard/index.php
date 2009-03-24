<?php if(count($this->blogs) == 0) : ?>

<div id="noblogmsg" class="b-dialog" style="display:none">
Nenhum <b>blog</b> cadastrado. <?php B_Helper::a("Clique aqui", "blog", "add") ?> para adicionar um novo blog, ou utilize o link "blogs" no menu superior.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation->dialog_close ?></a>
</div>
</div>

<?php else : ?>

<div class="dashboardcontainers" id="feedscontainer">
<h2>feeds</h2>
<div class="containercontentarea">
feeds container
</div>
<div class="containerfooter"><a id="feedaddlnk"><?php echo $this->translation->feed_add ?></a></div>
</div>

<div class="dashboardcontainers" id="itemscontainer">
<h2>items</h2>
<div class="containercontentarea">
items container
</div>
<div class="containerfooter">footer</div>
</div>

<div class="dashboardcontainers" id="queuecontainer">
<h2>queue</h2>
<div class="containercontentarea">
queue container
</div>
<div class="containerfooter">footer</div>
</div>

<div id="feedaddform" class="b-dialog" style="display:none">
<form>
    <h1><?php echo $this->translation->feed_add_form_title ?></h1>
    <table>
        <tr>
        <th><?php echo $this->translation->feed_add_url ?>:</th>
        <td><input type="text" name="feedaddurl" value=""></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="feedaddcancel" type="button" value="<?php echo $this->translation->application_form_cancel ?>" class="b-dialog-close">
            <input name="feedaddsubmit" type="button" value="<?php echo $this->translation->application_form_submit ?>">
        </td>
        </tr>
    </table>
</form>
</div>

<?php endif ?>
