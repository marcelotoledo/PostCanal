/** 
 * AB Spinner
 *
 * Based on JQuery Spinner plugin
 * Thanks to http://www.command-tab.com/2007/05/07/jquery-spinner-plugin 
 *
 * @requires JQuery
 */ 

jQuery.ab_spinner = function (config)
{
    var viewport = 
    {
        v: function() 
        {
            if (self.innerWidth) 
            {
                this.pageXOffset = self.pageXOffset;
                this.innerWidth = self.innerWidth;
            } 
            else if (document.documentElement && 
                     document.documentElement.clientWidth) 
            {
                this.pageXOffset = document.documentElement.scrollLeft;
                this.innerWidth = document.documentElement.clientWidth;
            }
            else if (document.body) 
            {
                this.pageXOffset = document.body.scrollLeft;
                this.innerWidth = document.body.clientWidth;
            }
            return this;
        },

        init: function(element) 
        {
            element.css("left", 
                Math.round(viewport.v().innerWidth / 2) + 
                viewport.v().pageXOffset - 
                Math.round(element.width() / 2));
            element.css("top", 0);
        }
    };

    if(!config.message)
    {
        config.message = "... loading";
    }

    var _s = "";

    _s += "<div class='ab-spinner-div'><table><tr><td>";
    _s += "<div class='ab-spinner-div-inner'>";
    _s += "</div></td><td>" + config.message + "</td></tr></table></div>";
    $("body").append(_s);

    var container = $(".ab-spinner-div");
    var inner = $(".ab-spinner-div-inner");

    viewport.init(container);

    /* set spinner height */

    if(config.height) 
    {
        inner.height(config.height);
    }
    
    /* set spinner width */

    if(config.width) 
    {
        inner.width(config.width);
    }

    /* set or get the spinner image */

    if(config.image)
    {
        inner.css("background-image", "url(" + config.image + ")");
        inner.css("background-position", "0px 0px");
        inner.css("background-repeat", "no-repeat");
    } 
    else 
    {
        config.image = inner.css("background-image");
    }
    
    /* determine how many frames exist */

    var frame  = 1;
    var frames = 1;

    img = new Image();
    img.src = config.image;
    img.onload = function() 
    {
        frames = img.width / config.width;
    };
    
    /* set the frame speed */

    if(!config.speed) 
    {
        config.speed = 5;
    }

    /* update the drawing area by adjusting the background-image */

    function spinnerRedraw() 
    {
        /* if we've reached the last frame, loop back around */

        if(frame >= frames) 
        {
            frame = 1;
        }
        
        /* set the background-position for this frame */

        pos = "-" + (frame * config.width) + "px 0px";
        inner.css("background-position", pos);
        
        /* increment the frame count */

        frame++;
    }
    
    /* kick off the animation */

    var animation = setInterval(spinnerRedraw,config.speed);
    
    /* call $.ab_spinner_stop to halt the spinner */

    $.ab_spinner_stop = function() 
    {
        clearInterval(animation);
        container.remove();
    };
}
