var url = window.location.pathname;
var mouse_is_inside = false;

$(function()
{
    /*
     * Barre de notifications lors d'un(e) gain/perte de privilège
     */
    $('.lost-notify-message').css('background-color', '#8E1609')
    $("#notification-padding").show();
    $("#notify-message").fadeIn("slow");
    $("#notify-message a.close-notify").click(function() {
        var id = $(this).attr('id');
        var notificationId = id.substring(id.lastIndexOf('-') + 1);
        var parent = $(this).parent();
        var prev = $(this).prev('span');
        $.post('/index/updatenotifbar', { 'notificationId': notificationId }, function(response)
        {
            if(checkSuccess(response))
            {
                parent.fadeOut("slow");
                var isLast = prev.attr('class');
                if(isLast == 'lastnotif')
                    $('#notification-padding').hide();
                else
                    parent.next('div').fadeIn("slow");
            }

        }, "json")
        .error(function() { alert("Une erreur est survenue"); });
        
        return false;
    });

    var urlSplit = url.split('/');
    
    // Code pour voter sur un topic/message
    $('.increment').removeAttr('href');
    $('.decrement').removeAttr('href');
    $('.increment').live("click", function()
    {
        rate('voteup', $(this));
    });

    $('.decrement').live("click", function()
    {
        rate('votedown', $(this));
    });
    
    // Code pour la mise en favoris des tags
    $('a[class^="fav-"]').removeAttr('href');
    $('a[class^="fav-"]').click(function()
    {
        var attr = $(this).attr('class');
        var tagId = attr.split("-");
        var action = $(this).find('img').first().attr('class');
        var url = "/forum/tag/"+action+"favorited/"+tagId[1];
        
        if(typeof auth != "undefined" && auth)
        {
            $.post(url, {}, function(response)
            {
                if(checkSuccess(response))
                {
                    var html = '<li class="favorited-style"><a href="/forum/tag/'+response.tagname+'" class="favorited-'+response.tagid+'">'+response.tagname+'</a>\n\
                                <a class="close2">x</a></li>';

                    if(response.action == "add")
                    {
                        $('#favlist').append(html);
                        $('.fav-'+response.tagid).each(function()
                        {
                            $(this).html('<a class="fav-'+response.tagid+'" title="Retirer des favoris"><img class="remove" src="/images/moins.png" alt="retirerfavoris"/></a>');
                        });
                        
                        /*$('.tag-name > a').each(function()
                        {
                            if($(this).text() == response.tagname)
                            {
                                $(this).parent().parent().parent().parent().parent().attr('class', 'topic interest');
                                return;
                            }
                        });*/
                    }
                    else if(response.action == "remove")
                    {
                        $('.favorited-'+response.tagid).parent().remove();
                        $('.fav-'+response.tagid).each(function()
                        {
                            $(this).html('<a class="fav-'+response.tagid+'" title="Ajouter aux favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>');
                        });
                        
                        /*$('.tag-name > a').each(function()
                        {
                            if($(this).text() == response.tagname)
                            {
                                $(this).parent().parent().parent().parent().parent().attr('class', 'topic');
                                return;
                            }
                        });*/
                    }
                }
            }, 'json')
            .error(function() { alert("Une erreur est survenue"); });
        }
        else
            alert("Vous devez vous identifier");
    });

    // Code lorsqu'on clique sur la croix d'un tag favoris
    $('.close2').removeAttr('href');
    deleteTagWithCross();

    function deleteTagWithCross()
    {
        $('.close2').live("click", function()
        {
            var attr = $(this).parent().find('a').first().attr('class').split('-');
            var url = "/forum/tag/removefavorited/"+attr[1];
            $.post(url, {}, function(response)
            {
                if(checkSuccess(response))
                {
                    $('.favorited-'+response.tagid).parent().remove();
                    $('.fav-'+response.tagid).each(function()
                    {
                        $(this).html('<a class="fav-'+response.tagid+'" title="Ajouter aux favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>');
                    });
                    
                    /*$('.tag-name > a').each(function()
                    {
                        if($(this).text() == response.tagname)
                        {
                            $(this).parent().parent().parent().parent().parent().attr('class', 'topic');
                            return;
                        }
                    });*/
                }
            },'json')
            .error(function() { alert("Une erreur est survenue"); });
        });
    }

    // Code pour l'autocomplete des tags lors d'un ajout de topic
    $('#tagsValues').hide();
    var tags = $('#tagsValues').val();
    
    $("#tags").tagit({
            tagSource: "/forum/tag/autocomplete",
            singleField: true,
            singleFieldDelimiter: " ",
            singleFieldNode: $('#tagsValues')
    });
    
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
    
    dialogDeleteDocument();
    dialogAlertDocument();
    
    
    /*
     * Notifications 
     */
    $('.notifications').hover(function(){ 
        mouse_is_inside=true; 
    }, function(){ 
        mouse_is_inside=false; 
    });

    $('body').mouseup(function(){ 
        if(!mouse_is_inside) $('.notifications').fadeOut('slow');
    });
    
    $('.notifications-link').click(function()
    {
        $('.notifications').fadeIn('slow');
    });
    
    $('.close-notifications').click(function()
    {
        $('.notifications').fadeOut('slow');
    });
    
    /*
     * Mise en favoris des documents
     */
    $('a[class^="favdoc-"]').removeAttr('href');
    $('a[class^="favdoc-"]').click(function()
    {
        var attr = $(this).attr('class');
        var docId = attr.split("-");
        var action = $(this).find('img').first().attr('class');
        var url = "/doc/"+docId[1]+"/"+action+"favorite";
        
        if(typeof auth != "undefined" && auth)
        {
            $.post(url, {}, function(response)
            {
                if(checkSuccess(response))
                {
                    if(response.action == "add")
                    {
                        $('.favdoc-'+response.documentId).
                            html('<a class="favdoc-'+response.documentId+'" title="Retirer des favoris"><img class="remove" src="/images/favorite.png" alt="retirerfavoris"/></a>'); 
                    }
                    else if(response.action == "remove")
                    {
                        $('.favdoc-'+response.documentId).
                            html('<a class="favdoc-'+response.documentId+'" title="Ajouter aux favoris"><img class="add" src="/images/favorite_grey.png" alt="ajouterfavoris"/></a>');
                    }
                }
            }, 'json')
            .error(function() { alert("Une erreur est survenue"); });
        }
        else
            alert("Vous devez vous identifier");
    });

});

