$(document).ready(function()
{
    /* DEFAULTS */
    
    var ar = false;

    <?php if(count($this->cms) > 0) : ?>

    /* dashboard containers */

    $("#dccms").b_dcontainer   (200, 500, 200, 300, [25,50]  );
    $("#dcqueue").b_dcontainer (700, 225, 200, 150, [250,325]);
    $("#dcfeedch").b_dcontainer(200, 250, 200, 150, [750,50] );
    $("#dcchitem").b_dcontainer(475, 250, 200, 150, [250,50] );

    /* select default cms */

    selectCMS($(".iicms").get(0).getAttribute("cid"));

    <?php endif ?>

    // $("#dcfeedch").b_dcontainer_title("<i>Outro blog Qualquer</i> Feeds");

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        if((ar = b) == true)
        {
            $.b_spinner_start
            ({
                height: 32, width: 32,
                image: "<?php B_Helper::img_src('spinner.gif') ?>",
                message: "... <?php echo $this->translation->application_loading ?>"
            });
        }
        else
        {
            $.b_spinner_stop();
        }
    }

    /* ACTIONS */

    /* error */

    function err()
    {
        alert("<?php echo $this->translation->server_error ?>");
    }

    /* select cms */

    function selectCMS(cid)
    {
        $(".iicms").each(function()
        {
            $(this).css("font-weight", "normal");
        });

        $("div[cid='" + cid + "']").css("font-weight", "bold");

        // _t = $("div[cid='" + cid + "'] > a").text();
        // $("#dcfeedch").b_dcontainer_title("<i>" + _t + "</i> Feeds");
    }

    /* TRIGGERS */

    $(".iicms").click(function()
    {
        selectCMS($(this).attr("cid"));
    });
});
