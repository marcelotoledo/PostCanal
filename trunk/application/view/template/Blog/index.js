var mytpl = null;

function toggle_blog_add()
{
    if(mytpl.new_blog_form.toggle().css('display')=='block')
    {
        mytpl.new_blog_url.val('');
        mytpl.new_blog_url.focus();
    }

    mytpl.blog_type_failed.hide();
}

function add_message(m)
{
    mytpl.new_blog_message.text(m);
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

    var _item = mytpl.blog_item_blank.clone();

    _item.find('div.blogitem').attr('blog', _blog.blog);
    _item.find('div.blogitemeditform').attr('blog', _blog.blog);

    mytpl.blog_list_area.prepend(_item.html());

    mytpl.blog_list_ref[_blog.blog] = 
    {
        item : mytpl.blog_list_area.find("div.blogitem[blog='" + _blog.blog + "']"),
        form : mytpl.blog_list_area.find("div.blogitemeditform[blog='" + _blog.blog + "']")
    };

    mytpl.blog_list_ref[_blog.blog].item.find('span.blogitemname').text(_blog.name);
    mytpl.blog_list_ref[_blog.blog].item.find('span.blogitemurl').text(_blog.url);
    mytpl.blog_list_ref[_blog.blog].form.find("input[name='name']").val(_blog.name);
    mytpl.blog_list_ref[_blog.blog].form.find("input[name='username']").val(_blog.username);
    mytpl.blog_list_ref[_blog.blog].form.find("input[name='keywords']").val(_blog.keywords);
}

function blog_add_callback(d)
{
    var _status = d.find('status').text();

    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_OK ?>")
    {
        blog_populate(d.find('result'));
        toggle_blog_add();
        blog_edit_show(d.find('result').find('blog').text());

        var _b = d.find('blog').text();

        mytpl.blog_list_ref[_b].form.find('div.donotchangepwd').hide();
        mytpl.blog_list_ref[_b].form.find('input[name="password"]').show();
    }
    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->discover_failed ?>");
    }
    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_TIMEOUT ?>")
    {
        add_message("<?php echo $this->translation()->discover_timeout ?>");
    }
    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_URL_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->url_failed ?>");
    }
    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_TYPE_FAILED ?>")
    {
        mytpl.blog_type_failed.show();
    }
    if(_status=="<?php echo C_Blog::DISCOVER_STATUS_MAINTENANCE ?>")
    {
        add_message("<?php echo $this->translation()->blog_type_maintenance ?>");
    }
}

function blog_add()
{
    add_message(''); 
    mytpl.blog_type_failed.hide();

    var _u = null;

    if((_u = mytpl.new_blog_url.val())=="")
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
    mytpl.blog_list_ref[b].item.find('a.blogeditlnk').hide();
    mytpl.blog_list_ref[b].item.find('a.blogdeletelnk').show();
    mytpl.blog_list_ref[b].form.show();
}

function blog_edit_hide(b)
{
    mytpl.blog_list_ref[b].form.find('div.donotchangepwd').show();
    mytpl.blog_list_ref[b].form.find('input[name="password"]').hide();
    mytpl.blog_list_ref[b].form.hide();
    mytpl.blog_list_ref[b].item.find('a.blogdeletelnk').hide();
    mytpl.blog_list_ref[b].item.find('a.blogeditlnk').show();
}

function blog_remove_from_list(b)
{
    mytpl.blog_list_ref[b].item.next('div.blogdeletemsg').remove();
    mytpl.blog_list_ref[b].item.remove();
    mytpl.blog_list_ref[b].form.remove();
    mytpl.blog_list_ref[b] = null;
    flash_message("<?php echo $this->translation()->deleted ?>");
}

function blog_delete_confirm_show(b)
{
    blog_edit_hide(b);
    mytpl.blog_list_ref[b].item.find('a.blogeditlnk').hide();
    mytpl.blog_list_ref[b].item.after(mytpl.blog_delete_blank.clone().html());
}

function blog_delete_confirm_hide(b)
{
    mytpl.blog_list_ref[b].item.next('div.blogdeletemsg').remove();
    mytpl.blog_list_ref[b].item.find('a.blogeditlnk').show();
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
        mytpl.blog_list_ref[_blog].item.find('span.blogitemname').text(_name);
        flash_message("<?php echo $this->translation()->saved ?>");
        blog_edit_hide(_blog);
    }
}

function blog_update(b)
{
    var _up = 
    {
        blog          : b,
        name          : mytpl.blog_list_ref[b].form.find("input[name='name']").val(),
        blog_username : mytpl.blog_list_ref[b].form.find("input[name='username']").val(),
        blog_password : mytpl.blog_list_ref[b].form.find("input[name='password']").val(),
        keywords      : mytpl.blog_list_ref[b].form.find("input[name='keywords']").val()
    }

    do_request('POST', './blog/update', _up, blog_update_callback);
}

$(document).ready(function()
{
    mytpl =
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
    
    mytpl.new_blog_button.click(function()
    {
        if(active_request==false) { toggle_blog_add(); }
    });

    mytpl.new_blog_url.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            if(active_request==false) { blog_add(); }
        }
    });

    mytpl.new_blog_submit.click(function()
    {
        if(active_request==false) { blog_add(); }
    });

    function blog_item_getid(i)
    {
        return i.parent().parent().attr('blog')
    }

    mytpl.blog_list_area.find('a.blogeditlnk').live('click', function()
    {
        blog_edit_show(blog_item_getid($(this)));
        return false;
    });

    mytpl.blog_list_area.find('a.blogdeletelnk').live('click', function()
    {
        blog_delete_confirm_show(blog_item_getid($(this)));
        return false;
    });

    mytpl.blog_list_area.find("div.donotchangepwd")
        .find('input')
        .live('change', function()
    {
        $(this).attr('checked', true);
        $(this).parent().toggle();
        mytpl.blog_list_area.find("input[name='password']").toggle().focus();
    });

    function blog_update_getid(i)
    {
        return i.parent().parent().parent().attr('blog')
    }

    mytpl.blog_list_area.find("input[name='blogupdatebtn']").live('click', function()
    {
        blog_update(blog_update_getid($(this)));
        return false;
    });

    mytpl.blog_list_area.find("input[name='blogcancelbtn']").live('click', function()
    {
        blog_edit_hide(blog_update_getid($(this)));
        return false;
    });

    function blog_delete_getid(i)
    {
        return i.parent().parent().parent().prev('div.blogitem').attr('blog');
    }

    mytpl.blog_list_area.find("input[name='blogdeletebtn']").live('click', function()
    {
        blog_delete(blog_delete_getid($(this)));
        return false;
    });

    mytpl.blog_list_area.find("input[name='blognodelbtn']").live('click', function()
    {
        blog_delete_confirm_hide(blog_delete_getid($(this)));
        return false;
    });

    blog_list();
});
