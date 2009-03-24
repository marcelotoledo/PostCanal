jQuery.b_dialog = function(config)
{
    var ww = $(window).width();
    var wh = $(window).height();

    function showdialog(dialog)
    {
        dialog.css('left', (ww * 0.5) - (dialog.outerWidth() * 0.5));
        dialog.css('top', (wh * 0.5) - (dialog.outerHeight() * 0.5));
        dialog.show();
    }

    /* show modal */

    if(config.modal == true)
    {
        $("body").prepend("<div class=\"b-dialog-modal\" " + 
                          "style=\"display:none\">&nbsp;</div>");

        $(".b-dialog-modal").width(ww);
        $(".b-dialog-modal").height(wh);
        $(".b-dialog-modal").css('opacity', 0.5);
        $(".b-dialog-modal").show();
    }

    /* existing div dialog */

    if(config.selector)
    {
        dialog = $(config.selector);

        showdialog(dialog);

        $(".b-dialog-close").click(function() 
        {
            dialog.hide(); 
            if(config.modal == true) { $(".b-dialog-modal").remove(); }
        });
    }

    /* message dialog */

    else if(config.message)
    {
        if(!config.close) { config.close = "close"; }

        $("body").prepend("<div class=\"b-dialog b-dialog-alert\" " + 
                          "     style=\"display:none\">" + 
                          "<div class=\"b-dialog-message\"></div><hr>" + 
                          "<div class=\"b-dialog-buttons\">" + 
                          "<a class=\"b-dialog-close\">" + 
                          config.close + "</a></div></div>");

        $(".b-dialog-message").html(config.message);

        dialog = $(".b-dialog-alert");

        showdialog(dialog);

        $(".b-dialog-close").click(function()
        {
            $(".b-dialog-alert").remove();
            if(config.modal == true) { $(".b-dialog-modal").remove(); }
        });
    }
}
