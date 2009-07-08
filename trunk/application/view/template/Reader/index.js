var mytpl = null;

var magic_slh = 10;

$(document).ready(function()
{
    mytpl =
    {
        main_container     : $("#maincontainer"),
        left_container     : $("#leftcontainer"),
        right_container    : $("#rightcontainer"),
        right_middle       : $("#rightmiddle"),
        right_middle_area  : { x : 0 , y : 0 , w : 0 , h : 0 },
        right_middle_hover : false,
        right_footer       : $("#rightfooter"),
        subscribed_list    : $("#subscribedfeedslist")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            mytpl.main_container.position().top,
                   width  : $(window).width() };

        mytpl.left_container.height(_w.height);
        mytpl.right_container.width(_w.width -
                                    mytpl.left_container.width());
        mytpl.right_container.height(_w.height);
        mytpl.right_container.css('left', mytpl.left_container.width());
        mytpl.subscribed_list.height(_w.height - 
                                     mytpl.subscribed_list.position().top - 
                                     magic_slh);
        mytpl.right_middle.height(_w.height - mytpl.right_middle.offset().top);

        mytpl.right_middle_area.x = mytpl.right_middle.offset().left;
        mytpl.right_middle_area.y = mytpl.right_middle.offset().top;
        mytpl.right_middle_area.w = mytpl.right_middle.width();
        mytpl.right_middle_area.h = mytpl.right_middle.height();
    }

    function initialize()
    {
        window_update();
    }

    /* triggers */

    $(window).resize(function()
    {
        window_update();
    });

    function on_mouse_wheel(e)
    {
        if(mytpl.right_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            mytpl.right_middle.scrollTop(mytpl.right_middle.scrollTop() - j);
            return false;
        }
    }

    $(window).bind('DOMMouseScroll', function(e)
    {
        on_mouse_wheel(e);
    });

    $(document).bind('onmousewheel', function(e) /* Mozilla */
    {
        on_mouse_wheel(e);
    });

    window.onmousewheel = document.onmousewheel = on_mouse_wheel; /* IE */

    function mouse_is_over_area(x, y, a)
    {
        return ((x >= a.x && x <= (a.x + a.w)) && (y >= a.y && y <= (a.y + a.h)));
    }

    function on_mouse_move(e)
    {
        var _mp = { x : 0 , y : 0 };
        
        e = e ? e : window.event;

        _mp.x = (e.pageX) ? e.pageX : e.clientX + document.body.scrollLeft;
        _mp.y = (e.pageY) ? e.pageY : e.clientY + document.body.scrollTop;

        mytpl.right_middle_hover = mouse_is_over_area(_mp.x, _mp.y, mytpl.right_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    initialize();
});
