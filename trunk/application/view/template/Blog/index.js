$(document).ready(function()
{
    var active_request = false;

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    function set_active_request(b)
    {
        ((active_request = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* actions */

    function blog_remove_from_list(blog)
    {
        if(i = $("div.blogitem[blog='" + blog + "']"))
        {
            i.remove();
        }
    }

    function blog_delete(blog)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('blog', 'delete') ?>",
            dataType: "xml",
            data: { blog: blog },
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
                d = $(xml).find('data');
                blog_remove_from_list(d.find('result').text());
            },
            error: function () { err(); }
        });
    }

    /* events */

    $("a.blogdeletelnk").click(function()
    {
        if(confirm("<?php echo $this->translation()->are_you_sure ?>"))
        {
            blog_delete($(this).attr('blog'));
        }
    });
});
