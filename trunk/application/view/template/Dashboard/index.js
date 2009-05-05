$(document).ready(function()
{
    var top_bar = $("#topbar");
    var middle_content = $("#middlecontent");
    var bottom_bar = $("#bottombar");
    var queue_list_bar = $("#queuelistbar");

    var queue_display = 0; // 0 none | 1 half | 2 max
    var queue_height_control = $("#bottomrightbar span.hctl");

    /* DISPLAY FUNCTIONS */

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    /* window update / maximize containers */

    function window_update()
    {
        ww = $(window).width();
        wh = $(window).height();

        _t_h = top_bar.outerHeight();
        _b_t = bottom_bar.position().top;
        _b_h = bottom_bar.outerHeight();

        if(queue_display == 0)
        {
            bottom_bar.css('top', wh - _b_h);
            queue_height_control.removeClass('hctl-close');
            queue_height_control.addClass('hctl-open');
            queue_list_bar.hide();
        }
        if(queue_display == 1)
        {
            bottom_bar.css('top', wh / 2);
            queue_list_bar.show();
        }
        else if(queue_display == 2)
        {
            bottom_bar.css('top', _t_h);
            queue_height_control.removeClass('hctl-open');
            queue_height_control.addClass('hctl-close');
        }

        _b_t = bottom_bar.position().top;

        middle_content.css('top', _t_h);
        middle_content.css('left', 0);
        middle_content.width(ww);
        middle_content.height(wh - _t_h - _b_h);

        if(queue_list_bar.is(':visible'))
        {
            queue_list_bar.css('top', _b_t + _b_h);
            queue_list_bar.css('left', 0);
            queue_list_bar.width(ww);
            queue_list_bar.height(wh - _b_t - _b_h);
        }
    }

    window_update();

    <?php if(count($this->blogs) > 0) : ?>

    /* todo */

    <?php else : ?>

    $.b_dialog({ selector: "#noblogmsg", modal: true });
    $.b_dialog_show();

    <?php endif ?>

    /* EVENTS */

    $(window).resize(function()
    {
        window_update();
    });

    queue_height_control.click(function()
    {
        queue_display = (queue_display < 2) ? queue_display + 1 : 0;
        window_update();
    });
});
