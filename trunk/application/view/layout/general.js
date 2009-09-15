var active_request = 0;

$.ajaxSetup(
{
    timeout: 30000
});

function set_active_request(b)
{
    active_request+= b ? 1 : -1;
    if(active_request<0) { active_request = 0; }
    (active_request > 0) ? $.b_spinner_start() : $.b_spinner_stop();
}

function do_request(t, u, d, c)
{
    $.ajax
    ({
        type: t, url: u, dataType: "xml", data: d,
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { c($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function server_error()
{
    if(active_request<=0) { document.location='./ouch'; }
}

function flash_message(m)
{
    $("#flashmessage").html(m).show();
    setTimeout("$(\"#flashmessage\").fadeOut(1900)", 100); // IE fix
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
