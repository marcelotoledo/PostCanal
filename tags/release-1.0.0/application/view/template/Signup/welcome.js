var mytpl = null;

$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        signinbtn : $("#signin-button")
    };

    mytpl.signinbtn.click(function() 
    {
        location='./';
    });
});
