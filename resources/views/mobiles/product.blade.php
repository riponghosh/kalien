@extends('mobiles.layouts.default')
@section('content')
	<product :product='@json($trip_activity)' :trip_activity_tickets='@json($trip_activity_tickets)'></product>
@endsection

@section('script')
{!! Html::script('js/mVue/product.js'.VERSION) !!}
@endsection