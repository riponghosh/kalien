@inject('CountryPresenter','App\Presenters\CountryPresenter')
<div class="grid_large">
    @foreach($group_activities as $group_activity)
        @include('groupActivity.groupActivityCard')
    @endforeach
</div>

<script>
    if ($.fn.masonry){
        $('.grid_large').masonry({
            // options...
            itemSelector: '.grid-item',
            columnWidth: 320
        });
    }
    
</script>