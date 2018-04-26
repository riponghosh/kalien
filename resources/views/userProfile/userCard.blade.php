<div class="guideGrid grid-item container-fluid guideCard hover-shadow" style="width: 270px;">
    <a data-user-name="{{$guide->uni_name}}" class="open-modal showGuideProfile" >
    <div class="row" style="height: 150px;">
    @if($is_mb == 'false')
        <?php $has_video = false; ?>
        @foreach($guide->trip as $trip)
            @foreach($trip->trip_media as $trip_media)
                @if($trip_media->media->media_format == 'url')
                    <div class="meida-topic-tag omit_text">
                        {{$trip->trip_title}}
                    </div>
                    <div class="media_wrapper">
                        <div class="cardVideo" id="card_{{$trip_media->media_id}}" data-video-url="{{$trip_media->media->media_location_standard}}">
                        </div>
                    </div>
                    <?php break 2;?>
                @endif
            @endforeach
            @foreach($trip->trip_media as $trip_media)
            @if($trip_media->media_type == 'img')
                <div class="meida-topic-tag omit_text">
                    {{$trip->trip_title}}
                </div>
                <div class="media_wrapper">
                    <div class="cardCoverImg" id="card_{{$trip_media->media_id}}" style="width:100%;height:100%">
                        <img class="img-fit-cover" src="{{Storage::url($trip_media->media->media_location_standard)}}">
                    </div>
                </div>
                <?php break 2;?>
            @endif
            @endforeach
        @endforeach
    @elseif($is_mb == 'true')
        @foreach($guide->trip as $trip)
            @foreach($trip->trip_media as $trip_media)
                @if($trip_media->media_type == 'img')
                    <div class="meida-topic-tag omit_text">
                        {{$trip->trip_title}}
                    </div>
                    <div class="media_wrapper">
                        <div class="cardCoverImg" id="card_{{$trip_media->media_id}}" style="width:100%;height:100%">
                            <img class="img-fit-cover" src="{{Storage::url($trip_media->media->media_location_standard)}}">
                        </div>
                    </div>
                    <?php break 2;?>
                @endif
            @endforeach
        @endforeach
    @endif
    </div><!--media row-->
    <div class="row">
        <div class="userCircle">
            @if (file_exists(public_path(Storage::url($guide->user_icon[0]->media->media_location_low))))
                <img class="userImg img-circle" src="{{Storage::url($guide->user_icon[0]->media->media_location_low)}}"/>
            @else
                <img class="userImg img-circle" src="/img/icon/user_icon_bg.png"/>
            @endif
        </div>
        <div class="name_container col-xs-offset-6">
            <p class="name_container">
                {{$guide->name}}
            </p>
            <p class="city_container">
                @if(count($guide->guide->guideServicePlace) > 0)
                <?php $rank = 0 ?>
                @foreach($guide->guide->guideServicePlace as $city)
                    @if($city->service_place->rank >= $rank)
                        <?php $rank = $city->service_place->rank;
                        $service_place = $city->service_place->city_name;
                        ?>
                    @endif
                @endforeach
                @lang('countries.'.$service_place){{' -'}}
                @endif
                {{ $CountryPresenter->service_country_name($guide->guide->service_country) }}
            </p>
        </div>
    </div>
    <div class="card_bottom row">
        <div class="col-xs-12">
            @if(count($guide->languages) > 0)
            <?php $i = 1; ?>
            <div class="row">
                <div class="col-xs-12">
                    <span style="font-size: 10px;color: rgba(0,0,0,0.5);display:block;margin-bottom:8px;">Langauges</span>
                    <ul class="list-inline">
                    @foreach($guide->languages as $language)
                    @if($i < 3)
                        <li class="round-corner-tag">
                            {{$UserPresenter->language($language->language->language_name)}}
                        </li>
                    @elseif($i == 3)
                        @if(count($guide->languages) <= 3)
                            <li class="round-corner-tag">
                                {{$UserPresenter->language($language->language->language_name)}}
                            </li>
                        @else
                            <li class="round-corner-tag block-color">
                                <?php $more_lan = count($guide->languages)-2; ?>
                                {{'+'.$more_lan}}
                            </li>
                        @endif
                    @endif
                    <?php $i ++ ?>
                    @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <hr>
            <div class="row"><!--TODO-->
                <?php $sum_rating = 0; $i = 0;$avg_rating = 0.0;?>
                @if(count($guide->review) > 0 )
                    @foreach($guide->review as $review)
                    <?php
                        $sum_rating = $sum_rating + $review->rating;
                        $i++;
                    ?>
                    @endforeach
                    <?php $avg_rating = $sum_rating/$i; ?>
                @endif
                <?php
                $is_actived = $avg_rating > 0 ? 'star-active' : 'star-inactive';
                ?>
                <div class="col-xs-5 p-b-10">
                    <span id="rating_{{$guide->id}}"><i class="fa fa-star {{$is_actived}}">{{number_format($avg_rating,1,'.','')}}</i></span><span style="color: rgba(0,0,0,0.5);">({{$i}})</span>
                </div>
                <div class="col-xs-7 pull-right text-right" style="margin-top: -5px; ">
                    <?php
                    $price = 'Free';
                    if(isset($guide->guide->charge_per_day) && isset($guide->guide->currency_unit)){
                        $price = number_format(cur_convert($guide->guide->charge_per_day, $guide->guide->currency_unit)*0.1,1,'.',',');
                    }
                    ?>
                    <span style="font-size: 10px;color: rgba(0,0,0,0.5);margin-bottom:1px;">{{$UserPresenter->cur_units(CLIENT_CUR_UNIT, 's')}}/小時</span>
                        <span style="font-size: 18px">{{$price}}</span>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>
<script>
    /*
    $(document).ready(function(){
        $('#rating_{{$guide->id}}').rating({{$avg_rating}});
    })
    */
</script>


