$(function()
{
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
        
        $.post(url, {}, function(response)
        {
            parent.find('span').first().text(response);

        });
    }

    // Code pour la mise en favoris des tags
    $('a[class^="fav-"]').attr('href','#');
    $('a[class^="fav-"]').click(function()
    {
        var attr = $(this).attr('class');
        var tagId = attr.split("-");
        var url = "/forum/tag/favorite/tag/"+tagId[1];

        $.post(url, {}, function(response)
        {
            var responseSplit = response.split("/");
            var html = '<li class="favorited-style"><a href="/forum/topic/tag/name/'+responseSplit[0]+'" class="favorited-'+tagId[1]+'">'+responseSplit[0]+'</a>\n\
                        <a class="close2">x</a></li>';

            if(responseSplit[1] == "add")
            {
                $('#favlist').append(html);
                $('.close2').attr('href','#');
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
        });
    });

    // Code lorsqu'on clique sur la croix d'un tag favoris
    $('.close2').attr('href','#');
    deleteTagWithCross();

    function deleteTagWithCross()
    {
        $('.close2').click(function()
        {
            var attr = $(this).parent().find('a').first().attr('class').split('-');
            url = "/forum/tag/removefavoritedajax/tag/"+attr[1];
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
    $("#tags").tagit({
            availableTags: "/forum/tag/autocomplete"
    });
});