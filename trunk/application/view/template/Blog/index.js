var mytpl = null;


function blog_set(blog)
{
    mytpl.bloglistarea
        .find("div.blogitem[blog='" + blog + "']")
        .find("div.blogitemleft").addClass('blogitemleftbold');
}

function blog_unset(blog)
{
    mytpl.bloglistarea
        .find("div.blogitem[blog='" + blog + "']")
        .find("div.blogitemleft").removeClass('blogitemleftbold');
}

function blog_edit_show(blog)
{
    blog_set(blog);
    mytpl.bloglistarea
        .find("div.blogitemeditform[blog='" + blog +  "']").show();
}

function blog_edit_hide(blog)
{
    blog_unset(blog);
    mytpl.bloglistarea
        .find("div.blogitemeditform[blog='" + blog +  "']").hide();
}

function blog_update_callback(blog, updated)
{
    mytpl.bloglistarea
        .find("div.blogitem[blog='" + blog + "']")
        .find("div.blogitemleft")
        .find("span.blogitemname").text(updated.find('name').text());
    blog_edit_hide(blog);
}

function blog_update(blog)
{
    var _f = mytpl.bloglistarea.find("div.blogitemeditform[blog='" + blog +  "']");
    var _f_name = _f.find("input[name='blog_name']");
    var _f_username = _f.find("input[name='blog_username']");
    var _f_password = _f.find("input[name='blog_password']");
    var _f_keywords = _f.find("input[name='keywords']");

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('blog','update') ?>",
        dataType: "xml",
        data: { blog          : blog,
                name          : _f_name.val(), 
                blog_username : _f_username.val(), 
                blog_password : _f_password.val(),
                keywords      : _f_keywords.val() },
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
            var _u = null;
            if((_u = _d.find('updated')))
            {
                blog_update_callback(blog, _u);
            }
        },
        error: function () { server_error(); }
    });

    _f_password.val("");
}

function blog_remove_from_list(blog)
{
    mytpl.bloglistarea.find("div.blogitem[blog='" + blog + "']").remove();
}

function blog_delete(blog)
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('blog','delete') ?>",
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
            var _d = $(xml).find('data');
            blog_remove_from_list(_d.find('result').text());
        },
        error: function () { server_error(); }
    });
}


$(document).ready(function()
{
    mytpl =
    {
        bloglistarea : $("#bloglistarea")
    };

    /* triggers */

    mytpl.bloglistarea.find("a.blogeditlnk").click(function()
    {
        blog_edit_show($(this).attr('blog'));
    });

    mytpl.bloglistarea.find("a.blogdeletelnk").click(function()
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

    mytpl.bloglistarea.find("input[name='blogupdatebtn']").click(function()
    {
        blog_update($(this).attr('blog'));
    });

    mytpl.bloglistarea.find("input[name='blogcancelbtn']").click(function()
    {
        blog_edit_hide($(this).attr('blog'));
    });
});
