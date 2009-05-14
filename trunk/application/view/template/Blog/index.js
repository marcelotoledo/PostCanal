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

    function blog_set(blog)
    {
        $("div.blogitem[blog='" + blog + "'] > div.blogitemleft").addClass('blogitemleftbold');
    }

    function blog_unset(blog)
    {
        $("div.blogitem[blog='" + blog + "'] > div.blogitemleft").removeClass('blogitemleftbold');
    }

    function blog_edit_show(blog)
    {
        blog_set(blog);
        $("div.blogitemeditform[blog='" + blog +  "']").show();
    }

    function blog_edit_hide(blog)
    {
        blog_unset(blog);
        $("div.blogitemeditform[blog='" + blog +  "']").hide();
    }

    function blog_update_callback(result)
    {
        blog      = result.find('blog').text();
        blog_name = result.find('name').text();
        _i = $("div.blogitem[blog='" + blog + "']");
        _i.find("div.blogitemleft > span.blogitemname").text(blog_name);
        blog_edit_hide(blog);
    }

    function blog_update(blog)
    {
        _f = $("div.blogitemeditform[blog='" + blog +  "']");
        _f_name = _f.find("input[name='blog_name']").val();
        _f_username = _f.find("input[name='blog_username']").val();
        _f_password = _f.find("input[name='blog_password']").val();

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'update') ?>",
            dataType: "xml",
            data: { blog          : blog,
                    blog_name     : _f_name, 
                    blog_username : _f_username, 
                    blog_password : _f_password },
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
                if((r = d.find('result')))
                {
                    blog_update_callback(r);
                }
            },
            error: function () { err(); }
        });
    }

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
            type: "POST",
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

    $("a.blogeditlnk").click(function()
    {
        blog_edit_show($(this).attr('blog'));
    });

    $("a.blogdeletelnk").click(function()
    {
        blog = $(this).attr('blog');
        blog_set(blog);
        if(confirm("<?php echo $this->translation()->are_you_sure ?>"))
        {
            blog_delete(blog);
        }
        else
        {
            blog_unset(blog);
        }
    });

    $("input[name='blogupdatebtn']").click(function()
    {
        blog_update($(this).attr('blog'));
    });
});
