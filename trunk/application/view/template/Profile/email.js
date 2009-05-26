$(document).ready(function()
{
    function email_change()
    {
        email = $("#email").val();
        password = $("#password").val();
        user = $("#user").val();

        if(password == "")
        {
            alert("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        parameters = { email: email, password: password, user: user }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile','email') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function() { set_active_request(true);  },
            complete: function()   { set_active_request(false); },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var accepted = data.find('accepted').text();
                var message = data.find('message').text();

                if(accepted == "true") 
                {
                    $("#emlform").toggle();
                    $("#changenotice").toggle();
                }
                else
                {
                    if(message != "") { alert(message); }
                }
            }, 
            error: function (data) { error_message(); }
        });
    };


    /* TRIGGERS */

    $("#emlchangesubmit").click(function() 
    {
        if(active_request==false) {  email_change(); }
    });
});
