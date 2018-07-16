@extends('mobiles.layouts.default')
@section('content')
	<Slider></Slider>
	<cardcomponent :group_activities='@json($group_activities)'></cardcomponent>
@endsection

@section('script')
{!! Html::script('js/mVue/homePage.js'.VERSION) !!}
@endsection