var my_layout = null;

$(document).ready(function()
{
    var my_layout =
    {
        SPINNER_OFFSET_Y : 5
    };

    spinner_init({ x: 0, y: my_layout.SPINNER_OFFSET_Y });
    disable_submit();
});
