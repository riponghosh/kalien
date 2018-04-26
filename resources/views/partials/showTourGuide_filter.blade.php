<form id="filter-form">
	{{ csrf_field() }}
	<div class="filter-header">
			Filter
	</div>
	<section>
		<div class="filter-subtitle">
			Sex
		</div>
			<input type="checkbox" class="flt-btn" name="sex[]" value="1" id="flt-sex1"/>
			<label for="flt-sex1" id="label-flt-sex1" class="label-btn">
				<img src="/img/icon/maleTransparent_icon.png" width="100%"/>
			</label>
			<input type="checkbox" class="flt-btn" name="sex[]" value="2" id="flt-sex2"/>
			<label for="flt-sex2" id="label-flt-sex2" class="label-btn">
				<img src="/img/icon/femaleTransparent_icon.png" width="100%"/>
			</label>
			<input type="checkbox" class="flt-btn" id="flt-sex0"/>
			<label for="flt-sex0" ></label>

	</section>
	<section>
		<div class="filter-subtitle">
			Transport
		</div>
			<input type="checkbox" class="flt-btn" name="transport[]" value="1" id="flt-transport1"/>
			<label for="flt-transport1" id="label-flt-transport1" src="/img/motorbikeTransparent_icon.png" class="label-btn">
				<img src="/img/icon/motorbikeTransparent_icon.png" width="100%"/>
			</label>
			<input type="checkbox" class="flt-btn" name="transport[]" value="2" id="flt-transport2"/>
			<label for="flt-transport2" id="label-flt-transport2" class="label-btn">
				<img src="/img/icon/carTransparent_icon.png" width="100%"/>
			</label>
			<input type="checkbox" class="flt-btn" id="flt-transport0"/>
			<label for="flt-transport0" id="label-flt-transport0"></label>
	</section>

</form>
<script>
	var ageSlider = document.getElementById('slider'),
		maxAgeSliderValue = document.getElementById('max-age-slider-value');
	minAgeSliderValue = document.getElementById('min-age-slider-value');

	searchingFilter();
	noUiSlider.create(slider, {
		start: [15, 70],
		connect: true,
		margin: 5,
		step: 5,
		range: {
			'min': 15,
			'max': 70
		},
		pips: {
			mode: 'values',
			values: [15,20,25,30,40,50,60,70],
		}
	});
	ageSlider.noUiSlider.on('update', function( values, handle ) {
		var ageSliderValue = handle ? maxAgeSliderValue : minAgeSliderValue
		ageSliderValue.innerHTML = parseInt(values[handle]);
	});
	var config = {
		'.chosen-select'           : {max_selected_options : 5,width : "100%"},
		'.chosen-select-deselect'  : {allow_single_deselect:true,width : "100%"},
		'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>