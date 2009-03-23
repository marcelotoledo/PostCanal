<?php if(count($this->blogs) == 0) : ?>

<div id="noblogmsg">Nenhum <b>blog</b> cadastrado. <?php B_Helper::a("Clique aqui", "blog", "add") ?> para adicionar um novo blog, ou utilize o link "blogs" no menu superior.</div>

<?php else : ?>

<div class="dashboardcontainers" id="feedscontainer">
<h2>feeds</h2>
<div class="containercontentarea">
feeds container
</div>
<div class="containerfooter">footer</div>
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

<?php endif ?>
