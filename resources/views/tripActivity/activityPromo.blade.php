<section class="activity-promo-section">
    <p class="trip-activity-section-title h3" style="font-weight: 400">{{trans('tripActivity.TitleActivityIntroduction')}}</p>
    @if($trip_activity['description'] != null)
        <div class="trip-activity-intro m-b-15">
            {{$trip_activity['description']}}
        </div>
    @endif
    @foreach($trip_activity['media'] as $trip_activity_img)
        @if(!$trip_activity_img['is_gallery_image'])
            <div class="trip-activity-intro-imgs m-b-25">
                <img class="m-b-5" src="{{$trip_activity_img['url']}}">
                <div class="desc">
                    {{$trip_activity_img['description_zh_tw']}}
                </div>
            </div>
        @endif
    @endforeach
</section>