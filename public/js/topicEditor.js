$(function()
{
    // Nouveau Topic
    CKEDITOR.replace('form_topic_content',{
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
                width : 700,
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
});
