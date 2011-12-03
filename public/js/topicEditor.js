$(function()
{
    // Nouveau Topic
    CKEDITOR.replace('form_topic_content',{
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
                fontSize_sizes : 'Smaller/FontSmaller;Larger/FontLarger;8pt/FontSmall;14pt/FontBig;Double Size/FontDouble',
                width : 700
    });
});
