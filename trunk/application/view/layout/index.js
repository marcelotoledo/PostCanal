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
            <?php if($this->browser_is_safari) : ?>
            var _ml = parseInt(my_layout.mid_row.css('margin-left'));
            <?php else : ?>
            var _ml = my_layout.mid_row.position().left;
            <?php endif ?>
            my_layout.top_row.css('margin-left', _ml);
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

    spinner_init();
    disable_submit();
    window_update();
});
