$(document).ready(function()
{
    /* DEFAULTS */
    
    var ar = false;

    /* dashboard containers */

    <?php if(count($this->blogs) == 0) : ?>

    $("#noblogmsg").dialog({ bgiframe: true, modal: true });

    <?php else : ?>

    /* maximize content area */

    function maxcontent(o)
    {
        _c = $("#" + o.attr('id') + " > div.containercontentarea");
        _f = $("#" + o.attr('id') + " > div.containerfooter").height();

        if(_c.position())
        { 
            _c.height((o.height() - _c.position().top) - _f);
        }
    }

    /* maximize containers */

    function maxcontainers()
    {
        ww = $(window).width();
        wh = $(window).height();

        _c = $("#feedscontainer");
        _h = wh - _c.offset().top + _c.height() - _c.outerHeight();
        _c.height(_h);
        maxcontent(_c);

        _c = $("#itemscontainer");
        _w = ww - _c.offset().left + _c.width() - _c.outerWidth();
        _c.width(_w);
        _c.height(_h * 0.5);
        maxcontent(_c);

        _t = _c.offset().top + _c.position().top + _c.height();

        // alert("offset: " + _c.offset().top + "\n" + "position: " + _c.position().top + "\n" + "scroll: " + _c.scrollTop() + "\n" + "height: " + _c.height() + "\n" + "inner: " + _c.innerHeight() + "\n" + "outer: " + _c.outerHeight());

        _c = $("#queuecontainer");
        _c.width(_w);
        _c.css('top', _t - 8);
        _c.height((_h * 0.5) - 5);
        maxcontent(_c);
    }

    maxcontainers();

    /* set default blog */

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
