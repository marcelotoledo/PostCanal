$(document).ready(function()
{
    <?php if($this->registry()->request()->object->getController() != "dashboard"): ?>

    /* maximize middle content */

    var top_bar = $("#topbar");
    var middle_content = $("#middlecontent");

    function window_update()
    {
        ww = $(window).outerWidth();
        wh = $(window).height();

        _t_h = top_bar.outerHeight();

        middle_content.css('top', _t_h);
        middle_content.css('left');
        middle_content.width(ww);
        middle_content.height(wh - _t_h);
    }

    window_update();

    $(window).resize(function()
    {
        window_update();
    });

    <?php endif ?>
});
