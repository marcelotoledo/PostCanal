var mytpl = null;

var feed_display = 'all';
var article_display = 'lst';
var articles_content = Array();
var current_article = null;

var magic_q_min = 5;
var magic_q_exp = 16;
var magic_q_max = 140;

var queue_hctrl_display = 0 // 0 min | 1 exp | 2 max


function on_blog_change()
{
    /* void */
}

function feed_area_enable()
{
    mytpl.feed_area_head.removeClass('areadisabled');
    mytpl.feed_dsp.show();
    mytpl.article_dsp.show();
    if(article_display=='lst') // @see set_article_display()
    {
        mytpl.feed_navigation.show();
    }
    mytpl.feed_refresh.show();
}

function feed_area_disable()
{
    mytpl.feed_area_head.addClass('areadisabled');
    mytpl.feed_dsp.hide();
    mytpl.article_dsp.hide();
    mytpl.feed_navigation.hide();
    mytpl.feed_refresh.hide();
}

function queue_minimize()
{
    mytpl.queue_area.css('bottom', 0);
    mytpl.queue_list_area.hide();
    feed_area_enable();
    mytpl.feed_list_area.show();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_exp.show();
}

function queue_expand(h)
{
    mytpl.queue_area.css('bottom', h - mytpl.queue_area.outerHeight() - magic_q_exp);
    mytpl.queue_list_area.show();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_max.show();
}

function queue_maximize()
{
    mytpl.queue_area.css('bottom', $(window).height() - magic_q_max);
    mytpl.feed_list_area.hide();
    feed_area_disable();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_min.show();
}


$(document).ready(function()
{
    mytpl =
    {
        top_bar              : $("#topbar"),
        feed_area            : $("#feedarea"),
        feed_area_head       : $("#feedareahead"),
        feed_dsp             : $("#feeddisplay"),
        feed_dsp_all         : $("#feeddspall"),
        feed_dsp_all_lnk     : $("#feeddspalllnk"),
        feed_dsp_thr         : $("#feeddspthr"),
        feed_dsp_thr_lnk     : $("#feeddspthrlnk"),
        article_dsp          : $("#articledisplay"),
        article_dsp_lst      : $("#articledsplst"),
        article_dsp_lst_lnk  : $("#articledsplstlnk"),
        article_dsp_exp      : $("#articledspexp"),
        article_dsp_exp_lnk  : $("#articledspexplnk"),
        feed_navigation      : $("#feednavigation"),
        article_previous_lnk : $("#articlepreviouslnk"),
        article_next_lnk     : $("#articlepreviouslnk"),
        feed_refresh         : $("#feedrefresh"),
        feed_refresh_lnk     : $("#feedrefreshlnk"),
        feed_list_area       : $("#feedlistarea"),
        queue_area                        : $("#queuearea"),
        queue_area_head                   : $("#queueareahead"),
        queue_area_hctrlbar               : $("#queueareahctrlbar"),
        queue_publication_manual_lnk      : $("#queuepublicationmanuallnk"),
        queue_publication_manual_label    : $("#queuepublicationmanuallabel"),
        queue_publication_automatic_lnk   : $("#queuepublicationautomaticlnk"),
        queue_publication_automatic_label : $("#queuepublicationautomaticlabel"),
        queue_interval                    : $("#queueinterval"),
        queue_interval_sel                : $("#queueintervalsel"),
        queue_feeding_manual_lnk          : $("#queuefeedingmanuallnk"),
        queue_feeding_manual_label        : $("#queuefeedingmanuallabel"),
        queue_feeding_automatic_lnk       : $("#queuefeedingautomaticlnk"),
        queue_feeding_automatic_label     : $("#queuefeedingautomaticlabel"),
        queue_hctrl_lnks                  : $("#queuehctrllnks"),
        queue_hctrl_min                   : $("#queuehctrlmin"),
        queue_hctrl_exp                   : $("#queuehctrlexp"),
        queue_hctrl_max                   : $("#queuehctrlmax"),
        queue_list_area                   : $("#queuelistarea")
    };

    function window_update()
    {
        var _th = mytpl.top_bar.outerHeight() + mytpl.feed_area_head.outerHeight();
            _bh = 0;

        if(queue_hctrl_display == 0)
        {
            _bh = mytpl.queue_area_head.outerHeight() + magic_q_min;
            queue_minimize();
        }
        if(queue_hctrl_display == 1)
        {
            _bh = $(window).height() / 2;
            queue_expand(_bh);
        }
        if(queue_hctrl_display == 2)
        {
            _bh = $(window).height();
            queue_maximize();
        }

        mytpl.feed_list_area.css('top', _th);
        mytpl.feed_list_area.css('left', 0);
        mytpl.feed_list_area.width($(window).width());
        mytpl.feed_list_area.height($(window).height() - _th - _bh);

        mytpl.feed_list_area.find('div.articlelabel').width($(window).width() * 0.6);
    }

    /*<?php if(count($this->blogs)==0) : ?>**/

    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();

    /*<?php endif ?>**/

    /* triggers */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    $(window).resize(function()
    {
        window_update();
    });

    mytpl.queue_hctrl_lnks.find("a").click(function()
    {
        queue_hctrl_display = (queue_hctrl_display < 2) ? queue_hctrl_display + 1 : 0;
        window_update();
    });
});
