const elixir = require('laravel-elixir');

require('laravel-elixir-vue');
var path = {
  'masonryLayout' : 'node_modules/masonry-layout/masonry.js',
  'masonryLayoutPkgd' : 'node_modules/masonry-layout/dist/masonry.pkgd.min.js',

};

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.webpack([
        'Vue/appDefault.js', 
        'Vue/groupActivity.js',
        'Vue/footer.js',
    ],'public/js/mVue/groupActivity.js');
    mix.webpack([
        'Vue/appDefault.js', 
        'Vue/homePage.js',
        'Vue/footer.js',
    ],'public/js/mVue/homePage.js'); 
    mix.webpack([
        'Vue/appDefault.js', 
        'Vue/ticket.js',
        'Vue/footer.js',
    ],'public/js/mVue/ticket.js'); 
    mix.webpack([
        'Vue/appDefault.js', 
        'Vue/product.js',
        'Vue/footer.js',
    ],'public/js/mVue/product.js'); //for mobile Vue
    //above is mobile env
    mix.sass([
      'bootstrap.min.css',
      'app.scss',
      'pulgin/bootstrap-datepicker-white.min.css',
      'pulgin/daterangepicker-white.css',
      'pulgin/chosen.css',
      'pulgin/cropper.scss',
      'pulgin/nouislider.css',
      'pulgin/summernote.css',
      'pulgin/sweet-alert.css',
      'pulgin/toastr.min.css',
      'pulgin/select2.scss',
      'pulgin/select2-bootstrap.css',
      'pulgin/hopscotch.css',
      'pulgin/card.css',
      'scheduleDesk/all.scss',
      'userProfile/index_modal.scss',
      'trip_appointment_info.scss',
      'TripActivity/main.scss',
      'GroupActivity'
  ],'public/css/app.css')
        .copy(path.masonryLayout,'public/js/masonryLayout.js')
   .copy(path.masonryLayoutPkgd,'public/js/masonryLayoutPkgd.js')
   .webpack('app.js');
    //mix.webpack(['resources/assets/js/employee.js'], 'public/js/vue');
    mix.sass([
        'userInterface',
        'TripActivityCard/main.scss'
    ],'public/css/userInterface.css');
    mix.sass([
        'pulgin/cropper.scss',
        'pulgin/bootstrap-datepicker.min.css',
        'pulgin/switchery.min.css',
        'pulgin/hopscotch.css',
        'pulgin/toastr.min.css',
        'pulgin/dropify.min.css',
        'pulgin/tablesaw.css'
    ],'public/css/userInterface/pulgin.css');
    mix.scripts([
        'pulgin/jquery.multi-select.js',
        'pulgin/select2.min.js',
        'pulgin/jquery.quicksearch.js',
        'pulgin/bootstrap-datepicker.min.js',
        'pulgin/switchery.min.js'
    ],'public/js/userInterfaceAbouts/pulgin.js');
    mix.sass([
        'pulgin/select2.scss',
        'pulgin/select2-bootstrap.css',
        'pulgin/multi-select.css'
    ],'public/css/userInterfaceAbouts.css');
    mix.sass([
        'pulgin/dropify.min.css',
    ],'public/css/userInterfaceTripsIntroduction/pulgin.css');
    mix.scripts([
        'pulgin/dropify.min.js',
    ],'public/js/userInterfaceTripsIntroduction/pulgin.js');
    mix.scripts([
        'userInterfaceTripIntroduction/main.js'
    ],'public/js/userInterfaceTripsIntroduction.js');
    mix.scripts([
      'lib/lodash.min.js',
      'jquery/jquery-ondrag.js',
      'jquery/jquery-donetyping.js',
      'moment.js',
      'summernote/summernote.js',
      'summernote/zh-TW.js',
      'summernote/summernote-image-attributes.js',
      'summernote/editor.js',
      'schedule/date_column.js',
      'schedule/event_sync_manager.js',
      'schedule/dragging_line.js',
      'schedule/event.js',
      'schedule/event_info.js',
      'schedule/schedule.js',
    ], 'public/js/schedule/schedule.js');
    mix.scripts([
        'userInterface',

    ],'public/js/userInterface.js');
    mix.scripts([
        'tripActivityEditor'
    ],'public/js/employee.js');
    mix.scripts([
        'lib/lodash.min.js',
        'pulgin/switchery.min.js',
        'package/cropper.js',
        'pulgin/canvas-to-blob.js',
        'pulgin/hopscotch.min.js',
        'pulgin/toastr.min.js',
        'pulgin/dropify.min.js',
        'pulgin/tablesaw.js',
        'pulgin/tablesaw-init.js',
        'pulgin/jquery.magnific-popup.min.js',
        'pulgin/jquery.dataTables.js',
        'pulgin/dataTables.bootstrap.js',
        'pulgin/mindmup-editabletable.js',
        'pulgin/datatables.editable.init.js'
    ],'public/js/userInterface/pulgin.js');
    mix.scripts([
      'login/login.js'
    ],'public/js/login/login.js')
    mix.scripts([
      'groupActivity/main.js',
      'login/login.js',
      'login/mailActivate.js',
      'globalFunction.js',
      'ajaxForm.js'
    ],'public/js/gobal.js')
    mix.sass('guide/guide.scss','public/css/guide/guide.css');
    mix.scripts('guide.js');
    mix.scripts(['userProfile/edituserProfile.js',
                 'userProfile/userProfile_modal.js'
    ],'public/js/userProfile/all.js');
    mix.scripts('jquery/jquery.cookie.js');
    mix.scripts('jquery/chosen.jquery.min.js');
    mix.scripts([
        'jquery/jquery.cookie.js',
        'jquery/chosen.jquery.min.js',
        'package/noUiSlider/nouislider.js',
        'pulgin/toastr.min.js',
        'package/cropper.js',
        'pulgin/canvas-to-blob.js',
        'pulgin/iphone-inline-video.js',
        'pulgin/moment.js',
        'pulgin/bootstrap-datepicker.min.js',
        'pulgin/daterangepicker.js',
        'pulgin/sweet-alert.js',
        'pulgin/select2.min.js',
        'pulgin/bootstrap-inputmask.min.js',
        'userNotification/userNotification.js',
        'pulgin/creditCardValidator',
        'pulgin/hopscotch.min.js',
        'jquery/jquery.payment.js'
    ],'public/js/package/all.js');
    mix.scripts(['bsModalExtend/bsModalStep.js',
                 'bsModalExtend/all.js'
    ],'public/js/bsModalExtend.js');
    mix.sass('chatRoom/all.scss', 'public/css/chatRoom/all.css');
    mix.scripts([
                   'jquery/jquery.cookie.js',
                   'chatRoom/rooms_manager.js',
                   'chatRoom/room_ui.js',
                   'chatRoom/room.js',
                    'chatRoom/room_group_display.js'
                ], 'public/js/chatRoom/all.js');
    mix.scripts([
                    'searchingFilter/searchingFilter_ui.js',
                    'searchingFilter/main.js',
                ],  'public/js/searchingFilter/all.js');
    mix.browserify('socketTest.js');
});