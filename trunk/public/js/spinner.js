jQuery.b_spinner = function(config)
{
    var ww = $(window).width();

    $("body").prepend("<div class=\"b-spinner\" style=\"display:none\">" +
                      "<div class=\"b-spinner-image\">" +
                      "<img src=\"" + config.image + "\"></div>" +
                      "<div class=\"b-spinner-message\">" + config.message +
                      "</div></div>");

    spinner = $(".b-spinner");
    spinner.css('top', 0);
    spinner.css('left', ((ww - config.offset) * 0.5) - (spinner.outerWidth() * 0.5));

    $.b_spinner_start = function()
    {
        $(".b-spinner").show();
    }

    $.b_spinner_stop = function()
    {
        $(".b-spinner").hide();
    }
}
