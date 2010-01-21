jQuery.fn.b_txtoverflow=function(f){var d=null;var g=null;var e=null;var a=null;var c=null;var b=0;if(f!=undefined){if(f.buffer!=undefined){d=f.buffer}}if(d==undefined){d=$("#b_txtoverflow-buffer")}if(d==undefined){return false}if(f!=undefined){if(f.width!=undefined){e=parseInt(f.width)}}if(e==null){e=$(this).width()}if(e==0){e=500}if(f!=undefined){if(f.text!=undefined){a=f.text}}d.css("font-family",$(this).css("font-family"));d.css("font-size",$(this).css("font-size"));d.css("font-style",$(this).css("font-style"));d.css("font-variant",$(this).css("font-variant"));d.css("font-weight",$(this).css("font-weight"));if(a!=null){d.text(a)}else{$(this).find("span.hellip").remove();d.text($(this).text())}g=parseInt(d.width())+1;if(e<g){a=d.text();b=parseInt(e/(g/a.length))-1;$(this).html(a.substring(0,b)+'<span class="hellip">&hellip;</span><span style="display:none">'+a.substring(b)+"</span>")}else{if(a!=null){$(this).text(a)}}};
jQuery.b_spinner=function(a){var b=$(window).width();$("body").prepend('<div class="b-spinner" style="display:none"><div class="b-spinner-image"><img src="'+a.image+'"></div><div class="b-spinner-message">'+a.message+"</div></div>");spinner=$(".b-spinner");spinner.css("top",a.offset.y);spinner.css("left",((b+a.offset.x)*0.5)-(spinner.outerWidth()*0.5));$.b_spinner_start=function(){$(".b-spinner").show()};$.b_spinner_stop=function(){$(".b-spinner").hide()}};
jQuery.fn.pc_literalTime=function(b){var i=null;if(typeof b=="object"&&typeof b.t=="number"){i=b.t}else{i=parseInt($(this).attr("time"))}if(isNaN(i)){return false}var d=Math.abs(i);var c={p:{b:null,a:"ago"},f:{b:"in",a:null}};if(typeof b=="object"&&typeof b.m=="object"){c=b.m}var a={y:"year",Y:"years",m:"month",M:"months",d:"day",D:"days",h:"hour",H:"hours",i:"min",I:"min",s:"sec",S:"sec"};if(typeof b=="object"&&typeof b.n=="object"){a=b.n}var e=" and ";if(typeof b=="object"&&b.g!=undefined){e=b.g}var f=[{t:31104000,s:a.y,p:a.Y},{t:2592000,s:a.m,p:a.M},{t:86400,s:a.d,p:a.D},{t:3600,s:a.h,p:a.H},{t:60,s:a.i,p:a.I}];var h=1;var g=0;var j=(i>0)?((c.f.b==null)?"":c.f.b+" "):((c.p.b==null)?"":c.p.b+" ");jQuery.each(f,function(){if(d>this.t&&h>0){var l=(h==1)?(d/this.t):Math.floor(d/this.t);var k=(l-Math.floor(l));l=(k<=0.5&&k>0)?((l>0)?Math.floor(l):Math.ceil(l))+"½":Math.ceil(l);d-=(l*this.t);j=j+((g>0)?e:" ")+l+" "+(l>1?this.p:this.s);h--;g++}});if(h>0){j=j+((g>0)?e:" ")+d+" "+(d>1?a.S:a.s)}j=j+" "+((i>0)?((c.f.a==null)?"":c.f.a+" "):((c.p.a==null)?"":c.p.a+" "));$(this).text(j)};
jQuery.b_dialog=function(b){var e=$(window).width();var a=$(window).height();var d=null;var c=(b.selector)?$(b.selector):null;if(b.modal==true&&d==null){$("body").prepend('<div class="b-dialog-modal"      style="display:none">&nbsp;</div>');d=$(".b-dialog-modal");d.width(e);d.height(a);d.css("opacity",0.5)}if(b.message){if(!b.close){b.close="close"}if(c==null){$("body").prepend('<div class="b-dialog b-dialog-alert"      style="display:none"><div class="b-dialog-message"></div><hr><div class="b-dialog-buttons"><a class="b-dialog-close">'+b.close+"</a></div></div>")}$(".b-dialog-message").html(b.message);c=$(".b-dialog-alert")}if(c!=null){c.css("left",(e*0.5)-(c.outerWidth()*0.5));c.css("top",(a*0.5)-(c.outerHeight()))}$.b_dialog_hide=function(){if(c){c.hide()}if(d){d.hide()}};$.b_dialog_show=function(){if(c){c.show()}if(d){d.show()}$(document).bind("click.b-dialog",function(){$.b_dialog_hide();$(document).unbind("click.b-dialog")});$(document).bind("keypress.b-dialog",function(){$.b_dialog_hide();$(document).unbind("keypress.b-dialog")})}};
jQuery.cookie=function(b,j,m){if(typeof j!="undefined"){m=m||{};if(j===null){j="";m.expires=-1}var e="";if(m.expires&&(typeof m.expires=="number"||m.expires.toUTCString)){var f;if(typeof m.expires=="number"){f=new Date();f.setTime(f.getTime()+(m.expires*24*60*60*1000))}else{f=m.expires}e="; expires="+f.toUTCString()}var l=m.path?"; path="+(m.path):"";var g=m.domain?"; domain="+(m.domain):"";var a=m.secure?"; secure":"";document.cookie=[b,"=",encodeURIComponent(j),e,l,g,a].join("")}else{var d=null;if(document.cookie&&document.cookie!=""){var k=document.cookie.split(";");for(var h=0;h<k.length;h++){var c=jQuery.trim(k[h]);if(c.substring(0,b.length+1)==(b+"=")){d=decodeURIComponent(c.substring(b.length+1));break}}}return d}};
jQuery.fn.b_modal=function(c,a){$("body").append('<div class="b-modal">&nbsp;</div>');$("body").find("div.b-modal").css("opacity",0.75);c=c||($(window).width()*0.8);a=a||($(window).height()-100);var b={L:(($(window).width()-c)/2),T:(($(window).height()-a)/2),W:c,H:a};$(this).css("top",b.T);$(this).css("left",b.L);$(this).css("width",b.W);$(this).css("height",b.H);$(this).show()};jQuery.fn.b_modal_close=function(){$(this).hide();$("body").find("div.b-modal").remove()};