var my_template = null;

function send_email_complete()
{
    my_template.email_form.hide();
    my_template.email_message.show();
}

function send_email_cb(d)
{
    if(d.length==0) { server_error(); return null; }
    send_email_complete();
}

function send_email()
{
    var _data = { name    : my_template.input_name.val(),
                  email   : my_template.input_email.val(),
                  subject : my_template.input_subject.val(),
                  message : my_template.input_message.val() }

    if(_data.name == '' ||
       _data.email.indexOf('@')==-1 ||
       _data.subject == '' ||
       _data.message == '')
    {
        alert('please fill up the form correctly');
        my_template.input_name.focus();
        return false;
    }

    $(window).scrollTop(0);
    do_request('POST', '/support', _data, send_email_cb);
}


$(document).ready(function()
{
    /* template vars */

    my_template = 
    {
        email_form    : $("#emailfrm"),
        email_message : $("#emailmsg"),
        input_name    : $("#inp-name"),
        input_email   : $("#inp-email"),
        input_subject : $("#inp-subject"),
        input_message : $("#inp-message"),
        button_send   : $("#btn-send")
    };

    my_template.button_send.click(function()
    {
        send_email();
    });

    my_template.input_name.focus();
});
