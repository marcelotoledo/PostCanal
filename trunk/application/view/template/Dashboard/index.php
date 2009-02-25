<?php if(is_object($this->information)) : ?>
<?php if(strlen($this->information->name) > 0) : ?>
<h2>Bem vindo, <?php echo $this->information->name ?></h2>
<?php endif ?>
<?php endif ?>


<table id="main-panel">
<tr>

<td id="cms-panel">
<span class="panel-title">CMS</span>

<?php if(count($this->cms) == 0) : ?>

<span>Nenhum item cadastrado</span> 

<?php else : ?>

<table class="panel-list">

<?php foreach($this->cms as $cms) : ?>
<tr><td><div class="cms-item" cid="<?php echo $cms->cid_md5 ?>"><?php echo $cms->name ?></div></tr></td>
<?php endforeach ?>

</table>

<?php endif ?>

<span><?php $this->DefaultHelper()->a("adicionar", "cms", "add") ?></span>

</td>

<td id="right-panel">

            <table>
                <td id="rss-items-panel">
                    <h2>Basics of cooking <i>items</i></h2>
                    <table>
                        <tr>
                            <td>
                                <div class="rss-item-title">Vegan Barbecue, Easy Unusual BBQ Grilled Recipes</div>
                                <div class="rss-item-date">12/09/2008 10:02 PM</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="rss-item-title">Vegan Easy Make Chilli Recipe With Eggplant</div>
                                <div class="rss-item-date">12/08/2008 08:50 AM</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="rss-item-title">Easy Christmas Homemade Edible Food Gifts</div>
                                <div class="rss-item-date">12/05/2008 03:04 PM</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="rss-item-title">Wartime Vegetarian Low-Cost Meals</div>
                                <div class="rss-item-date">12/02/2008 07:26 AM</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="rss-item-title">Vegetarian Tortilla Soup Recipe</div>
                                <div class="rss-item-date">11/24/2008 09:07 PM</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td id="rss-chanels-panel">
                    <span class="panel-title">RSS</span>
                    <table class="panel-list">
                        <tr>
                            <td>
                                <div class="channel-item">Sushi of the day</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="channel-item channel-item-selected">Vegetarian cooking</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="channel-item">Basics of cooking</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="channel-item">George Foreman Blog</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </table>
            <table>
                <tr>
                    <td id="queue-panel">
                        <h2>Culin&aacute;ria's <i>Queue</i></h2>
                        <table>
                            <tr>
                                <td>
                                    <div class="rss-item-title">Easy Christmas Homemade Edible Food Gifts</div>
                                    <div class="rss-item-date">12/09/2008 10:02 PM</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="rss-item-title">Christmas diner starters</div>
                                    <div class="rss-item-date">11/11/2008 04:02 PM</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="rss-item-title">Recipes for Diabetics</div>
                                    <div class="rss-item-date">11/08/2008 05:18 AM</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>


</td>

</tr>
</table>



