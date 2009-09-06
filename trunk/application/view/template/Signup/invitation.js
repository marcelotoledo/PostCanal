$(document).ready(function()
{
    function invitation_cb(d)
    {
        $("#invitetit").hide();
        $("#inviteform").hide();
        $("#invitemsg").show();
    }

    $("#invitemebtn").click(function()
    {
        var _fd = { name  : $("#input-name").val() , 
                    email : $("#input-email").val() };

        if(_fd.name=='' || _fd.email=='')
        {
            alert('form incomplete');
            return false;
        }

        do_request('POST', './signup/invitation', _fd, invitation_cb);
    });

    $("#gototour").click(function()
    {
        parent.document.location='./tour';
        return false;
    });
});
