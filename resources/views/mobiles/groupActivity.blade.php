@extends('mobiles.layouts.default')
@section('content')
	<gcompomnent
		:group-activity='@json($group_activity)'
		:refund-forbidden-when-gp-achieved={{($trip_activity['merchant_id'] == env('MERCHANT_ID_PNEKO')) ? 'true' : 'false'}}
		:product='@json($trip_activity)'
		:organiser='@json($group_activity['host'])'
	></gcompomnent>
	<Bottombar gp-activity-id={{$group_activity['gp_activity_id']}}></Bottombar>
@endsection

@section('script')
	{!! Html::script('js/mVue/groupActivity.js'.VERSION) !!}
@endsection
