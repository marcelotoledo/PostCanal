/* spinner */

jQuery.b_spinner=function(a){var b=$(window).width();$("body").prepend('<div class="b-spinner" style="display:none"><div class="b-spinner-image"><img src="'+a.image+'"></div><div class="b-spinner-message">'+a.message+"</div></div>");spinner=$(".b-spinner");spinner.css("top",0);spinner.css("left",(b*0.5)-(spinner.outerWidth()*0.5));$.b_spinner_start=function(){$(".b-spinner").show()};$.b_spinner_stop=function(){$(".b-spinner").hide()}};

/* dialog */

jQuery.b_dialog=function(b){var e=$(window).width();var a=$(window).height();var d=null;var c=(b.selector)?$(b.selector):null;if(b.modal==true&&d==null){$("body").prepend('<div class="b-dialog-modal"      style="display:none">&nbsp;</div>');d=$(".b-dialog-modal");d.width(e);d.height(a);d.css("opacity",0.5)}if(b.message){if(!b.close){b.close="close"}if(c==null){$("body").prepend('<div class="b-dialog b-dialog-alert"      style="display:none"><div class="b-dialog-message"></div><hr><div class="b-dialog-buttons"><a class="b-dialog-close">'+b.close+"</a></div></div>")}$(".b-dialog-message").html(b.message);c=$(".b-dialog-alert")}if(c!=null){c.css("left",(e*0.5)-(c.outerWidth()*0.5));c.css("top",(a*0.5)-(c.outerHeight()))}$.b_dialog_hide=function(){if(c){c.hide()}if(d){d.hide()}};$.b_dialog_show=function(){if(c){c.show()}if(d){d.show()}$(document).bind("click.b-dialog",function(){$.b_dialog_hide();$(document).unbind("click.b-dialog")});$(document).bind("keypress.b-dialog",function(){$.b_dialog_hide();$(document).unbind("keypress.b-dialog")})}};

/* txtoverflow */

jQuery.fn.b_txtoverflow=function(f){var d=null;var g=null;var e=null;var a=null;var c=null;var b=0;if(f!=undefined){if(f.buffer!=undefined){d=f.buffer}}if(d==undefined){d=$("#b_txtoverflow-buffer")}if(d==undefined){return false}if(f!=undefined){if(f.width!=undefined){e=parseInt(f.width)}}if(e==null){e=$(this).width()}if(e==0){e=500}if(f!=undefined){if(f.text!=undefined){a=f.text}}d.css("font-family",$(this).css("font-family"));d.css("font-size",$(this).css("font-size"));d.css("font-style",$(this).css("font-style"));d.css("font-variant",$(this).css("font-variant"));d.css("font-weight",$(this).css("font-weight"));if(a!=null){d.text(a)}else{$(this).find("span.hellip").remove();d.text($(this).text())}g=parseInt(d.width())+1;if(e<g){a=d.text();b=parseInt(e/(g/a.length))-1;$(this).html(a.substring(0,b)+'<span class="hellip">&hellip;</span><span style="display:none">'+a.substring(b)+"</span>")}else{if(a!=null){$(this).text(a)}}};

/* modal */

jQuery.fn.b_modal = function()
{
    $('body').append('<div class="b-modal">&nbsp;</div>');
    $('body').find('div.b-modal').css('opacity', 0.75);

   var _rect = { L : $(window).width()  * 0.1 ,
                 T :                       50 ,
                 W : $(window).width()  * 0.8 ,
                 H : $(window).height()  -100 };

    $(this).css('top', _rect.T);
    $(this).css('left', _rect.L);
    $(this).css('width', _rect.W);
    $(this).css('height', _rect.H);

    $(this).show();
}
jQuery.fn.b_modal_close = function()
{
    $(this).hide();
    $('body').find('div.b-modal').remove();
}

/* timezone */

