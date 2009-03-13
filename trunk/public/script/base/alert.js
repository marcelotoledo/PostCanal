jQuery.ab_alert = function (message) { var viewport = { v: function() { if (self.innerWidth) { this.pageXOffset = self.pageXOffset; this.pageYOffset = self.pageYOffset; this.innerWidth = self.innerWidth; this.innerHeight = self.innerHeight; }
 else if (document.documentElement && document.documentElement.clientWidth) { this.pageXOffset = document.documentElement.scrollLeft; this.pageYOffset = document.documentElement.scrollTop; this.innerWidth = document.documentElement.clientWidth; this.innerHeight= document.documentElement.clientHeight; }
 else if (document.body) { this.pageXOffset = document.body.scrollLeft; this.pageYOffset = document.body.scrollTop; this.innerWidth = document.body.clientWidth; this.innerHeight = document.body.clientHeight; }
 return this; }
, init: function(element) { element.css("left", Math.round(viewport.v().innerWidth / 2) + viewport.v().pageXOffset - Math.round(element.width() / 2)); element.css("top", Math.round(viewport.v().innerHeight / 2) + viewport.v().pageYOffset - Math.round(element.height() / 2)); element.fadeIn(200); }
 }
; var _s = ""; _s += "<div class='ab-alert-div' style='display:none'>"; _s += "<div class='ab-alert-div-inner'>"; _s += message + " <a class='ab-alert-close'>[x]</a>"; _s += "</div></div>"; $("body").append(_s); var container = $(".ab-alert-div"); var inner = $(".ab-alert-div-inner"); var close = $(".ab-alert-close"); viewport.init(container); $.ab_alert_close = function() { container.remove(); }
;  close.click(function() { $.ab_alert_close() }
); }
