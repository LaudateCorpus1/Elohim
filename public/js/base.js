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
    
    // Code pour voter
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
        var url = "/tag/"+action+"favorited/"+tagId[1];
        
        if(typeof auth != "undefined" && auth)
        {
            $.post(url, {}, function(response)
            {
                if(checkSuccess(response))
                {
                    var html = '<li class="favorited-style"><a href="/tag/'+response.tagname+'" class="favorited-'+response.tagid+'">'+response.tagname+'</a>\n\
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
            var url = "/tag/removefavorited/"+attr[1];
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

    // Code pour l'autocomplete des tags
    $('#tagsValues').hide();
    var tags = $('#tagsValues').val();
    
    $("#tags").tagit({
            tagSource: "/default/index/autocompletetag",
            singleField: true,
            singleFieldDelimiter: " ",
            singleFieldNode: $('#tagsValues')
    });
    
   
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