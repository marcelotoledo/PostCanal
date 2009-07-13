var mytpl = null;

var magic_slh = 10;


function on_blog_change()
{
}

$(document).ready(function()
{
    mytpl =
    {
        main_container       : $("#maincontainer"),
        queue_container      : $("#queuecontainer"),
        queue_header_title   : $("#queueheadertitle"),
        queue_middle         : $("#queuemiddle"),
        article_list         : $("#queuemiddle"),
        article_blank        : $("#articleblank"),
        content_blank        : $("#contentblank"),
        queue_middle_area    : { x : 0 , y : 0 , w : 0 , h : 0 },
        queue_middle_hover   : false,
        queue_footer         : $("#queuefooter"),
        article_expanded_lnk : $("#articleexpandedlnk"),
        article_expanded_lab : $("#articleexpandedlab"),
        article_list_lnk     : $("#articlelistlnk"),
        article_list_lab     : $("#articlelistlab"),
        article_next         : $("#articlenext"),
        article_prev         : $("#articleprev")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            mytpl.main_container.position().top,
                   width  : $(window).width() };

        mytpl.queue_container.width(_w.width);
        mytpl.queue_container.height(_w.height);
        mytpl.queue_middle.height(_w.height - mytpl.queue_middle.position().top);

        mytpl.queue_middle_area.x = mytpl.queue_middle.offset().left;
        mytpl.queue_middle_area.y = mytpl.queue_middle.offset().top;
        mytpl.queue_middle_area.w = mytpl.queue_middle.width();
        mytpl.queue_middle_area.h = mytpl.queue_middle.height();
    }

    function initialize()
    {
        window_update();
    }

    /* events */

    $(window).resize(function()
    {
        window_update();
    });

    function on_mouse_wheel(e)
    {
        if(mytpl.queue_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            mytpl.queue_middle.scrollTop(mytpl.queue_middle.scrollTop() - j);
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

        mytpl.queue_middle_hover = mouse_is_over_area(_mp.x, _mp.y, mytpl.queue_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
