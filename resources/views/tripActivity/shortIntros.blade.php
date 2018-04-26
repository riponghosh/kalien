<section class="short-intro-list-section">
    <ul>
        @foreach($trip_activity['trip_activity_short_intros'] as $trip_activity_short_intro)
            <li><i class=" fa fa-dot-circle-o m-r-10" style="color: #4692e7;"></i>{{$trip_activity_short_intro['intro']}}</li>
        @endforeach
    </ul>
</section>