tinymce.init({ 
    selector:'textarea.tinymce',
    plugins: "link,code,table",
    height: 300,
    convert_urls : 0,
    remove_script_host : 0,
    toolbar: "undo redo | styleselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | indent outdent | table | code"
});