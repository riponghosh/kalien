@extends('userProfile.userInterface.main')
@section('title')
    @lang('userInterface.TripsIntroduction')
@endsection
@section('css')
    {!! Html::style('css/userInterfaceTripsIntroduction/pulgin.css')!!}
@endsection
@section('interfaceContent')
<div class="container" id="Trip-card-container">
    <div class="row card-box-area">
    </div>
    <div class="row">
        <div class="col-sm-12">
            <button id="addTripsBtn" class="btn btn-success btn-bordred w-md waves-effect waves-light">+ Add trip</button>
        </div>
    </div>
</div>
@endsection
@section('js_plugin')
{!! Html::script('js/userInterfaceTripsIntroduction/pulgin.js'.VERSION); !!}
@endsection
@section('js_file')
{!! Html::script('js/userInterfaceTripsIntroduction.js'.VERSION); !!}
@endsection
@section('script')
    <script type="text/javascript">
        $('.dropify').dropify({
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
        $.setBasicData([
            @foreach($trips as $trip)
            {
                tripId: {{$trip->trip_id}},
                topic: "{{$trip->trip_title}}",
                description: "{{ str_replace("\n","\\n" ,$trip->trip_description)}}",
                map_url: "{{$trip->map_url}}",
                map_address: "{{$trip->map_address}}",
                external_link: "{{$trip->external_link}}",
                publish : {{$trip->trip_status == 'published' ? 1 : 0}},
                @if(isset($trip->trip_media{0}))
                media : [
                @foreach($trip->trip_media as $trip_media)
                    <?php $media = $trip_media->media;$mediaUrl = $media->media_location_standard;$mediaFormat = $trip_media->media_type?>
                    @if($trip_media->media_type == 'video_url')

                    {
                        mediaType: "{{$mediaFormat}}",
                        media: "{{$mediaUrl}}",
                        mediaId: "{{$trip_media->trips_media_id}}",
                        order: "{{$trip_media->feature_order}}",
                    },
                    @elseif($trip_media->media_type == 'img')
                    {
                        mediaType: "{{$mediaFormat}}",
                        media: "{{Storage::url($mediaUrl)}}",
                        mediaId: "{{$trip_media->trips_media_id}}",
                        order: "{{$trip_media->feature_order}}",
                    },
                    @endif
                @endforeach
                ],
                @endif
            },
            @endforeach
        ]);
    </script>
@endsection
