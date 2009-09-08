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

        if(_fd.name=='' || _fd.email.indexOf('@')==-1)
        {
            alert('please fill up the form correctly');
            return false;
        }

        do_request('POST', './signup/invitation', _fd, invitation_cb);
    });

    $("#gotomain").click(function()
    {
        parent.document.location='./';
        return false;
    });
});
