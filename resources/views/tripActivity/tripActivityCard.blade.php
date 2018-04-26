<a href="{{$card_url}}" target="_blank">
<div class="trip_activity_card m-b-15" style="width:270px">
    <div class="row" style="height: 150px;">
        <div class="media_wrapper">
            <div class="cardCoverImg" id="" style="width:100%;height:100%">
                <img class="img-fit-cover" src="{{$MediaPresenter->img_path($trip_activity_card->trip_gallery_pic)}}">
            </div>
        </div><!-- End media_wrapper -->
    </div>
    <div class="row content_wrapper">
        <div class="col-xs-12">
            <p class="h5">{{$trip_activity_card->title}}</p>
            <p>{{$trip_activity_card->sub_title}}</p>
        </div>
    </div>
    <div class="card_bottom"></div>
</div>
</a>
