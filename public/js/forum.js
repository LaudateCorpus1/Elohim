var url = window.location.pathname;

$(function()
{
    /*
     * Commentaires
     */
    var editComment = false;
    var commentId = null;
    // Code pour le formulaire des commentaires
    $('a[class^=comment-link]').removeAttr('href');
    var submitted = false;
    $('a[class^=comment-link]').click(function()
    {
        editComment = false;
        submitted = false;
        var attr = $(this).attr('class');
        var messageId = attr.substring(attr.lastIndexOf('-') + 1);
        var type = attr.split('-');
        $('#comment-form-'+type[2]+'-'+messageId).find('div').remove('.remaining-char');
        $('#comment-form-'+type[2]+'-'+messageId).find('div').first().append('<div class="remaining-char">500 caractères restants</div>');
        $('#comment-form-'+type[2]+'-'+messageId).find('textarea').val('');
        $('#comment-form-'+type[2]+'-'+messageId).show();
        $(this).hide();
    });
    
    // Compteur des caractères du commentaire
    $('#form_comment_content').live('keyup', function()
    {
        var charLength = $(this).val().length;
        $('.remaining-char').text(500 - charLength + ' caractères restants');
    });
    
    // Edition d'un commentaire
    $('a[class^=edit-comment]').removeAttr('href');
    $('a[class^=edit-comment]').live('click', function()
    {
        editComment = true;
        submitted = false;
        
        var attr = $(this).attr('class');
        commentId = attr.substring(attr.lastIndexOf('-') + 1);
        
        var parentInfo = $(this).parent().parent().attr('id').split('-');
        var divForm = $('#comment-form-'+parentInfo[1]+'-'+parentInfo[2]);
        divForm.find('textarea').val($(this).parent().find('.comment-text').text());
        divForm.find('div').remove('.remaining-char');
        divForm.find('div').first().append('<div class="remaining-char"></div>');
        $('.remaining-char').text(500 - divForm.find('textarea').first().val().length + ' caractères restants');
        divForm.show();
        
        if(!checkVisible(divForm))
        {
            $('html, body').animate(
            {
                scrollTop: divForm.offset().top
            }, 1000);
        }
        
    });
    
    // Lorsque l'utilisateur envoie le commentaire
    $('form[id^=form_comment]').submit(function()
    {
        var id = $(this).attr('id');
        var messageId = id.substring(id.lastIndexOf('_') + 1);
        var content = $(this).find('textarea').val();
        var type = id.split('_');
        
        submitted = saveComment(messageId, content, submitted, type[2], editComment, commentId);
        return false;
    });
    
    dialogCloseTopic();
    dialogCloseMotif();    
    dialogReopenTopic();
    // La dévalidation en ajax n'est pas utilisée
    dialogValidateAnswer(true);
});

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
                                            dataType: "json",
                                            data: data,
                                            success: function(response)
                                            {
                                                if(checkSuccess(response))
                                                {
                                                    if(response.count == '1')
                                                        $('.close-motif').show();//$('#topic-menu').append('<a href="#" >('+response+')</a>');

                                                    $('.close-motif').text('('+response.count+')');

                                                    $('#dialog-motif > ol').append("<li>" + $('input[name=close_motif]').val() + " par <strong>" + $('input[name=username]').val() + "</strong></li>");

                                                    if(response.count == '7')
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
                                                alert('Une erreur est survenue');
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
                height:200,
                width: 350,
                modal: true,
                buttons: {
                        Valider: function() {
                            var topic_id = $('input[name=topic_id]').val();
                            $.ajax({
                                type: "POST",
                                url: "/forum/topic/reopen",
                                dataType: "json",
                                data: {"topic_id": topic_id},
                                success: function(response)
                                {
                                    if(checkSuccess(response))
                                    {
                                        var count = parseInt(response.count);
                                        var diff = 6;
                                        if(count != 7)
                                            diff = 6 - count;

                                        $('#dialog-reopen-form > strong').text(diff);

                                        if(count == 1)
                                            $('.nb-reopen-votes').show();//$('#topic-menu').append('<span class="nb-reopen-votes">('+response+')</span>');
                                            $('.nb-reopen-votes').attr('title', response.count+' personnes ont déjà voté');
                                            $('.nb-reopen-votes').text('('+response.count+')');

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
                                    alert('Une erreur est survenue');
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

function dialogValidateAnswer(validation)
{
    var ac = 'validate';
    if(!validation)
        ac = 'devalidate'
    var topicId = $('.vote-t').find('input').first().val();
    $( "#dialog-"+ac+"-message" ).dialog({
                autoOpen: false,
                resizable: false,
                height:200,
                width: 350,
                modal: true,
                buttons: {
                        Valider: function() {
                            var messageId = $(this).data('messageId');
                            $.ajax({
                                type: "POST",
                                url: "/forum/message/"+ac,
                                dataType: "json",
                                data: {"message": messageId, "topic": topicId},
                                success: function(response)
                                {
                                    if(checkSuccess(response))
                                    {
                                        $('.message-validation').hide();
                                        $('#message-'+messageId).find('.message-validation').html('<a href="/forum/message/devalidate/topic/'+topicId+'/message/'+messageId+'" class="message-devalidate-link label">Annuler validation</a>');
                                        $('#message-'+messageId).find('.message-validation').show();
                                        $('#message-'+messageId).hide().prependTo("#messages").fadeIn(1000);
                                        $('#message-'+messageId).css('background-color', '#9F9');
                                        
                                        $('html, body').animate(
                                        {
                                            scrollTop: $('#message-'+messageId).offset().top
                                        }, 500);
                                        //$(window).scrollTop($('#message-'+messageId).position().top)
                                    }
                                },
                                error: function(a, b, c)
                                {
                                    alert('Une erreur est survenue');
                                }
                            });
                            $( this ).dialog( "close" );
                        },
                        Annuler: function() {
                                $( this ).dialog( "close" );
                        }
                }
        });
                
    $('.message-'+ac+'-link').removeAttr('href');
    
    $('.message-'+ac+'-link').click(function(e) {
        //Cancel the link behavior
        e.preventDefault();
        var _id = $(this).parents('div:eq(1)').attr('id');
        var messageId = _id.substring(_id.lastIndexOf('-') + 1);
        // On passe la variable messageId au modal dialog
        $('#dialog-'+ac+'-message').data('messageId', messageId).dialog('open');
    });
}

/*
 * Params : edit et commentId ne servent que pour l'édition d'un commentaire
 * messageId est l'id du message ou du topic sur lequel le commentaire est fait
 * submitted indique si le bouton a bien été cliqué ou pas (pour éviter le spam)
 */
function saveComment(messageId, content, submitted, controller, edit, commentId)
{
    edit = typeof(edit) != 'undefined' ? edit : false;
    
    if(content == "") {
        alert("Veuillez entrer un commentaire");
    }
    else
    {
        if(!submitted)
        {
            var _url = "/forum/"+controller+"/comment";
            var postId = messageId;
            if(edit)
            {
                _url = "/forum/comment/edit/"+commentId;
                postId = commentId;
            }
                
                
            var topicId = $('.vote-t').find('input').first().val();
            $.ajax({
                type: "POST",
                url: _url,
                dataType: "json",
                data: {"topic": topicId, "message": postId, "form_comment_content": content},
                success: function(response)
                {
                    if(checkSuccess(response))
                    {
                        submitted = true;
                        if(edit)
                        {
                            $('.edit-comment-'+commentId).parent().find('.comment-text').text(content);
                        }
                        else
                        {
                            var html = '<div class="comment"><span class="comment-text">'+content+'</span> - Le '+response.date+' par <a href="/users/'+response.userId+'/'+response.user+'">'+response.user+'</a> - <a class="edit-comment-'+response.commentId+'">Editer</a></div>';
                            $('#comments-'+controller+'-'+messageId).append(html);
                            $('.comment-link-'+controller+'-'+messageId).show();
                        }
                        $('#comment-form-'+controller+'-'+messageId).hide();
                        $('#comment-form-'+controller+'-'+messageId).find('textarea').val('');
                        
                    }
                },
                error: function(request, status, error)
                {
                    alert("Une erreur est survenue");
                }
            });
        }
    }
    return submitted;
}

function checkVisible(elm) {
    var vpH = $(window).height(), // Viewport Height
        st = $(window).scrollTop(), // Scroll Top
        y = $(elm).offset().top;

    var invisible = (y > (vpH + st));
    
    return !invisible;
}

