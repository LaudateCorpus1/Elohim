$(function()
{
    var url = window.location.pathname;
    
    // Lien répondre à un topic
    $('#answer-topic > a').removeAttr('href');
    CKEDITOR.replace('form_message_content',{
		toolbar : [['Bold','Italic','Underline', 'FontSize', '-', 'Image', '-', 'Undo','Redo','-','NumberedList', 'BulletedList','-','Link','Unlink', '-', 'About']],
                //filebrowserBrowseUrl: '/simogeo-Filemanager-8b138bc/index.html',
                language : 'fr',
                scayt_autoStartup : true,
                scayt_sLang : 'fr_FR',
                scayt_contextCommands : 'off',
                contentsCss : '/css/assets/output_xhtml.css',
                coreStyles_bold	: { element : 'span', attributes : {'class': 'Bold'} },
                coreStyles_italic	: { element : 'span', attributes : {'class': 'Italic'}},
                coreStyles_underline	: { element : 'span', attributes : {'class': 'Underline'}},
                fontSize_sizes : '10/FontTen;12/FontTwelve;14/FontFourteen;16/FontSixteen;20/FontTwenty;24/FontTwentyfour',
                fontSize_style :
                {
                    element : 'span',
                    attributes : { 'class' : '#(size)' }
                },
                forcePasteAsPlainText :true,
                
                on :
                {
                    instanceReady : function( ev )
                    {
                        this.dataProcessor.writer.setRules( 'p',
                        {
                            indent : false,
                            breakBeforeOpen : true,
                            breakAfterOpen : false,
                            breakBeforeClose : false,
                            breakAfterClose : true
                        });
                    }
                }
    });
    
    
    $('#answer-topic > a').click(function()
    {
        $('#block-quick-answer').show();
        $('a[name=answer]').offset().top;
    });
    var submitted = false;
    // Lorsque l'utilisateur envoie le message
    $('form[id=form_message]').submit(function()
    {
        if(!submitted)
        {
            var topicId = $('.vote-t').find('input').first().val();
            CKEDITOR.instances.form_message_content.updateElement();
            var content = $('#form_message_content').val();
            if(content == "")
            {
                alert("Veuillez entrer un message");
            }
            else
            {
                submitted = true;
                addMessage(topicId, content);
            }
        }
        return false;
    });
});

function addMessage(topicId, content)
{
    $.ajax({
            type: "POST",
            url: "/forum/"+topicId+"/answer",
            dataType: "json",
            data: { "form_message_content": content },
            success: function(response)
            {
                if(checkSuccess(response))
                {
                    window.location.reload();
                    /*var html = '<div id="message">'+content+' - le '+response.date+' par '+response.user+'</div>';
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