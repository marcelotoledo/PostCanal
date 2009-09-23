var my_template = null;

function report_populate(i)
{
    var d = 
    {
        id   : parseInt(i.find('id').text()),
        name : i.find('name').text()
    };

    my_template.report_list.append('<li report="' + d.id + '"><div class="title">' + d.name + '</div><div class="control">[<a href="#view">view</a>][<a href="#edit">edit</a>][<a href="#delete">delete</a>]</div><div style="clear:left"></div></li>' + "\n");
}

function report_list_callback(d)
{
    d.find('list').children().each(function()
    {
        report_populate($(this));
    });
}

function report_list()
{
    do_request('GET', './report/list', {} , report_list_callback);
}

function report_edit_callback(d)
{
    if(d.length==0) { server_error(); return null; }

    var i = d.find('report');

    if(i.length>0)
    {
        my_template.report_id.val(parseInt(i.find('id').text()));
        my_template.report_name.val(i.find('name').text());
        my_template.report_query.val(i.find('query').text());
        $(window).scrollTop(9999);
        my_template.report_name.focus();
    }
}

function report_edit(i)
{
    do_request('GET', './report/edit', { id : i }, report_edit_callback);
}

function report_update_callback(d)
{
    if(d.length==0) { server_error(); return null; }

    if(d.find('updated').text()=='true')
    {
        my_template.report_list.html('');
        report_list();
        report_submit_complete();
        return true;
    }
    else
    {
        alert('report update failed');
        return false;
    }
}

function report_update()
{
    var d = { id    : my_template.report_id.val(),
              name  : my_template.report_name.val(),
              query : my_template.report_query.val() };

    if(d.name == '' || d.query == '')
    {
        alert('please fill up the form correctly');
        my_template.report_name.focus();
        return false;
    }

    $(window).scrollTop(0);
    do_request('POST', './report/edit', d, report_update_callback);
}

function report_submit_complete()
{
    my_template.report_id.val('');
    my_template.report_name.val('');
    my_template.report_query.val('');
}

function report_add_callback(d)
{
    if(d.length==0) { server_error(); return null; }

    if(d.find('added').text()=='true')
    {
        my_template.report_list.html('');
        report_list();
        report_submit_complete();
        return true;
    }
    else
    {
        alert('report add failed');
        return false;
    }
}

function report_add()
{
    var d = { name  : my_template.report_name.val(),
              query : my_template.report_query.val() };

    if(d.name == '' || d.query == '')
    {
        alert('please fill up the form correctly');
        my_template.report_name.focus();
        return false;
    }

    $(window).scrollTop(0);
    do_request('POST', './report/add', d, report_add_callback);
}

function report_delete_callback(d)
{
    if(d.length==0) { server_error(); return null; }

    if(d.find('deleted').text()=='true')
    {
        my_template.report_list.html('');
        report_list();
        return true;
    }
    else
    {
        alert('report delete failed');
        return false;
    }
}

function report_delete(i)
{
    do_request('POST', './report/delete', { id : i }, report_delete_callback);
}



$(document).ready(function()
{
    /* template vars */

    my_template = 
    {
        report_list   : $("#report-list"),
        report_form   : $("#report-form"),
        report_id     : $("#report-id"),
        report_name   : $("#report-name"),
        report_query  : $("#report-query"),
        submit_form   : $("#submit-form"),
        submit_cancel : $("#submit-cancel")
    };

    disable_submit();
    spinner_init({ x: 0, y: 0 });
    report_list();

    my_template.submit_form.click(function()
    {
        (parseInt(my_template.report_id.val())>0) ? report_update() :
                                                    report_add() ;
        $(this).blur();
        return null;
    });

    my_template.submit_cancel.click(function()
    {
        report_submit_complete();
        $(window).scrollTop(0);
        $(this).blur();
        return null;
    });

    my_template.report_list.find('li > a[href="#view"]').live('click', function()
    {
        document.location='./report/view?id=' + $(this).parent().attr('report');
        $(this).blur();
        return false;
    });

    my_template.report_list.find('li > a[href="#edit"]').live('click', function()
    {
        report_edit($(this).parent().attr('report'));
        $(this).blur();
        return false;
    });

    my_template.report_list.find('li > a[href="#delete"]').live('click', function()
    {
        report_delete($(this).parent().attr('report'));
        $(this).blur();
        return false;
    });
});
