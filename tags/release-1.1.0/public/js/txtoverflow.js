/**
 * jQuery text overflow plugin
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
 *     display: none;
 *     position: absolute;
 *     width: auto;
 *     height: auto;
 *     white-space: nowrap;
 * }
 */

jQuery.fn.b_txtoverflow = function(cf)
{
    var bf = null;
    var bw = null;
    var ln = null;
    var tx = null;
    var ap = null;
    var ss = 0;

    if(cf!=undefined) { if(cf.buffer!=undefined) { bf = cf.buffer; } }
    if(bf==undefined) { bf = $("#b_txtoverflow-buffer"); }
    if(bf==undefined) { return false; }

    if(cf!=undefined) { if(cf.width!=undefined)  { ln = parseInt(cf.width); } }
    if(ln==null)      { ln = $(this).width(); }
    if(ln==0)         { ln = 500; }

    if(cf!=undefined) { if(cf.text!=undefined)   { tx = cf.text; } }

    bf.css('font-family',  $(this).css('font-family'));
    bf.css('font-size',    $(this).css('font-size'));
    bf.css('font-style',   $(this).css('font-style'));
    bf.css('font-variant', $(this).css('font-variant'));
    bf.css('font-weight',  $(this).css('font-weight'));

    if(tx!=null)
    {
        bf.text(tx);
    }
    else
    {
        $(this).find('span.hellip').remove();
        bf.text($(this).text());
    }

    bw = parseInt(bf.width()) + 1; // to avoid zero div

    if(ln < bw)
    {
        tx = bf.text();
        ss = parseInt(ln / (bw / tx.length)) - 1;
        $(this).html(tx.substring(0, ss) + '<span class="hellip">&hellip;</span><span style="display:none">' + tx.substring(ss) + '</span>');
    }
    else
    {
        if(tx!=null) { $(this).text(tx); }
    }
}
