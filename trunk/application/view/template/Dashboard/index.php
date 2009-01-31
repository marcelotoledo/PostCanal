<?php if(is_object($this->information)) : ?>
<?php if(strlen($this->information->name) > 0) : ?>
<h2>Bem vindo, <?php echo $this->information->name ?></h2>
<?php endif ?>
<?php endif ?>

<?php if(count($this->cmslist) == 0) : ?>

<span class="dashboardinfo">Nenhum CMS cadastrado. 
<?php $this->DefaultHelper()->a("adicionar", "cms", "add") ?></span>

<?php else : ?>

<?php foreach($this->cmslist as $cms) : ?>
<span class="cmsitem"><?php echo $cms->name ?></span>
<?php endforeach ?>

<?php endif ?>
