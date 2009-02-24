$(document).ready(function()
{
    /* DEFAULTS */


    /* FX */

    $(".cms-item").hover
    (
        function()
        {
            $(this).addClass("cms-item-hover");
        },
        function()
        {
            $(this).removeClass("cms-item-hover");
        }
    );

    /* TRIGGERS */

    $(".cms-item").click(function()
    {
        alert($(this).html());
    });
});
