 jQuery.b_spinner_start = function (config) { var viewport = { v: function() { if (self.innerWidth) { this.pageXOffset = self.pageXOffset; this.innerWidth = self.innerWidth; }
 else if (document.documentElement && document.documentElement.clientWidth) { this.pageXOffset = document.documentElement.scrollLeft; this.innerWidth = document.documentElement.clientWidth; }
 else if (document.body) { this.pageXOffset = document.body.scrollLeft; this.pageYOffset = document.body.scrollTop; this.innerWidth = document.body.clientWidth; this.innerHeight = document.body.clientHeight; }
 return this; }
, init: function(element) { element.css("left", Math.round(viewport.v().innerWidth / 2) + viewport.v().pageXOffset - Math.round(element.width() / 2)); element.css("top", viewport.v().pageYOffset); }
 }
; if(!config.message) { config.message = "... loading"; }
 var _s = ""; _s += "<div class='b-spinner-div'><table><tr><td>"; _s += "<div class='b-spinner-div-inner'>"; _s += "</div></td><td>" + config.message + "</td></tr></table></div>"; $("body").append(_s); var container = $(".b-spinner-div"); var inner = $(".b-spinner-div-inner"); viewport.init(container);  if(config.height) { inner.height(config.height); }
  if(config.width) { inner.width(config.width); }
  if(config.image) { inner.css("background-image", "url(" + config.image + ")"); inner.css("background-position", "0px 0px"); inner.css("background-repeat", "no-repeat"); }
 else { config.image = inner.css("background-image"); }
  $.b_spinner_stop = function() { container.remove(); }
; }
