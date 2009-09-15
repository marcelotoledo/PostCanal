/**
 * jQuery modal plugin
 *
 * author : Rafael Castilho <rafael@castilho.biz>
 *
 * usage:
 * 
 * $("#myoriginal").b_modal(), 
 *
 * css:
 *
 * #b-modal
 * {
 *     position: absolute;
 *     top: 0;
 *     left: 0;
 *     width: 100%;
 *     height: 100%;
 *     background-color: black;
 * }
 */

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
