var url = window.location.pathname;

$(function()
{
    var urlSplit = url.split('/');
    
    // Code pour voter sur un topic/message
    $('.increment').removeAttr('href');
    $('.decrement').removeAttr('href');
    $('.increment').live("click", function()
    {
        rate('incrementvote', $(this));
    });

    $('.decrement').live("click", function()
    {
        rate('decrementvote', $(this));
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
                            $(this).html('<a class="fav-'+tagId[1]+'" title="Retirer des favoris"><img src="/images/moins.png" alt="retirerfavoris"/></a>');
                        });
                    }
                    else if(responseSplit[1] == "remove")
                    {
                        $('.favorited-'+tagId[1]).parent().remove();
                        $('.fav-'+tagId[1]).each(function()
                        {
                            $(this).html('<a class="fav-'+tagId[1]+'" title="Ajouter aux favoris"><img src="/images/plus2.png" alt="ajouterfavoris"/></a>');
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
                    $(this).html('<a class="fav-'+attr[1]+'" title="Ajouter aux favoris"><img src="/images/plus2.png" alt="ajouterfavoris"/></a>');
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
    
    dialogCloseTopic();
    dialogCloseMotif();    
    dialogReopenTopic();
    dialogValidateAnswer();
    
    /*$('#loading_div').hide().ajaxStart(function()
    {
        $(this).show();
    }).ajaxStop(function() 
    {
        $(this).hide();
    });*/

});

function checkSuccess(response)
{
    try
    {
        var obj = null;
        if(typeof(response) != 'object')
        {
            obj = jQuery.parseJSON(response);
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
function rate(action, object)
{
    var url;
    var val = object.parent().attr('class');
    if(val == 'vote-t')
    {
        url = "/forum/topic/"+action+"/topic/";
    }
    else if(val == 'vote-m')
    {
        url = "/forum/message/"+action+"/message/";
    }

    var element = object.parent().find('input').attr('value');
    url += element;

    if(auth)
    {
        $.post(url, {}, function(response)
        {
            try
            {
                if(checkSuccess(response))
                {
                    object.parent().find('span').first().text(response.vote);
                    
                    var html
                    if(response.type.indexOf('UP') != -1)
                    {
                        if(response.revote)
                        {
                            object = $('a[class=disabled]');
                            html = '<a class="decrement" title="Voter contre"><img src="/images/arrow_left_orange.gif" /></a>';
                        }
                        else
                            html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_right_grey.png" /></a>';
                    }
                    else
                    {
                        if(response.revote)
                        {
                            object = $('a[class=disabled]');
                            html = '<a class="increment" title="Voter pour"><img src="/images/arrow_right_orange.gif" /></a>';
                        }
                        else
                            html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_left_grey.png" /></a>';
                    }
                    object.replaceWith(html);
                }
            }
            catch(e)
            {

            }

        }, "json");
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

function dialogValidateAnswer()
{
    $( "#dialog-validate-message" ).dialog({
                autoOpen: false,
                resizable: false,
                height:180,
                width: 350,
                modal: true,
                buttons: {
                        Valider: function() {
                            $.ajax({
                                type: "POST",
                                url: "/forum/message/validate",
                                dataType: "json",
                                data: { "message": messageId },
                                success: function(response)
                                {
                                    if(checkSuccess(response))
                                    {
                                        var topicId = url.substring(url.lastIndexOf('/') + 1);
                                        $('.message-validation').hide();
                                        $('#message-'+messageId).find('.message-validation').html('<a href="/forum/message/devalidate/topic/'+topicId+'/message/'+messageId+'">Annuler validation</a>');
                                        $('#message-'+messageId).find('.message-validation').show();
                                        //$('#messages').prepend($('#message-'+messageId)).hide().slideDown('slow');
                                        $('#message-'+messageId).hide().prependTo("#messages").fadeIn(1000);
                                        $('#message-'+messageId).css('background-color', '#9F9');
                                        $(window).scrollTop($('#message-'+messageId).position().top)
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
                
    $('.message-validation-link').removeAttr('href');
    
    $('.message-validation-link').click(function(e) {
        //Cancel the link behavior
        e.preventDefault();
        _id = $(this).parents('div:eq(1)').attr('id');
        messageId = _id.substring(_id.lastIndexOf('-') + 1);
        $('#dialog-validate-message').dialog('open', {'messageId': messageId});
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
