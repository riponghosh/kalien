function bindSummerNoteEditor($element, options){
  $element.summernote($.extend({
    lang: 'zh-TW',
    height: 350,
    focus: true,
    popover: {
      image: [
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['custom', ['imageShape']], //'imageAttributes',
        ['remove', ['removeMedia']]
      ],
    },
    toolbar: [
//        ['style', ['style']],
//        ['font', ['bold', 'underline', 'clear']],
//        ['fontname', ['fontname']],
//        ['color', ['color']],
//        ['para', ['ul', 'ol', 'paragraph']],
//        ['table', ['table']],
//        ['insert', ['link', 'picture', 'video']],
//        ['view', ['fullscreen', 'codeview', 'help']],
      ['font1', ['style']],
      // ['font2', ['bold', 'underline', 'italic', 'superscript', 'subscript', 'strikethrough', 'clear']],
      // ['font3', ['fontname', 'fontsize', 'height', 'shape']],
      // ['color', ['color']],
      // ['para', ['ul', 'ol', 'paragraph']],
      // ['table', ['table']],
      ['insert', ['link', 'picture', 'video']],
      // ['view', ['fullscreen', 'codeview', 'help']],
      ['view', ['fullscreen']],
    ],
  }, options));
}
