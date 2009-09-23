<h1>Report</h1>

<h2>View report :: <?php echo $this->report_name ?></h2>

<p><a href="./report">go back</a></p>

<p>
<table id="report-view" style="border:2px solid gray">
<?php foreach($this->result AS $l => $i) : ?>
    <?php if($l==0) : ?>
    <tr>
    <?php foreach(array_keys($i) as $c) : ?>
        <th><?php echo $c; ?></th>
    <?php endforeach ?>
    </tr>
    <?php endif ?>
    <tr>
    <?php foreach($i as $c => $j) : ?>
        <td><?php echo strlen($j)>0 ? $j : "&nbsp;" ?></td>
    <?php endforeach ?>
    </tr>
<?php endforeach ?>
</table>
</p>
