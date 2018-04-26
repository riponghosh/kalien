@inject('CountryPresenter','App\Presenters\CountryPresenter')
<h4>
	Tour Guide
</h4>
		@if (count($searchResult) > 0)
		@foreach ($searchResult as $searchResults)
		<div class="guideGrid">
			<div class="userIcon">
			<a href="GET/userProfile/modal/{{$searchResults->id}}" class="open-modal showGuideProfile" >
			@if (file_exists(public_path('/img/userIcon/'.$searchResults->id.'.png')))
       			 <img src="img/userIcon/{{$searchResults->id}}.png"/>
      		@else
			 <img src="/img/icon/user_icon_bg.png"/>
			@endif
			</a>
			</div>
			<div class="nameContainer">
				{{ $searchResults->name }}
			</div>
			<div class="addressContainer">				
				{{ $searchResults->living_address }}/{{ $CountryPresenter->iso_convertTo_name($searchResults->country) }}			
			</div>
		</div>
		@endforeach
		@endif
