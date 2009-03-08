<?php if(strlen($this->profile->name) > 0) : ?>
<h2><?php echo $this->translation->application_welcome ?>, 
    <?php echo $this->profile->name ?></h2>
<?php endif ?>

<?php if(count($this->cms) == 0) : ?>

<div class="nocms">Nenhum CMS cadastrado. <?php B_Helper::a("Adicionar novo CMS", "cms", "add") ?></span> 

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
<div><?php B_Helper::a($this->translation->application_add, "cms", "add") ?></span>
    </td>
    <td id="right-panel"><!-- dashboard/cms loaded by ajax --></td>
</tr>
</table>

<?php endif ?>
