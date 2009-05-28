var mytpl = null;


function on_blog_change()
{
}


$(document).ready(function()
{
    mytpl =
    {
        feed_area            : $("#feedarea"),
        feed_area_head       : $("#feedareahead"),
        feed_dsp_all         : $("#feeddspall"),
        feed_dsp_all_lnk     : $("#feeddspalllnk"),
        feed_dsp_thr         : $("#feeddspthr"),
        feed_dsp_thr_lnk     : $("#feeddspthrlnk"),
        article_dsp_lst      : $("#articledsplst"),
        article_dsp_lst_lnk  : $("#articledsplstlnk"),
        article_dsp_exp      : $("#articledspexp"),
        article_dsp_exp_lnk  : $("#articledspexplnk"),
        feed_navigation      : $("#feednavigation"),
        article_previous_lnk : $("#articlepreviouslnk"),
        article_next_lnk     : $("#articlepreviouslnk"),
        feed_refresh_lnk     : $("#feedrefreshlnk"),
        feed_list_area       : $("#feedlistarea"),

        queue_area                        : $("#queuearea"),
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
    },

    /*<?php if(count($this->blogs)==0) : ?>**/

    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();

    /*<?php endif ?>**/

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });
});
