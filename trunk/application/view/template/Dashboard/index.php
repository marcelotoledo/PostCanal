<?php if(count($this->blog) == 0) : ?>

<div id="noblogmsg">Nenhum <b>blog</b> cadastrado. <?php B_Helper::a("Clique aqui", "blog", "add") ?> para adicionar um novo blog, ou utilize o link "blogs" no menu superior.</div>

<?php else : ?>

<div class="dashboardcontainers" id="feedscontainer">
<h2>feeds</h2>
feeds container
</div>

<div class="dashboardcontainers" id="newscontainer">
<h2>news</h2>
news container
</div>

<div class="dashboardcontainers" id="queuecontainer">
<h2>queue</h2>
queue container
</div>

<?php endif ?>
