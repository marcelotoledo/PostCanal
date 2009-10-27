/**
 * jQuery literalTime plugin
 *
 * usage:
 * 
 * $("#mytimecontainer").b_modal(), 
 *
 * html:
 *
 * <div id="mytimecontainer" time="3456"></div>
 */

jQuery.fn.pc_literalTime = function(config)
{
    /* parse time in seconds */
    var _t = parseInt($(this).attr('time')); if(isNaN(_t)) { return false; }
    var _T = Math.abs(_t);

    /* prepend and append dictionary */
    var _m = { p : { b : null, a : 'ago' },
               f : { b : 'in', a : null } };
    if(typeof config == 'object' && typeof config.m == 'object') { _m = config.m; }

    /* literal time dictionary */
    var _n = { y :   'year', Y :   'years' ,
               m :  'month', M :  'months' ,
               d :    'day', D :    'days' , 
               h :   'hour', H :   'hours' ,
               i :    'min', I :     'min' ,
               s :    'sec', S :     'sec' };
    if(typeof config == 'object' && typeof config.n == 'object') { _n = config.n; }

    /* literal time glue dictionary */
    var _g = ' and ';
    if(typeof config == 'object' && config.g.length > 0) { _g = config.g; }

    /* literal time table conversion */
    var _c = [ { t: 31104000, s : _n.y, p: _n.Y },
               { t:  2592000, s : _n.m, p: _n.M },
               { t:    86400, s : _n.d, p: _n.D },
               { t:     3600, s : _n.h, p: _n.H },
               { t:       60, s : _n.i, p: _n.I } ];

    /* literal word counters */
    var _w = 1;
    var _y = 0;

    /* prepend */
    var _s = (_t > 0) ? ((_m.f.b==null) ? '' : _m.f.b + ' ') : 
                        ((_m.p.b==null) ? '' : _m.p.b + ' ') ;

    /* calculate literal time */
    jQuery.each(_c, function()
    {
        if(_T > this.t && _w > 0)
        {
            var _d = Math.floor(_T / this.t);
            _T -= (_d * this.t);
            _s = _s + ((_y > 0) ? _g : ' ') + _d + ' ' + (_d > 1 ? this.p : this.s);
            _w--;
            _y++;
        }
    });

    /* calculate residual seconds */
    if(_w > 0) { _s = _s + ((_y > 0) ? _g : ' ') + _T + ' ' + (_T > 1 ? _n.S : _n.s); }

    /* append */
    _s = _s + ' ' + ((_t > 0) ? ((_m.f.a==null) ? '' : _m.f.a + ' ') : 
                                ((_m.p.a==null) ? '' : _m.p.a + ' '));

    $(this).text(_s);
}
