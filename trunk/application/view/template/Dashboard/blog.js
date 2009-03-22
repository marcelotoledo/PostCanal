$(document).ready(function()
{
    /* password recovery */

    function err()
    {
        alert("erro ao adicionar canal!");
    }

    function feedAdd()
    {
        if($("input[name='chaddurl']").val() == "")
        {
            $.ab_alert("<?php echo $this->translation->feed_add_url_null ?>");
            return null;
        }

        parameters = { url: $("input[name='chaddurl']").val(), 
                       cid: $("input[name='chaddcid']").val() }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function()
            {
                active_request = true;
                showSpinner();
            },
            complete: function()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (xml) 
            { 
                /*
                var data = $(xml).find('data');
                var message = data.find('message').text();
                if(message != "") $.ab_alert(message);
                */
            }, 
            error: function () { err(); } 
        });
    };

    /* TRIGGERS */

    /* feed add popup */

    $("#chaddlnk").click(function()
    {
        $.ab_alert($("#chaddpopup").html());
    });

    /* feed add submit */

    $(".chaddsubmit").click(function()
    {
        feedAdd();
    });
});
