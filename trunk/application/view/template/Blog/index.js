var my_template = null;

function toggle_blog_add()
{
    if(my_template.new_blog_form.toggle().css('display')=='block')
    {
        my_template.new_blog_url.val('');
        my_template.new_blog_url.focus();
    }

    my_template.blog_type_failed.hide();
}

function add_message(m)
{
    my_template.new_blog_message.text(m);
    my_template.new_blog_message.show();
}

function blog_populate(b)
{
    var _blog = 
    {
        blog     : b.find('blog').text(),
        name     : b.find('name').text(),
        url      : b.find('url').text(),
        username : b.find('username').text(),
        keywords : b.find('keywords').text()
    };

    var _item = my_template.blog_item_blank.clone();
    _item.find('div.blog').attr('blog', _blog.blog);

    my_template.blog_list_area.prepend(_item.html());

    my_template.blog_list_ref[_blog.blog] = 
    {
        item : my_template.blog_list_area.find("div.blog[blog='" + _blog.blog + "']"),
    };

    my_template.blog_list_ref[_blog.blog].item.find('div.blogtit').text(_blog.name);
    my_template.blog_list_ref[_blog.blog].item.find('div.blogurl > span').text(_blog.url);
    my_template.blog_list_ref[_blog.blog].item.find("input[name='name']").val(_blog.name);
    my_template.blog_list_ref[_blog.blog].item.find("input[name='username']").val(_blog.username);
    // my_template.blog_list_ref[_blog.blog].item.find("input[name='keywords']").val(_blog.keywords);
}

