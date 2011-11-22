$(function()
{
    var url = window.location.pathname;
    var urlSplit = url.split('/');
    
    // Code pour voter sur un topic/message
    $('.increment').removeAttr('href');
    $('.decrement').removeAttr('href');
    $('.increment').click(function()
    {
        rate('incrementvote', $(this).parent());
    });

    $('.decrement').click(function()
    {
        rate('decrementvote', $(this).parent());
    });

    

    // Code pour la mise en favoris des tags
    $('a[class^="fav-"]').removeAttr('href');
    $('a[class^="fav-"]').click(function()
    {
        var attr = $(this).attr('class');
        var tagId = attr.split("-");
        var url = "/forum/tag/favorite/tag/"+tagId[1];

        if(auth)
        {
            $.post(url, {}, function(response)
            {
                if(checkSuccess(response))
                {
                    var responseSplit = response.split("/");
                    var html = '<li class="favorited-style"><a href="/forum/topic/tag/name/'+responseSplit[0]+'" class="favorited-'+tagId[1]+'">'+responseSplit[0]+'</a>\n\
                                <a class="close2">x</a></li>';

                    if(responseSplit[1] == "add")
                    {
                        $('#favlist').append(html);
                        $('.close2').removeAttr('href');
                        deleteTagWithCross();
                        $('.fav-'+tagId[1]).each(function()
                        {
                            $(this).html('<a href="#" class="fav-'+tagId[1]+'" title="Retirer des favoris">-</a>');
                        });
                    }
                    else if(responseSplit[1] == "remove")
                    {
                        $('.favorited-'+tagId[1]).parent().remove();
                        $('.fav-'+tagId[1]).each(function()
                        {
                            $(this).html('<a href="#" class="fav-'+tagId[1]+'" title="Ajouter aux favoris">+</a>');
                        });
                    }
                }
            });
        }
        else
            alert("Vous devez vous identifier");
    });

    // Code lorsqu'on clique sur la croix d'un tag favoris
    $('.close2').removeAttr('href');
    deleteTagWithCross();

    function deleteTagWithCross()
    {
        $('.close2').click(function()
        {
            var attr = $(this).parent().find('a').first().attr('class').split('-');
            url = "/forum/tag/removefavorited/tag/"+attr[1];
            $.post(url, {}, function()
            {
                $('.favorited-'+attr[1]).parent().remove();
                $('.fav-'+attr[1]).each(function()
                {
                    $(this).html('<a href="#" class="fav-'+attr[1]+'" title="Ajouter aux favoris">+</a>');
                });
            });
        });
    }

    // Code pour l'autocomplete des tags lors d'un ajout de topic
    $('#tagsValues').hide();
    var tags = $('#tagsValues').val();
    
    $("#tags").tagit({
            availableTags: "/forum/tag/autocomplete", populateTags: tags
    });
    
    // Code pour le formulaire des commentaires
    $('a[class^=comment-link]').removeAttr('href');
    var submitted = false;
    $('a[class^=comment-link]').click(function()
    {
        submitted = false;
        var attr = $(this).attr('class');
        var messageId = attr.substring(attr.lastIndexOf('-') + 1);
        $('#comment-form-'+messageId).show();
        $(this).hide();
    });
    
    // Lorsque l'utilisateur envoie le commentaire
    $('form[id^=form_comment]').submit(function()
    {
        console.log("szs");
        if(!submitted)
        {
            submitted = true;
            var id = $(this).attr('id');
            var messageId = id.substring(id.lastIndexOf('_') + 1);
            var content = $(this).find('textarea').val();
            addComment(messageId, content);
        }
        return false;
    });
    
    // Lien répondre à un topic
    $('#answer-topic > a').removeAttr('href');
    CKEDITOR.replace('form_message_content',{
		toolbar : [['Bold','Italic','Underline', 'FontSize', '-', 'Image', '-', 'Undo','Redo','-','NumberedList', 'BulletedList','-','Link','Unlink']],
                //filebrowserBrowseUrl: '/simogeo-Filemanager-8b138bc/index.html',
                language : 'fr',
                scayt_autoStartup : true,
                scayt_sLang : 'fr_FR',
                scayt_contextCommands : 'off',
                contentsCss : '/css/assets/output_xhtml.css',
                coreStyles_bold	: { element : 'span', attributes : {'class': 'Bold'} },
                coreStyles_italic	: { element : 'span', attributes : {'class': 'Italic'}},
                coreStyles_underline	: { element : 'span', attributes : {'class': 'Underline'}},
                fontSize_sizes : 'Smaller/FontSmaller;Larger/FontLarger;8pt/FontSmall;14pt/FontBig;Double Size/FontDouble'
    });
    $('#answer-topic > a').click(function()
    {
        $('#block-quick-answer').show();
        $('a[name=answer]').offset().top;
    });
    // Lorsque l'utilisateur envoie le message
    $('form[id=form_message]').submit(function()
    {
        var topicId = url.substring(url.lastIndexOf('/') + 1);
        var content = $(this).find('textarea').val();
        addMessage(topicId, content);
        return false;
    });
    
    
    dialogCloseTopic();
    dialogCloseMotif();    
    dialogReopenTopic();

});

