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
        container: $("#container"),


        disable_submit: function()
        {
            $("form").submit(function()
            {
                return false;
            });
        },

        container_update: function()
        {
            var ww = $(window).width(),
                wh = $(window).height();

            mylyt.container.css('left', (ww - mylyt.container.width()) / 2);
            mylyt.container.show();
        },

        init: function()
        {
            spinner_init();
            mylyt.disable_submit();
            mylyt.container_update();
        }
    };

    mylyt.init();

    $(window).resize(function()
    {
        mylyt.container_update();
    });
});
