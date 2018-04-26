<section class="master-rule-info-section">
    <ul class="list-inline">
        @foreach($trip_activity['rule_infos'] as $trip_activity_rule)
            <li class="m-b-15" style="margin-right: 80px;">
                <i class="fa fa-{{$TripActivityPresenter->activity_rule_icon($trip_activity_rule['rule_type']['info'])}} fa-lg m-r-10"></i>{{$TripActivityPresenter->activity_rule($trip_activity_rule['rule_type']['info'],$trip_activity_rule['info_value'])}}
            </li>
        @endforeach
    </ul>
</section>