function checkSuccess(response)
{
    try
    {
        if(typeof(response) != 'object')
        {
            var obj = jQuery.parseJSON(response);
            if(obj.error_message != null)
            {
                alert(obj.error_message);
                return false;
            }
        }
        else
        {
            if(response.error_message != null)
            {
                alert(response.error_message);
                return false;
            }
        }
        return true;
    }
    catch(e)
    {
        return true;
    }
}


// Fonction de vote
function rate(action, parent)
{
    var url;
    var val = parent.attr('class');
    if(val == 'vote-t')
    {
        url = "/forum/topic/"+action+"/topic/"
    }
    else if(val == 'vote-m')
    {
        url = "/forum/message/"+action+"/message/"
    }

    var element = parent.find('input').attr('value');
    url += element;

    if(auth)
    {
        $.post(url, {}, function(response)
        {
            try
            {
                if(checkSuccess(response))
                    parent.find('span').first().text(response);
            }
            catch(e)
            {

            }

        });
    }
    else
        alert("Vous devez vous identifier");
}


// Fonction pour la dialog modal pour fermer un topic
function dialogCloseTopic()
{
$('#dialog-form').dialog({
                    autoOpen: false,
                    height: 300,
                    width: 350,
                    modal: true,
                    buttons: {
                            Valider: function() {
                                    var bValid = true;
                                    $('input[name=close_motif]').addClass("required");
                                    if(typeof $('input[name=close_motif]:checked').val() == "undefined")
                                        bValid = false;

                                    if(bValid)
                                    {
                                        var data = $('#form_close_topic').serializeArray();
                                        $.ajax({
                                            type: "POST",
                                            url: "/forum/topic/close",
                                            data: data,
                                            success: function(response)
                                            {
                                                if(checkSuccess(response))
                                                {
                                                    if(response == '1')
                                                        $('.close-motif').show();//$('#topic-menu').append('<a href="#" >('+response+')</a>');

                                                        $('.close-motif').text('('+response+')');

                                                    $('#dialog-motif > ol').append("<li>" + $('input[name=close_motif]').val() + " par <strong>" + $('input[name=username]').val() + "</strong></li>");

                                                    if(response == '7')
                                                    {
                                                        $('#answer-topic').hide();
                                                        $('#topic-header > h1').prepend('<span class="topic-status">[fermé]</span>');
                                                        $('.close-modal-form').hide();
                                                        $('.reopen-modal-confirm').show();
                                                        $('.close-motif').hide();
                                                    }
                                                }
                                            },
                                            error: function(a, b, c)
                                            {
                                                alert(b);
                                            }
                                        });
                                        $( this ).dialog('close');
                                    }
                            },
                            Annuler: function() {
                                    $( this ).dialog('close');
                            }
                    },
                    close: function() {
                            //allFields.val( "" ).removeClass( "ui-state-error" );
                    }
        });
                
    $('.close-modal-form').removeAttr('href');
                
    $('.close-modal-form').click(function(e) {
        //Cancel the link behavior
        e.preventDefault();
        $('#dialog-form').dialog('open');
    });
}

