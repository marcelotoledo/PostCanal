var mytpl = null;

$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        signinbtn    : $("#signin_button")
    };

    mytpl.signinbtn.click(function() 
    {
        location='./';
    });
});
