var mylyt = null;

var blog =
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
        url: "./dashboard/setting",
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
        _par.blog = blog.current;
        _par[k] = v;

    $.ajax
    ({
        type: "POST",
        url: "./blog/update",
        dataType: "xml",
        data: _par,
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { blog_update_callback(k); },
        error: function () { server_error(); }
    });
}

function blog_load_callback(d)
{
    d.find('result').children().each(function()
    {
        blog.info[($(this).context.nodeName)] = $(this).text();
    });
    $(document).trigger('blog_loaded');
}

function blog_load()
{
    if(blog.current==undefined) { return false; }
    do_request('GET', './blog/load', { blog: blog.current }, blog_load_callback);
}

$(document).ready(function()
{
    var mylyt =
    {
        top_bar        : $("#topbar"),
        main_container : $("#maincontainer"),
        current_blog   : $("#currentblog"),
        blog_list      : $("#bloglstsel")
    };

    function container_update()
    {
        var ww = $(window).width(),
            wh = $(window).height(),
            th = mylyt.top_bar.outerHeight(),
            ph = parseInt(mylyt.main_container.css('padding-left')) +
                 parseInt(mylyt.main_container.css('padding-right')),
            pv = parseInt(mylyt.main_container.css('padding-top')) +
                 parseInt(mylyt.main_container.css('padding-bottom'));

        mylyt.main_container.css('top', th);
        mylyt.main_container.css('left', 0);
        mylyt.main_container.width(ww - ph);
        mylyt.main_container.height(wh - th - pv);
    }

    function selected_blog()
    {
        var _s = null;
        _s = mylyt.current_blog.val();
        _s = _s ? _s : mylyt.blog_list.find("option:selected").val();
        return _s;
    }

    spinner_init();
    disable_submit();
    container_update();
    blog.current = selected_blog();

    $(window).resize(function()
    {
        container_update();
    });

    $(document).bind('setting_blog_current_saved', function(e)
    {
        $(document).trigger('blog_changed');
    });

    if(mylyt.blog_list.length>0)
    {
        mylyt.blog_list.change(function()
        {
            if((blog.current = selected_blog()))
            {
                mylyt.blog_list.blur();
                save_setting('blog', 'current', blog.current);
            }
        });
    }

    /* avoid showing partial loaded content */

    mylyt.main_container.show();
});