function getBrowserTimezoneName(){tmSummer=new Date(Date.UTC(2005,6,30,0,0,0,0));so=-1*tmSummer.getTimezoneOffset();tmWinter=new Date(Date.UTC(2005,12,30,0,0,0,0));wo=-1*tmWinter.getTimezoneOffset();if(-660==so&&-660==wo){return"Pacific/Midway"}if(-600==so&&-600==wo){return"Pacific/Tahiti"}if(-570==so&&-570==wo){return"Pacific/Marquesas"}if(-540==so&&-600==wo){return"America/Adak"}if(-540==so&&-540==wo){return"Pacific/Gambier"}if(-480==so&&-540==wo){return"US/Alaska"}if(-480==so&&-480==wo){return"Pacific/Pitcairn"}if(-420==so&&-480==wo){return"US/Pacific"}if(-420==so&&-420==wo){return"US/Arizona"}if(-360==so&&-420==wo){return"US/Mountain"}if(-360==so&&-360==wo){return"America/Guatemala"}if(-360==so&&-300==wo){return"Pacific/Easter"}if(-300==so&&-360==wo){return"US/Central"}if(-300==so&&-300==wo){return"America/Bogota"}if(-240==so&&-300==wo){return"US/Eastern"}if(-240==so&&-240==wo){return"America/Caracas"}if(-240==so&&-180==wo){return"America/Santiago"}if(-180==so&&-240==wo){return"Canada/Atlantic"}if(-180==so&&-180==wo){return"America/Montevideo"}if(-180==so&&-120==wo){return"America/Sao_Paulo"}if(-150==so&&-210==wo){return"America/St_Johns"}if(-120==so&&-180==wo){return"America/Godthab"}if(-120==so&&-120==wo){return"America/Noronha"}if(-60==so&&-60==wo){return"Atlantic/Cape_Verde"}if(0==so&&-60==wo){return"Atlantic/Azores"}if(0==so&&0==wo){return"Africa/Casablanca"}if(60==so&&0==wo){return"Europe/London"}if(60==so&&60==wo){return"Africa/Algiers"}if(60==so&&120==wo){return"Africa/Windhoek"}if(120==so&&60==wo){return"Europe/Amsterdam"}if(120==so&&120==wo){return"Africa/Harare"}if(180==so&&120==wo){return"Europe/Athens"}if(180==so&&180==wo){return"Africa/Nairobi"}if(240==so&&180==wo){return"Europe/Moscow"}if(240==so&&240==wo){return"Asia/Dubai"}if(270==so&&210==wo){return"Asia/Tehran"}if(270==so&&270==wo){return"Asia/Kabul"}if(300==so&&240==wo){return"Asia/Baku"}if(300==so&&300==wo){return"Asia/Karachi"}if(330==so&&330==wo){return"Asia/Calcutta"}if(345==so&&345==wo){return"Asia/Katmandu"}if(360==so&&300==wo){return"Asia/Yekaterinburg"}if(360==so&&360==wo){return"Asia/Colombo"}if(390==so&&390==wo){return"Asia/Rangoon"}if(420==so&&360==wo){return"Asia/Almaty"}if(420==so&&420==wo){return"Asia/Bangkok"}if(480==so&&420==wo){return"Asia/Krasnoyarsk"}if(480==so&&480==wo){return"Australia/Perth"}if(540==so&&480==wo){return"Asia/Irkutsk"}if(540==so&&540==wo){return"Asia/Tokyo"}if(570==so&&570==wo){return"Australia/Darwin"}if(570==so&&630==wo){return"Australia/Adelaide"}if(600==so&&540==wo){return"Asia/Yakutsk"}if(600==so&&600==wo){return"Australia/Brisbane"}if(600==so&&660==wo){return"Australia/Sydney"}if(630==so&&660==wo){return"Australia/Lord_Howe"}if(660==so&&600==wo){return"Asia/Vladivostok"}if(660==so&&660==wo){return"Pacific/Guadalcanal"}if(690==so&&690==wo){return"Pacific/Norfolk"}if(720==so&&660==wo){return"Asia/Magadan"}if(720==so&&720==wo){return"Pacific/Fiji"}if(720==so&&780==wo){return"Pacific/Auckland"}if(765==so&&825==wo){return"Pacific/Chatham"}if(780==so&&780==wo){return"Pacific/Enderbury"}if(840==so&&840==wo){return"Pacific/Kiritimati"}return"UTC"};
