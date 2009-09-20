var my_layout = null;

$(document).ready(function()
{
    var my_layout =
    {
        main_container : $("#mainct"),
        top_row        : $("#toprow"),
        top_menu       : $("#topmenu"),
        mid_row        : $("#midrow"),
        menu_right_sep : $("#menursp")
    };

    function window_update()
    {
        /* fix top bar position and alignment */

        if(my_layout.mid_row.length>0)
        {
            my_layout.top_row.css('margin-left', my_layout.mid_row.offset().left);
        }

        if(my_layout.top_menu.length>0)
        {
            var _trr = $(window).width() -
                       my_layout.top_menu.position().left -
                       my_layout.top_menu.width() - 40;

            my_layout.menu_right_sep.width(_trr);
        }
    }

    $(window).resize(function()
    {
        window_update();
    });

    spinner_init(0);
    disable_submit();
    window_update();
});
