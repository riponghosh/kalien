/*****************************************
 ** ajax設定
 ******************************************/
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    cache: false,
    statusCode: {
        401: function() {
            window.open('/login');
        },
        404: function() {
            alert('Something wrong');
        }
    },
    beforeSend : function (xhr,btn){
    },
    complete:function () {
    },
});
