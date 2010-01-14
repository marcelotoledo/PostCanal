var my_template = null;

function add_message(m)
{
    my_template.new_blog_message.text(m);
    my_template.new_blog_message.show();
}

function toggle_blog_add()
{
    if(my_template.new_blog_form.toggle().css('display')=='block')
    {
        add_message('');
        my_template.new_blog_url.val('');
        my_template.new_blog_url.focus();

        if($("#noblogmsg0").is(':visible')) /* tutorial */
        {
            $("#noblogmsg0").hide(100);
            $("#noblogmsg1").show(100);
        }
    }

    my_template.new_blog_button.toggle();
    my_template.blog_type_failed.hide();
}

function txtoverflow_up()
{
    my_template.blog_list_area.find('div.blog').each(function()
    {
        $(this).find('div.blogtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.blog_list_area.width() * 0.8) });
    });
}

function blog_populate(b)
{
    var _blog = 
    {
        blog          : b.find('blog').text(),
        name          : b.find('name').text(),
        url           : b.find('url').text(),
        username      : b.find('username').text(),
        oauth_enabled : (b.find('oauth_enabled').text()=='true'),
        keywords      : b.find('keywords').text()
    };

    var _item = my_template.blog_item_blank.clone();
    _item.find('div.blog').attr('blog', _blog.blog);

    my_template.blog_list_area.prepend(_item.html());

    my_template.blog_list_ref[_blog.blog] = 
    {
        item : my_template.blog_list_area.find("div.blog[blog='" + _blog.blog + "']")
    };

    if(_blog.oauth_enabled)
    {
        my_template.blog_list_ref[_blog.blog].item.find('div.username-row').hide();
        my_template.blog_list_ref[_blog.blog].item.find('div.password-row').hide();

        if(_blog.username)
        {
            my_template.blog_list_ref[_blog.blog].item.find('p.oauth-authorize-row').hide();
            my_template.blog_list_ref[_blog.blog].item.find('p.oauth-reauthorize-row').show();
        }
        else
        {
            my_template.blog_list_ref[_blog.blog].item.find('p.oauth-authorize-row').show();
            my_template.blog_list_ref[_blog.blog].item.find('div.form-bot').hide();
        }
    }

    my_template.blog_list_ref[_blog.blog].item.find('div.blogtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.blog_list_area.width() * 0.8), text: _blog.name });
    my_template.blog_list_ref[_blog.blog].item.find('div.blogurl > span').text(_blog.url);
    my_template.blog_list_ref[_blog.blog].item.find("input[name='name']").val(_blog.name);
    my_template.blog_list_ref[_blog.blog].item.find("input[name='username']").val(_blog.username);
    // my_template.blog_list_ref[_blog.blog].item.find("input[name='keywords']").val(_blog.keywords);
}

function blog_add_callback(d)
{
    var _status = d.find('status').text();

    if(_status=="<?php echo C_Site::ADD_STATUS_OK ?>")
    {
        blog_populate(d.find('result'));
        toggle_blog_add();
        blog_edit_show(d.find('result').find('blog').text());

        var _b = d.find('blog').text();
        var _n = d.find('name').text();
        var _u = d.find('url').text();

        my_template.blog_list_ref[_b].item.find('span.whypwdquestion').show();
        my_template.blog_list_ref[_b].item.find('div.donotchangepwd').hide();
        my_template.blog_list_ref[_b].item.find('input[name="password"]').show();
        my_template.blog_list_ref[_b].item.find('input[name="username"]').focus();

        // add new blog to blogs select box
        my_template.blog_list_select.append('<option value="' + _b + '" selected>' + _n + '</option>');
        my_template.blog_list_select.change();

        if($("#noblogmsg1").is(':visible')) /* tutorial */
        {
            $("#noblogmsg1").hide(100);
            $("#noblogmsg2").show(100);
        }

        // wordpress install remote publishing tip

        if(d.find('type_name').text()=='wordpress' &&
           _u.indexOf('wordpress.com')==-1)
        {
            my_template.blog_list_ref[_b].item.find('div.wordpress-remote-publishing').show();
        }
           
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_OVERQUOTA ?>")
    {
        add_message("<?php echo $this->translation()->overquota ?>");
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->discover_failed ?>");
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_TIMEOUT ?>")
    {
        add_message("<?php echo $this->translation()->discover_timeout ?>");
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_URL_FAILED ?>")
    {
        add_message("<?php echo $this->translation()->url_failed ?>");
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_TYPE_FAILED ?>")
    {
        my_template.blog_type_failed.show();
    }
    if(_status=="<?php echo C_Site::ADD_STATUS_MAINTENANCE ?>")
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

    if(_u.indexOf('://')==-1) 
    { 
        _u = 'http://' + _u; 
        my_template.new_blog_url.val(_u);
    }

    do_request('POST', '/site/add', { url: _u }, blog_add_callback);
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
    do_request('GET', '/site/list', { }, blog_list_callback);
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
    // hide foreve all new site notice
    my_template.blog_list_ref[b].item.find('div.newsite-notice').hide();
    my_template.blog_list_ref[b].item.find('span.whypwdquestion').hide();
    my_template.blog_list_ref[b].item.find('div.blogbot').hide();
    my_template.blog_list_ref[b].item.find('button.blogdeletebtn').hide();
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').show();
}

function blog_remove_from_list(b)
{
    //my_template.blog_list_ref[b].item.next('div.blogdeletemsg').remove();
    my_template.blog_list_ref[b].item.remove();
    my_template.blog_list_ref[b] = null;

    // remove blog from blogs select box
    my_template.blog_list_select.find('option[value="' + b + '"]').remove();
    my_template.blog_list_select.change();

    flash_message("<?php echo $this->translation()->deleted ?>");
}

function blog_delete_confirm_show(b)
{
    //blog_edit_hide(b);
    my_template.blog_list_ref[b].item.find('button.blogeditbtn').hide();
    my_template.blog_list_ref[b].item.find('button.blogdeletebtn').hide();
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
    do_request('POST', '/site/delete', { blog: b }, blog_delete_callback);
}

function blog_update_callback(d)
{
    var _updated = null;

    if((_updated = d.find('updated'))!=undefined)
    {
        var _blog = _updated.find('blog').text();
        var _name = _updated.find('name').text();
        my_template.blog_list_ref[_blog].item.find('div.blogtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.blog_list_area.width() * 0.8), text: _name });
        my_template.blog_list_select.find('option[value="' + _blog + '"]').text(_name);
        flash_message("<?php echo $this->translation()->saved ?>");
        blog_edit_hide(_blog);

        if($("#noblogmsg2").is(':visible')) /* tutorial */
        {
            $("#noblogmsg2").hide(100);
            $("#noblogmsg3").show(100);
        }
    }
}

function blog_oauth_authorize(b)
{
    document.location='/site/authorize?blog=' + b;
}

function blog_update(b)
{
    var _up = 
    {
        blog          : b,
        name          : my_template.blog_list_ref[b].item.find("input[name='name']").val(),
        blog_username : my_template.blog_list_ref[b].item.find("input[name='username']").val(),
        blog_password : my_template.blog_list_ref[b].item.find("input[name='password']").val(),
        blog_password_is_visible : my_template.blog_list_ref[b].item.find("input[name='password']").is(':visible')
    }

    add_message(''); 

    if(_up.name=='' || _up.blog_username=='' ||
       (_up.blog_password=='' && _up.blog_password_is_visible))
    {
        var astr;
        astr  = "Please fill this form correctly with Name";
        astr += my_template.blog_list_ref[b].item.find('p.username-title-oauth').is(':visible') ? ' and OAuth token' : (_up.blog_password_is_visible ? ', Username and Password' : ' and Username');
        alert(astr);
        return null;
    }

    do_request('POST', '/site/update', _up, blog_update_callback);
}

function on_blog_change()
{
    // document.location='/rw'; TODO
}

$(document).ready(function()
{
    my_template =
    {
        new_blog_button    : $("#addnewblogbtn"),
        new_blog_form      : $("#addnewblogform"),
        new_blog_url       : $("#blogurl"),
        new_blog_submit    : $("#addsubmit"),
        new_blog_cancel    : $("#addcancel"),
        new_blog_message   : $("#addmessage"),
        blog_list_area     : $("#bloglistarea"),
        blog_item_blank    : $("#blogitemblank"),
        blog_delete_blank  : $("#blogdeleteblank"),
        blog_type_failed   : $("#blogtypefailedmsg"),
        txtoverflow_buffer : $("#b_txtoverflow-buffer"),
        blog_list_select   : $("#bloglstsel"),
        blog_list_ref      : Array()
    }; 
    
    function window_update()
    {
        txtoverflow_up();
    }

    $(window).resize(function()
    {
        window_update();
    });

    function no_blog()
    {
        $("#noblogmsg0").show(); /* tutorial */
    }

    function initialize()
    {
        <?php if(count($this->blogs)==0) : ?>
        no_blog();
        <?php endif ?>
        blog_list();
    }

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
        $(this).blur();
        return false;
    });

    my_template.new_blog_cancel.click(function()
    {
        my_template.new_blog_button.click();
        $("#noblogmsg1").hide();
        $(this).blur();
        return false;
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

    my_template.blog_list_area.find("span.whypwdquestion")
        .find('a')
        .live('click', function()
    {
        var b = $(this).parent().parent().parent().parent().parent().parent().attr('blog');
        my_template.blog_list_ref[b].item.find('div.password-notice').show();
        $(this).blur();
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

    my_template.blog_list_area.find("a.oauth-authorize-lnk").live('click', function()
    {
        blog_oauth_authorize(blog_update_getid($(this)));
        return false;
    });

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

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
