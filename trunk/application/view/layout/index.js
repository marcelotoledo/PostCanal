var active_request = false;
var mylyt = null;


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
        container: $("#container")
    };

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