function dialogCloseMotif()
{
    $('.close-motif').removeAttr('href');
    $('#dialog-motif').dialog({
                    autoOpen: false,
                    height: 205,
                    modal: true
            });
                
    $('.close-motif').click(function(e) {
        //Cancel the link behavior
        e.preventDefault();
        $('#dialog-motif').dialog('open');
    });
}

function dialogReopenTopic()
{
    $( "#dialog-reopen-form" ).dialog({
                autoOpen: false,
                resizable: false,
                height:180,
                width: 350,
                modal: true,
                buttons: {
                        Valider: function() {
                            var topic_id = $('input[name=topic_id]').val();
                            $.ajax({
                                type: "POST",
                                url: "/forum/topic/reopen",
                                data: { "topic_id": topic_id },
                                success: function(response)
                                {
                                    if(checkSuccess(response))
                                    {
                                        var count = parseInt(response);
                                        var diff = 6;
                                        if(count != 7)
                                            diff = 6 - count;

                                        $('#dialog-reopen-form > strong').text(diff);

                                        if(count == 1)
                                            $('.nb-reopen-votes').show();//$('#topic-menu').append('<span class="nb-reopen-votes">('+response+')</span>');

                                            $('.nb-reopen-votes').text('('+response+')');

                                        if(count == 7)
                                        {
                                            $('#answer-topic').show();
                                            $('.topic-status').remove();
                                            $('.reopen-modal-confirm').hide();
                                            $('.close-modal-form').show();
                                            $('.nb-reopen-votes').hide();
                                        }
                                    }
                                },
                                error: function(a, b, c)
                                {
                                    alert(b + " " +c);
                                }
                            });
                            $( this ).dialog( "close" );
                        },
                        Annuler: function() {
                                $( this ).dialog( "close" );
                        }
                }
        });
                
    $('.reopen-modal-confirm').removeAttr('href');

    $('.reopen-modal-confirm').click(function(e) {
        //Cancel the link behavior
        e.preventDefault();
        $('#dialog-reopen-form').dialog('open');
    });
}

function addComment(messageId, content)
{
    if(content == "")
    {
        alert("Veuillez entrer un commentaire");
    }
    else
    {
        $.ajax({
            type: "POST",
            url: "/forum/message/comment",
            dataType: "json",
            data: { "message": messageId, "form_comment_content": content },
            success: function(response)
            {
                if(checkSuccess(response))
                {
                    var html = '<div class="comment">'+content+' - le '+response.date+' par '+response.user+'</div>';
                    $('#comment-form-'+messageId).hide();
                    $('#comments-'+messageId).append(html);
                    $('.comment-link-'+messageId).show();
                    $('#comment-form-'+messageId).find('textarea').val('');
                }
            },
            error: function(a, b, c)
            {
                alert(b + " " +c);
            }
        });
    }
}

function addMessage(topicId, content)
{
    console.log(content);
    if(content == "")
    {
        //alert("Veuillez entrer un message");
    }
    else
    {
        $.ajax({
            type: "POST",
            url: "/forum/topic/answer",
            dataType: "json",
            data: { "topic": topicId, "form_message_content": content },
            success: function(response)
            {
                if(checkSuccess(response))
                {
                    /*var html = '<div class="comment">'+content+' - le '+response.date+' par '+response.user+'</div>';
                    $('#comment-form-'+messageId).hide();
                    $('#comments-'+messageId).append(html);
                    $('.comment-link-'+messageId).show();
                    $('#comment-form-'+messageId).find('textarea').val('');*/
                }
            },
            error: function(a, b, c)
            {
                alert(b + " " +c);
            }
        });
    }
}