function checkSuccess(response)
{
    if(response.status == 'error')
    {
        alert(response.message);
        return false;
    }
    return true;
}


// Fonction de vote
function rate(action, object)
{
    var url;
    var topicId = '';
    //var val = object.parent().attr('class');
    var val = object.closest('div[class^="vote-"]').attr('class');
    var element;
    if(val == 'vote-d') 
    {
        element = object.parent().parent().parent().find('input').attr('value');
        url = "/doc/"+element+"/"+action;
    }
    else
    {
        element = object.parent().find('input').attr('value');
        topicId = $('.vote-t').find('input').first().val();
        
        if(val == 'vote-t') {
            url = "/forum/topic/"+element+"/"+action;
        }
        else if(val == 'vote-m') {
            url = "/forum/message/"+element+"/"+action;
        }
    }

    if(typeof auth != "undefined" && auth)
    {
        $.post(url, { 'topic': topicId }, function(response)
        {
            try
            {
                if(checkSuccess(response))
                {
                    if(val == 'vote-d')
                        object.parent().parent().parent().find('span').first().text(response.vote);
                    else
                        object.parent().find('span').first().text(response.vote);
                    
                    var html;
                    if(response.type.indexOf('UP') != -1)
                    {
                        if(response.revote)
                        {
                            if(val == 'vote-d')
                            {
                                object = object.parent().parent().parent().find('.disabled');
                                html = '<a class="decrement" title="Voter contre"><img src="/images/arrow_down.png" /></a>';
                            }
                                
                            else
                            {
                                object = object.parent().find('.disabled');
                                html = '<a class="decrement" title="Voter contre"><img src="/images/arrow_left_orange.gif" /></a>';
                            }
                        }
                        else
                        {
                            if(val == 'vote-d')
                                html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_up_grey.png" /></a>';
                            else
                                html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_right_grey.png" /></a>';
                        }
                            
                    }
                    else
                    {
                        if(response.revote)
                        {
                            if(val == 'vote-d')
                            {    
                                object = object.parent().parent().parent().find('.disabled');
                                html = '<a class="increment" title="Voter pour"><img src="/images/arrow_up.png" /></a>';
                            }
                            else
                            {
                                object = object.parent().find('.disabled');
                                html = '<a class="increment" title="Voter pour"><img src="/images/arrow_right_orange.gif" /></a>';
                            }
                        }
                        else
                        {
                            if(val == 'vote-d')
                                html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_down_grey.png" /></a>';
                            else
                                html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_left_grey.png" /></a>';
                        } 
                    }
                    object.replaceWith(html);
                }
            }
            catch(e)
            {

            }

        }, "json")
        .error(function() { alert("Une erreur est survenue"); });
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


/**************************/
/******** Library *********/
/**************************/
function dialogDeleteDocument()
{
    $( "#dialog-delete-document" ).dialog({
                autoOpen: false,
                resizable: false,
                height:150,
                width: 350,
                modal: true,
                buttons: {
                        Valider: function() {
                            var id = $(this).data('id');
                            var idSplit = id.split('-');
                            var documentId = idSplit[3];
                            var action = idSplit[4];
                            $.ajax({
                                type: "POST",
                                url: "/doc/"+documentId+"/delete/",
                                dataType: "json",
                                success: function(response)
                                {
                                    if(checkSuccess(response))
                                    {
                                        if(action == 'show')
                                            window.location = '/library/'+response.username;
                                        else if(action == 'index')
                                            $('#'+id).parent().parent().parent().remove();
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
                
    $('a[id^=delete-document-link]').removeAttr('href');
    
    $('a[id^=delete-document-link]').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('id');
        $('#dialog-delete-document').data('id', id).dialog('open');
    });
}

function dialogAlertDocument()
{
    $('#dialog-alert-document').dialog({
        autoOpen: false,
        height: 210,
        width: 600,
        modal: true,
        buttons: {
                Valider: function() {
                    
                    var id = $(this).data('id');
                    var motifElement = $('#dialog-alert-document').find('#motif');
                    if(motifElement.val() == "") {
                        alert("Veuillez entrer un motif");
                    }
                    else
                    {
                        var data = $('#form_document_alert').serializeArray();
                        $('.ui-dialog-buttonset').find('button:first').hide();
                        motifElement.addClass('ui-autocomplete-loading');
                        
                        $.ajax({
                            type: "POST",
                            url: "/library/alert/id/"+id,
                            dataType: "json",
                            data: data,
                            success: function(response)
                            {
                                if(checkSuccess(response))
                                {
                                    motifElement.removeClass('ui-autocomplete-loading');
                                    $('#dialog-alert-document').append('<div class="message">'+response.message+'</div>');
                                    $('.ui-dialog-buttonset').find('button:last > span').text('Fermer');
                                    /*if(response.count == '1')
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
                                    }*/
                                }
                            },
                            error: function(a, b, c)
                            {
                                alert('Une erreur est survenue');
                            }
                        });
                    }
                    //$( this ).dialog('close');
                    //$('#dialog-alert-document').find('#motif').removeClass('ui-autocomplete-loading');
                },
                Annuler: function() {
                        $( this ).dialog('close');
                }
        },
        close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });
                
    $('.alert-document-link').removeAttr('href');
    $('#validate_document_alert').parent().parent().remove();
    
    
    $('.alert-document-link').click(function(e) {
        var _id = $(this).attr('id');
        var documentId = _id.substring(_id.lastIndexOf('-') + 1);
        //Cancel the link behavior
        e.preventDefault();
        // Si le dialog est ouvert à nouveau après un envoi (sans recharger la page)
        if ($('#dialog-alert-document > .message').length)
        {
            $('.ui-dialog-buttonset').find('button:first').show();
            $('.ui-dialog-buttonset').find('button:last > span').text('Annuler');
            $('#dialog-alert-document').find('#motif').val('');
            $('#dialog-alert-document > .message').remove();
        }
        $('#dialog-alert-document').data('id', documentId).dialog('open');
    });
}