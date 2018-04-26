@extends('userProfile.userInterface.main')
@inject('MediaPresenter','App\Presenters\MediaPresenter')

@section('title')
    @lang('userInterface.Photos')
@endsection
@section('interfaceContent')
<div class="container ablums_gallery">
    <div class="row">
        @for($i= 1; $i<= 5; $i++)
            <?php $photo = $album[$i] ?>
        <div class="col-md-4">
            <div class="card-box">
                <form action="" method="POST">
                <h4 class="header-title m-t-0 m-b-20">新增相片</h4>
                <input type="file" class="photo_input dropify" data-orders="{{$photo['order'] }}" data-photo-id="{{$photo['media_id']}}" data-default-file="{{$MediaPresenter->img_path($photo['media_path'])}}"/>
                <div class="form-group">
                    <?php $input_disabled = $photo['media_id'] == null ? 'disabled' : null ;?>
                    <input type="text" value="{{$photo['media_description']}}" name="photos_description" data-photo-id="{{$photo['media_id']}}" class="form-control m-t-10" placeholder="寫下照片說明..." {{$input_disabled}}>
                </div>
                </form>
            </div><!--End card-box -->
        </div>
        @endfor
    </div>
</div>
@endsection
@section('script')
<script>
    $dropifyEv = $('.dropify').dropify({
        messages: {
            'default': 'Drag and drop a image/video here or click',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong appended.'
        },
        error: {
            'fileSize': 'The file size is too big (1M max).'
        }
    });
    $dropifyEv.on('dropify.beforeClear', function(event, element){
        if($(this).data('photo-id') == '') return;
        $.post('/PUT/userProfile/photo/delete',{photo_id: $(this).data('photo-id')},function(res){
            if(res.success == true){
                alert('delete success');
                window.location.reload();
            }else if(res.success == false){
                alert('fail to delete')
                console.log(res.msg);
            }else{
                alert('something wrong');
            }
        })
    })
    /* Media - image*/
    $('.ablums_gallery').find('.photo_input').bind('change',function(){
        pData = new FormData();
        pData.append('p_order',$(this).data('orders'));
        pData.append('photo',$(this)[0].files[0]);
        $.ajax({
            url: '/PUT/userProfile/photo/update',
            cache: false,
            contentType: false,
            processData: false,
            data: pData,
            type: "POST",
            success: function(res){
                if(res.success == true){
                    alert('upload success');
                    window.location.reload();
                }else if(res.success == false){
                    alert('fail to upload')
                    console.log(res.msg);
                    window.location.reload();
                }else{
                    alert('something wrong');
                    window.location.reload();
                }
            }
        })

    })
    /*photo description*/
    $('.ablums_gallery').find('[name="photos_description"]').bind('change',function(){
        if($(this).data('photo-id') == null || $(this).data('photo-id') == '')window.location.reload();
        $photoId = $(this).data('photo-id');
        $description = $(this).val();
        $.post('/PUT/userProfile/photo/description',{photo_id: $(this).data('photo-id'),description: $description},function(res){
            if(res.success == true){
                alert('upload success');
                window.location.reload();
            }else if(res.success == false){
                alert('fail to upload')
                console.log(res.msg);
            }else{
                alert('something wrong');
            }
        })
    })
</script>
@endsection