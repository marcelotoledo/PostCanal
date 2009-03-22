<?php $this->renderTemplate('js', false) ?>


<table>
    <td id="channel-items-panel">

    <div id="chadddiv" style="display:block">
        <h2>Adicionar novo Canal</h2>
        <form id="chaddform">
        <input type="hidden" name="chaddcid" value="<?php echo $this->blog->cid ?>">
        <input type="text" name="chaddurl" value="">
        <input type="button" value="adicionar" class="chaddsubmit">
        </form>
    </div> 

    <div id="chlstdiv">
        <h2>Basics of cooking <i>items</i></h2>
        <div id="channel-items-list">
        <table>
            <tr>
                <td>
                    <div class="channel-item-title">Vegan Barbecue, Easy Unusual BBQ Grilled Recipes</div>
                    <div class="channel-item-date">12/09/2008 10:02 PM</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="channel-item-title">Vegan Easy Make Chilli Recipe With Eggplant</div>
                    <div class="channel-item-date">12/08/2008 08:50 AM</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="channel-item-title">Easy Christmas Homemade Edible Food Gifts</div>
                    <div class="channel-item-date">12/05/2008 03:04 PM</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="channel-item-title">Wartime Vegetarian Low-Cost Meals</div>
                    <div class="channel-item-date">12/02/2008 07:26 AM</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="channel-item-title">Vegetarian Tortilla Soup Recipe</div>
                    <div class="channel-item-date">11/24/2008 09:07 PM</div>
                </td>
            </tr>
        </table>
        </div>

    </div>

    </td>
    <td id="channel-chanels-panel">
        <span class="panel-title">Channels</span>
        <table class="panel-list">

<?php foreach($this->channels as $channel) : ?>

<tr><td><div class="channel-item" cid="<?php echo $this->blog->cid ?>" ch="<?php echo $channel->ch ?>"><?php echo $channel->title ?></div></td></tr>

<?php endforeach ?>

        </table>
<div><a id="chaddlnk" cid="<?php echo $this->blog->cid ?>"><?php echo $this->translation->application_add ?></a></span>
    </td>
</table>
<table>
    <tr>
        <td id="queue-panel">
            <h2><?php echo $this->blog->name ?>'s <i>Queue</i></h2>
            <div id="queue-items-list">
            <table>
                <tr>
                    <td>
                        <div class="channel-item-title">Easy Christmas Homemade Edible Food Gifts</div>
                        <div class="channel-item-date">12/09/2008 10:02 PM</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="channel-item-title">Christmas diner starters</div>
                        <div class="channel-item-date">11/11/2008 04:02 PM</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="channel-item-title">Recipes for Diabetics</div>
                        <div class="channel-item-date">11/08/2008 05:18 AM</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
