jQuery.b_dialog = function(config)
{
    var ww = $(window).width();
    var wh = $(window).height();

    var modal  = null;
    var dialog = (config.selector) ? $(config.selector) : null;

    /* configure modal */

    if(config.modal == true && modal == null)
    {
        $("body").prepend("<div class=\"b-dialog-modal\" " + 
                          "     style=\"display:none\">&nbsp;</div>");

        modal = $(".b-dialog-modal");
        modal.width(ww);
        modal.height(wh);
        modal.css('opacity', 0.5);
    }

    /* configure message */

    if(config.message)
    {
        if(!config.close) { config.close = "close"; }

        if(dialog == null)
        {
            $("body").prepend("<div class=\"b-dialog b-dialog-alert\" " + 
                              "     style=\"display:none\">" + 
                              "<div class=\"b-dialog-message\"></div><hr>" + 
                              "<div class=\"b-dialog-buttons\">" + 
                              "<a class=\"b-dialog-close\">" + 
                              config.close + "</a></div></div>");
        }

        $(".b-dialog-message").html(config.message);

        dialog = $(".b-dialog-alert");
    }

    if(dialog != null)
    {
        dialog.css('left', (ww * 0.5) - (dialog.outerWidth() * 0.5));
        dialog.css('top', (wh * 0.5) - (dialog.outerHeight()));
    }

    /* actions */

    $.b_dialog_hide = function()
    {
        if(dialog) { dialog.hide(); }
        if(modal)  { modal.hide();  }
    }

    $.b_dialog_show = function()
    {
        if(dialog) { dialog.show(); }
        if(modal)  { modal.show();  }

        $(document).bind('click.b-dialog', function()
        {
            $.b_dialog_hide();
            $(document).unbind('click.b-dialog');
        })

        $(document).bind('keypress.b-dialog', function()
        {
            $.b_dialog_hide();
            $(document).unbind('keypress.b-dialog');
        })
    }
}
