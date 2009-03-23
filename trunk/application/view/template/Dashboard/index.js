$(document).ready(function()
{
    /* DEFAULTS */
    
    var ar = false;

    /* dashboard containers */

    <?php if(count($this->blogs) == 0) : ?>

    $("#noblogmsg").dialog({ bgiframe: true, modal: true });

    <?php else : ?>

    ww = $(window).width();
    wh = $(window).height();

    uw = Math.floor(ww / 25) * 25;
    uh = Math.floor(wh / 25) * 25;

    $("#itemscontainer").width(uw - 325);
    $("#queuecontainer").width(uw - 325);
    $("#feedscontainer").height(uh - 100);
    $("#queuecontainer").height(uh - 350);

    function container(id, mw, mh, mz)
    {
        r = { grid: [25,25], minWidth: mw, minHeight: mh };
        d = { grid: [25,25], handle: 'h2', stack: { group: 'dbcontainers', min: mz } };
        $("#" + id).resizable(r).draggable(d);
    }

    container("feedscontainer", 200, 300, 1201);
    container("itemscontainer", 400, 150, 1202);
    container("queuecontainer", 400, 150, 1203);

    function front(id)
    {
        v = ['feedscontainer', 'itemscontainer', 'queuecontainer'];
        for(i=0;i<v.length;i++) { if(v[i]!=id) { $("#" + v[i]).css('z-index', (1201 + i)); } }
        $("#" + id).css('z-index', 1401);
    }

    $("#feedscontainer").click(function() { front($(this).attr('id')); });
    $("#itemscontainer").click(function() { front($(this).attr('id')); });
    $("#queuecontainer").click(function() { front($(this).attr('id')); });

    /* select default blog */

    <?php if(count($this->blogs) == 1) : ?>
    blog = $("#blogcur").val();
    <?php else : ?>
    blog = $("select[name='bloglst'] > option:selected").val();
    <?php endif ?>

    setblog(blog);

    <?php endif ?>

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

    /* load blog data */

    function loadqueue(blog)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('queue', 'list') ?>",
            dataType: "xml",
            data: { blog: blog },
            beforeSend: function()
            {
                sp(true);
            },
            complete: function()
            {
                loadfeeds(blog);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    function loadfeeds(blog)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'list') ?>",
            dataType: "xml",
            data: { blog: blog },
            beforeSend: function()
            {
                /* void */
            },
            complete: function()
            {
                /* load items from current feed */

                feed = ''; /* TODO */

                loaditems(blog, feed);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    function loaditems(blog, feed)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'item') ?>",
            dataType: "xml",
            data: { blog: blog, feed: feed},
            beforeSend: function()
            {
                /* void */
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    function setblog(blog)
    {
        loadqueue(blog);
    }

    /* TRIGGERS */

    $("select[name='bloglst']").change(function()
    {
        blog = $("select[name='bloglst'] > option:selected").val();
        setblog(blog);
    });
});
