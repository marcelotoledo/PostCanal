var my_layout = null;

var my_blog =
{
    current : null,
    info    : Array()
};

function save_setting_callback(c, n)
{
    $(document).trigger('setting_' + c + '_' + n + '_saved');
}

function save_setting(c, n, v)
{
    $.ajax
    ({
        type: "POST",
        url: "/dashboard/setting",
        dataType: "xml",
        data: { context : c , name : n , value : v },
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { save_setting_callback(c, n); },
        error: function () { server_error(); }
    });
}

function blog_update_callback(k)
{
    $(document).trigger('blog_' + k + '_updated');
}

function blog_update(k, v)
{
    var _par = new Object;
        _par.blog = my_blog.current;
        _par[k] = v;

    $.ajax
    ({
        type: "POST",
        url: "/site/update",
        dataType: "xml",
        data: _par,
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { blog_update_callback(k); },
        error: function () { server_error(); }
    });
}

/* blog load (for queue) */

function blog_load_callback(d)
{
    d.find('result').children().each(function()
    {
        my_blog.info[($(this).context.nodeName)] = $(this).text();
    });
    $(document).trigger('blog_loaded');
}

function blog_load()
{
    if(my_blog.current==undefined) { return false; }
    do_request('GET', '/site/load', { blog: my_blog.current }, blog_load_callback);
}

$(document).ready(function()
{
    var my_layout =
    {
        main_container   : $("#mainct"),
        menu_top         : $("#menutop"),
        current_blog     : $("#currentblog"),
        blog_list        : $("#bloglstsel"),
        flash_message    : $("#flashmessage"),
        SPINNER_OFFSET_X : 70,
        FLASH_OFFSET_X   : 200
    };

    function selected_blog()
    {
        var _s = null;
        _s = my_layout.current_blog.val();
        _s = _s ? _s : my_layout.blog_list.find("option:selected").val();
        return _s;
    }

    function window_update()
    {
        /* fix spinner and flash message position / width */

        $('body').find('div.b-spinner').remove();

        spinner_init({ x : my_layout.menu_top.offset().left - 
                           my_layout.SPINNER_OFFSET_X, y : 0 });

        my_layout.flash_message.css('left', 
            my_layout.menu_top.offset().left + 
          ((my_layout.menu_top.width() - my_layout.flash_message.width()) / 2));
    }

    function initialize()
    {
        window_update();
        disable_submit();
        my_blog.current = selected_blog();
    }

    initialize();

    $(window).resize(function()
    {
        window_update();
    });

    $(document).bind('setting_blog_current_saved', function(e)
    {
        $(document).trigger('blog_changed');
    });

    if(my_layout.blog_list.find('option').length==0)
    {
        my_layout.blog_list.attr('disabled', true);
    }

    my_layout.blog_list.change(function()
    {
        if((my_blog.current = selected_blog()))
        {
            my_layout.blog_list.blur();
            save_setting('blog', 'current', my_blog.current);
        }
    });
});
