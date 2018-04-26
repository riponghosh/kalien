$(document).ready(function(){
    checked();
    guide();
})

function checked(){
    $('.btn-group > .btn').click(function(){
        $(this).hasClass('checked')?
        $(this).removeClass('checked'):
        $(this).addClass('checked');
    })
}
function guide(){
  $('#filter-form').find('.flt-btn').click(function(){
    var data = $('#filter-form').serializeArray();
    console.log(data);    
    $.ajax({
            data    :   data,
            url     :   '/guideFilter',
            type    :   'post', 
            success :   function(response){
                            $('#showGuide').find($('.main')).html(response);
                        }
        })
    })
}
