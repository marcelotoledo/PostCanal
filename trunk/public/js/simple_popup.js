/* simple (div) popup (requires jquery) 
 * thanks to http://reindel.com/five_javascript_tricks_jquery/ */ 

function simple_popup(message)
{
    var viewport = {
        o: function() {
            if (self.innerHeight) {
                this.pageYOffset = self.pageYOffset;
                this.pageXOffset = self.pageXOffset;
                this.innerHeight = self.innerHeight;
                this.innerWidth = self.innerWidth;
            } else if (document.documentElement && document.documentElement.clientHeight) {
                this.pageYOffset = document.documentElement.scrollTop;
                this.pageXOffset = document.documentElement.scrollLeft;
                this.innerHeight = document.documentElement.clientHeight;
                this.innerWidth = document.documentElement.clientWidth;
            } else if (document.body) {
                this.pageYOffset = document.body.scrollTop;
                this.pageXOffset = document.body.scrollLeft;
                this.innerHeight = document.body.clientHeight;
                this.innerWidth = document.body.clientWidth;
            }
            return this;
        },
        init: function(el) {
            $(el).css("left",Math.round(viewport.o().innerWidth/2) + viewport.o().pageXOffset - Math.round($(el).width()/2));
            $(el).css("top",Math.round(viewport.o().innerHeight/2) + viewport.o().pageYOffset - Math.round($(el).height()/2));
        }
    };

    if (message==null)
    {
        $(".simple-popup-div").remove();
    }
    else
    {
        var str = "";

        str += "<div class='simple-popup-div'>";
        str += "<div class='simple-popup-div-inner'>";
        str += message;
        str += " <a href='#' class='simple-popup-close'>[x]</a>";
        str += "</div></div>";
        $("body").append(str);
        viewport.init(".simple-popup-div");
        $(".simple-popup-close").click(function(){
            $(".simple-popup-div").remove();
            return false;
        });
        return false;
    }
}
