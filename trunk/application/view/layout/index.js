var active_request = 0;
var mylyt = null;


function set_active_request(b)
{
    active_request+= b ? 1 : -1;
    if(active_request<0) { active_request = 0; }
    (active_request > 0) ? $.b_spinner_start() : $.b_spinner_stop();
}

function server_error()
{
    // alert("<?php echo $this->translation()->server_error ?>");
    console.error('server error');
}

function spinner_init()
{
    $.b_spinner
    ({
        image: "/image/spinner.gif", 
        message: "... <?php echo $this->translation()->loading ?>"
    });
}

function disable_submit()
{
    $("form").each(function()
    {
        if($(this).attr('action')=="")
        {
            $(this).submit(function()
            {
                return false;
            });
        }
    });
}

$(document).ready(function()
{
    var mylyt =
    {
        container: $("#container")
    };

    function container_update()
    {
        var ww = $(window).width(),
            wh = $(window).height();

        mylyt.container.css('left', (ww - mylyt.container.width()) / 2);
        mylyt.container.show();
    }

    spinner_init();
    disable_submit();
    container_update();

    $(window).resize(function()
    {
        container_update();
    });
});
