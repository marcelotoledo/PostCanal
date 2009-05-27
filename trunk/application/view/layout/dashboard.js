var active_request = false;
var mylyt = null;
var current_blog = null;


function set_active_request(b)
{
    ((active_request = b) == true) ?
        $.b_spinner_start() :
        $.b_spinner_stop();
}

function server_error()
{
    alert("<?php echo $this->translation()->server_error ?>");
}

function spinner_init()
{
    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>", 
        message: "... <?php echo $this->translation()->loading ?>"
    });
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

    function disable_submit()
    {
        $("form").submit(function()
        {
            return false;
        });
    }

    function container_update()
    {
        var ww = $(window).width(),
            wh = $(window).height(),
            th = mylyt.top_bar.outerHeight();

        mylyt.main_container.css('top', th);
        mylyt.main_container.css('left');
        mylyt.main_container.width(ww);
        mylyt.main_container.height(wh - th);
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
    current_blog = selected_blog();

    $(window).resize(function()
    {
        container_update();
    });

    if(mylyt.blog_list.length>0)
    {
        mylyt.blog_list.change(function()
        {
            current_blog = selected_blog();
            mylyt.blog_list.blur();
            $(document).trigger('blogchange');
        });
    }
});
