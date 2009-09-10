/**
 * jQuery text overflow plugin
 *
 * author : Rafael Castilho <rafael@castilho.biz>
 *
 * usage:
 * 
 * $("#myoriginal").b_txtoverflow({ buffer : $("#b_txtoverflow-buffer"), 
 *                                  width  : 400,
 *                                  text   : "foo bar" });   
 *
 * note: all parameters are optional, except buffer object
 *
 * css:
 *
 * #b_txtoverflow-buffer
 * {
 *     position: absolute;
 *     width: auto;
 *     height: auto;
 *     white-space: nowrap;
 * }
 */

jQuery.fn.b_txtoverflow = function(cf)
{
    var bf = null;
    var ln = null;
    var tx = null;
    var ap = null;

    if(cf!=undefined) { if(cf.buffer!=undefined) { bf = cf.buffer; } }
    if(bf==undefined) { bf = $("#b_txtoverflow-buffer"); }
    if(bf==undefined) { return false; }

    if(cf!=undefined) { if(cf.width!=undefined)  { ln = parseInt(cf.width); } }
    if(ln==null)      { ln = $(this).width(); }
    if(ln==0)         { ln = 500; }

    if(cf!=undefined) { if(cf.text!=undefined)   { tx = cf.text; } }
    if(tx==null)      { tx = $(this).text(); }

    if(cf!=undefined) { ap = cf.append; }
    if(ap==null)      { ap = '...'; }

    bf.css('font-family',  $(this).css('font-family'));
    bf.css('font-size',    $(this).css('font-size'));
    bf.css('font-style',   $(this).css('font-style'));
    bf.css('font-variant', $(this).css('font-variant'));
    bf.css('font-weight',  $(this).css('font-weight'));
    bf.text(tx);

    $(this).text(tx.substring(0, parseInt(ln / (bf.width() / bf.text().length)) - ap.length) + ap);
}
