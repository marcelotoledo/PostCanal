var mytpl = null;

var feed_display     = "<?php echo $this->preference->feed_display ?>";
var article_display  = "<?php echo $this->preference->article_display ?>";
var articles_content = Array();
var current_article  = null;

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

function set_feed_display()
{
    mytpl.feed_dsp_all.hide();
    mytpl.feed_dsp_thr.hide();

    if(feed_display=='all')
    {
        mytpl.feed_dsp_all.show();
        article_list(mytpl.feed_list_area);
    }
    if(feed_display=='thr') /* threaded */
    {
        mytpl.feed_dsp_thr.show();
        // feed_list();
    }

    mytpl.feed_list_area.scrollTop(0);
}

function set_article_display()
{
    mytpl.article_dsp_lst.hide();
    mytpl.article_dsp_exp.hide();

    if(article_display=='lst') /* list */
    {
        mytpl.article_dsp_lst.show();
        mytpl.feed_navigation.show();
        // article_content_hide_all();
    }
    if(article_display=='exp') /* expanded */
    {
        mytpl.article_dsp_exp.show();
        mytpl.feed_navigation.hide();
        //article_content_show_all();
    }
}

function article_populate(articles, container, append) // TODO
{
    if(articles.length==0)
    {
        if(append==true)
        {
            container.find("div.articlemore").remove();
        }
        else
        {
            container.html("<br/><span><?php echo $this->translation()->no_articles ?></span>");
        }

        return null;
    }

    if(append==false)
    {
        container.html("");
    }
    else
    {
        if((m = container.find("div.articlemore"))) { m.remove(); }
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = "";

    articles.each(function()
    {
        _data =
        {
            feed    : "",
            article : $(this).find('article').text(),
            title   : $(this).find('title').text(),
            link    : $(this).find('link').text(),
            date    : $(this).find('date').text(),
            author  : $(this).find('author').text(),
            content : $(this).find('content').text()
        };

        _item = mytpl.article_blank.clone();
        _inner = _item.find('div.article');
        _inner.attr('article', _data.article);
        _inner.addClass('article' + feed_display);

        if(feed_display == 'all')
        {
            _data.feed = $(this).find('feed').text();
            _inner.find('div.articlefeed').show().text(_data.feed);
        }

        _inner.find('div.articletitle').text(_data.title);
        _inner.find('div.articleinfo').text("@" + _data.date);
        _inner.find('div.articlebuttons').find('a.viewlnk').attr('href', _data.link);
        _inner.find('div.articlecontent').attr('article', _data.article);

        _lsdata += _item.html() + "\n";

        articles_content[_data.article] =
        {
            title   : _data.title,
            author  : _data.author,
            content : _data.content
        };
    });

    container.append(_lsdata);
    container.append(mytpl.article_more_blank.clone().find('div.articlemore').attr('older', _data.date));

    /* article triggers must be created after populate, otherwise
     * will not work (because populate write elements after document loading */

    container.find("div.article[bound='no']").each(function()
    {
        $(this).attr('bound', 'yes');

        $(this).click(function()
        {
//            if(article_display == 'lst')
//            {
//                _a = $(this).attr('article');
//
//                if($(this).hasClass('articlecontentshow'))
//                {
//                    article_content_hide(_a);
//                }
//                else
//                {
//                    article_content_hide_all();
//                    article_content_show(_a);
//                }
//            }
        });
    });
//
//    container.find("div.articlemore").click(function()
//    {
//        article_more(container, $(this).attr('older'));
//    });
//
//    window_update();
//
//    if(article_display == 'exp')
//    {
//        article_content_show_all();
//    }
//
//    body__.trigger('article_next_evt');
//    body__.unbind('article_next_evt');
}

function __article_load(container, append, older)
{
    url = (feed_display == 'thr') ? 
                "<?php B_Helper::url('article', 'threaded') ?>" : 
                "<?php B_Helper::url('article', 'all') ?>";
    $.ajax
    ({
        type: "GET",
        url: url,
        dataType: "xml",
        data: { blog  : current_blog, 
                feed  : container.attr('feed'), 
                older : older },
        beforeSend: function()
        {
            set_active_request(true);
        },
        complete: function()
        {
            set_active_request(false);
        },
        success: function (xml)
        {
            var _d = $(xml).find('data');
            article_populate(_d.find('articles').children(), container, append);
        },
        error: function () { server_error(); }
    });
}

function article_list(container)
{
    __article_load(container, false, null);
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
        article_blank        : $("#articleblank"),
        article_more_blank   : $("#articlemoreblank"),
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

    set_feed_display();
    set_article_display();

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

    mytpl.feed_dsp_all_lnk.click(function()
    {
        feed_display = 'all';
        save_preference('feed_display', feed_display);
        return false;
    });

    mytpl.feed_dsp_thr_lnk.click(function()
    {
        feed_display = 'thr';
        save_preference('feed_display', feed_display);
        return false;
    });

    $(document).bind('feed_display_saved' , function(e)
    {
        set_feed_display();
    });

    mytpl.article_dsp_lst_lnk.click(function()
    {
        article_display = 'lst';
        save_preference('article_display', article_display);
        return false;
    });

    mytpl.article_dsp_exp_lnk.click(function()
    {
        article_display = 'exp';
        save_preference('article_display', article_display);
        return false;
    });

    $(document).bind('article_display_saved' , function(e)
    {
        set_article_display();
    });
});
