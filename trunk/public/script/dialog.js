jQuery.b_dialog=function(b){var e=$(window).width();var a=$(window).height();var d=null;var c=(b.selector)?$(b.selector):null;if(b.modal==true&&d==null){$("body").prepend('<div class="b-dialog-modal" style="display:none">&nbsp;</div>');d=$(".b-dialog-modal");d.width(e);d.height(a);d.css("opacity",0.5)}if(b.message){if(!b.close){b.close="close"}if(c==null){$("body").prepend('<div class="b-dialog b-dialog-alert" style="display:none"><div class="b-dialog-message"></div><hr><div class="b-dialog-buttons"><a class="b-dialog-close">'+b.close+"</a></div></div>")}$(".b-dialog-message").html(b.message);c=$(".b-dialog-alert")}if(c!=null){c.css("left",(e*0.5)-(c.outerWidth()*0.5));c.css("top",(a*0.5)-(c.outerHeight()*0.5))}$.b_dialog_show=function(){if(c){c.show()}if(d){d.show()}};$.b_dialog_hide=function(){if(c){c.hide()}if(d){d.hide()}};$(".b-dialog-close").click(function(){$.b_dialog_hide()})};