@extends('mobiles.layouts.default')
@section('content')
	<Tab :user_activity_tickets='@json($user_activity_tickets)'></Tab>
@endsection

@section('script')
{!! Html::script('js/mVue/ticket.js'.VERSION) !!}
@endsection
