var url = window.location.pathname;

$(function()
{

    dialogDeleteDocument();
    dialogAlertDocument();
    
  
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
    
    /*
     * Trier les documents
     */
    $('#form_sort_document').change(function()
    {
        var sort = $("select option:selected").val();
        var tag = $('#form_sort_tagname').val();
        var category = $('#form_sort_category').val();
        var url = "/doc/sort/" + sort;
        
        if(tag != '')
            url += '/tag/' + tag;
        
        if(category != '')
            url += '/cat/' + category;
            
        window.location.href = url;
    });
});

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
        if(typeof auth != "undefined" && auth)
        {
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
        }
        else
            alert("Vous devez vous identifier");
    });
}