function blog_add_callback(d)
{
    var _status = d.find('status').text();

    if(_status=="<?php echo C_Blog::ADD_STATUS_OK ?>")
    {
        blog_populate(d.find('result'));
        toggle_blog_add();
        blog_edit_show(d.find('result').find('blog').text());

        var _b = d.find('blog').text();

        my_template.blog_list_ref[_b].item.find('div.donotchangepwd').hide();
        my_template.blog_list_ref[_b].item.find('input[name="password"]').show();
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_OVERQUOTA ?>")
    {
        add_message("<?php echo $this->translation()->overquota ?>");
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->discover_failed ?>");
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_TIMEOUT ?>")
    {
        add_message("<?php echo $this->translation()->discover_timeout ?>");
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_URL_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->url_failed ?>");
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_TYPE_FAILED ?>")
    {
        my_template.blog_type_failed.show();
    }
    if(_status=="<?php echo C_Blog::ADD_STATUS_MAINTENANCE ?>")
    {
        add_message("<?php echo $this->translation()->blog_type_maintenance ?>");
    }
}

function blog_add()
{
    add_message(''); 
    my_template.blog_type_failed.hide();

    var _u = null;

    if((_u = my_template.new_blog_url.val())=="")
    {
        add_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    do_request('POST', './blog/add', { url: _u }, blog_add_callback);
}

function blog_list_callback(d)
{
    d.find('blogs').children().each(function()
    {
        blog_populate($(this));
    })
}

function blog_list()
{
    do_request('GET', './blog/list', { }, blog_list_callback);
}

function blog_edit_show(b)
{
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').hide();
    my_template.blog_list_ref[b].item.find('button.blogdeletebtn').show();
    my_template.blog_list_ref[b].item.find('div.blogbot').show();
}

function blog_edit_hide(b)
{
    my_template.blog_list_ref[b].item.find('div.donotchangepwd').show();
    my_template.blog_list_ref[b].item.find('input[name="password"]').hide();
    my_template.blog_list_ref[b].item.find('div.blogbot').hide();
    my_template.blog_list_ref[b].item.find('button.blogdeletebtn').hide();
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').show();
}

function blog_remove_from_list(b)
{
    //my_template.blog_list_ref[b].item.next('div.blogdeletemsg').remove();
    my_template.blog_list_ref[b].item.remove();
    my_template.blog_list_ref[b] = null;
    flash_message("<?php echo $this->translation()->deleted ?>");
}

function blog_delete_confirm_show(b)
{
    //blog_edit_hide(b);
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').hide();
    var _form = my_template.blog_list_ref[b].item.find('div.blogbot > form');
    _form.hide();
    _form.after(my_template.blog_delete_blank.clone().html());
}

function blog_delete_confirm_hide(b)
{
    var _form = my_template.blog_list_ref[b].item.find('div.blogbot > form');
    _form.next('div.blogdeletemsg').remove();
    blog_edit_hide(b);
    _form.show();
    // my_template.blog_list_ref[b].item.find('div.blogdeletemsg').remove();
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').show();
}

function blog_delete_callback(d)
{
    blog_remove_from_list(d.find('result').text());
}

function blog_delete(b)
{
    do_request('POST', './blog/delete', { blog: b }, blog_delete_callback);
}

function blog_update_callback(d)
{
    var _updated = null;

    if((_updated = d.find('updated'))!=undefined)
    {
        var _blog = _updated.find('blog').text();
        var _name = _updated.find('name').text();
        my_template.blog_list_ref[_blog].item.find('div.blogtit').text(_name);
        flash_message("<?php echo $this->translation()->saved ?>");
        blog_edit_hide(_blog);
    }
}

function blog_update(b)
{
    var _up = 
    {
        blog          : b,
        name          : my_template.blog_list_ref[b].item.find("input[name='name']").val(),
        blog_username : my_template.blog_list_ref[b].item.find("input[name='username']").val(),
        blog_password : my_template.blog_list_ref[b].item.find("input[name='password']").val()
        // keywords      : my_template.blog_list_ref[b].item.find("input[name='keywords']").val()
    }

    do_request('POST', './blog/update', _up, blog_update_callback);
}

$(document).ready(function()
{
    my_template =
    {
        new_blog_button   : $("#addnewblogbtn"),
        new_blog_form     : $("#addnewblogform"),
        new_blog_url      : $("#blogurl"),
        new_blog_submit   : $("#addsubmit"),
        new_blog_message  : $("#addmessage"),
        blog_list_area    : $("#bloglistarea"),
        blog_item_blank   : $("#blogitemblank"),
        blog_delete_blank : $("#blogdeleteblank"),
        blog_type_failed  : $("#blogtypefailedmsg"),
        blog_list_ref     : Array()
    }; 
    
    my_template.new_blog_button.click(function()
    {
        if(active_request==false) { toggle_blog_add(); }
        $(this).blur();
        return false;
    });

    my_template.new_blog_url.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            if(active_request==false) { blog_add(); }
        }
    });

    my_template.new_blog_submit.click(function()
    {
        if(active_request==false) { blog_add(); }
    });

    function blog_item_getid(i)
    {
        return i.parent().parent().attr('blog');
    }

    my_template.blog_list_area.find('button.blogeditbtn').live('click', function()
    {
        blog_edit_show(blog_item_getid($(this)));
        return false;
    });

    my_template.blog_list_area.find('button.blogdeletebtn').live('click', function()
    {
        blog_delete_confirm_show(blog_item_getid($(this)));
        return false;
    });

    my_template.blog_list_area.find("div.donotchangepwd")
        .find('input')
        .live('change', function()
    {
        $(this).attr('checked', true);
        $(this).parent().toggle();
        my_template.blog_list_area.find("input[name='password']").toggle().focus();
    });

    function blog_update_getid(i)
    {
        return i.parent().parent().parent().parent().attr('blog')
    }

    my_template.blog_list_area.find("button.blogupdatebtn").live('click', function()
    {
        blog_update(blog_update_getid($(this)));
        return false;
    });

    my_template.blog_list_area.find("button.blogcancelbtn").live('click', function()
    {
        blog_edit_hide(blog_update_getid($(this)));
        return false;
    });

    function blog_delete_getid(i)
    {
        return i.parent().parent().parent().parent().parent().attr('blog');
        //return i.parent().parent().parent().prev('div.blogitem').attr('blog');
    }

    my_template.blog_list_area.find("button[name='blogdeletebtn']").live('click', function()
    {
        blog_delete(blog_delete_getid($(this)));
        return false;
    });

    my_template.blog_list_area.find("button[name='blognodelbtn']").live('click', function()
    {
        blog_delete_confirm_hide(blog_delete_getid($(this)));
        return false;
    });

    blog_list();
});
