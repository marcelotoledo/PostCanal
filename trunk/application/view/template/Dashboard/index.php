<?php if(is_object($this->information)) : ?>
<?php if(strlen($this->information->name) > 0) : ?>
<h2>Bem vindo, <?php echo $this->information->name ?></h2>
<?php endif ?>
<?php endif ?>

<?php if(count($this->cms) == 0) : ?>

<div class="nocms">Nenhum CMS cadastrado. <?php AB_Helper::a("Adicionar novo CMS", "cms", "add") ?></span> 

<?php else : ?>

<table id="main-panel">
<tr>
    <td id="cms-panel">
        <span class="panel-title">CMS</span>
        <table class="panel-list">

<?php foreach($this->cms as $cms) : ?>
<tr><td><div class="cms-item" cid="<?php echo $cms->cid ?>"><?php echo $cms->name ?></div></tr></td>
<?php endforeach ?>

        </table>
        <div><?php AB_Helper::a("adicionar", "cms", "add") ?></span>
    </td>
    <td id="right-panel"><!-- dashboard/cms loaded by ajax --></td>
</tr>
</table>

<?php endif ?>
