jQuery.b_spinner_start = function() { return false; }
jQuery.b_spinner_stop = function() { return false; }

$(document).ready(function()
{
    function invitation_cb(d)
    {
        $("#page0").hide();
        $("#page1").show();
    }

    $("#invitemebtn").click(function()
    {
        var _fd = { name  : $("#input-name").val() , 
                    email : $("#input-email").val() };

        if(_fd.name=='' || _fd.email.indexOf('@')==-1)
        {
            alert('Please fill up the form correctly.');
            return false;
        }

        do_request('POST', '/signup/invitation', _fd, invitation_cb);
    });

    $("#cancelbtn").click(function()
    {
        parent.document.location='<?php echo BASE_URL ?>';
        return false;
    });
});
