$(document).ready(function()
{
    /* maximize main container */

    var top_bar = $("#topbar");
    var main_container = $("#maincontainer");

    function window_update()
    {
        ww = $(window).outerWidth();
        wh = $(window).height();

        _t_h = top_bar.outerHeight();

        main_container.css('top', _t_h);
        main_container.css('left');
        main_container.width(ww);
        main_container.height(wh - _t_h);
    }

    window_update();

    $(window).resize(function()
    {
        window_update();
    });
});
