<div class="user-box">
    <div id="user-interface-icon" class="user-img">
        <?php
        $user_icon_src = '/img/components/user-symbol-empty.png';
        if($user_icon != null){
            if( file_exists(public_path(Storage::url($user_icon->media->media_location_low))) ){
                $user_icon_src = Storage::url($user_icon->media->media_location_low);
            }
        }
        ?>

        <img src="{{$user_icon_src}}" alt="user-img" title="{{$user->name}}" class="img-circle img-fit-center-thumbnail img-thumbnail img-responsive">
    </div>
    <h5><a href="#">{{$user->name}}</a> </h5>
</div>