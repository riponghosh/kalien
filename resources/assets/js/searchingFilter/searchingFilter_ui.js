function SearchingFilterUI(callbacks){
  if (callbacks == undefined) callbacks = {};

  var $searchingFilterToolbar = $('.searchingFilter_toolbar'),
      $searchingFilterModal =  $('#Searching_filter_Modal');

  //---------------------------------------
  // toolbar 行為
  //---------------------------------------
  $searchingFilterToolbar.find('.search_filter_btn').on('click',function(e){
    e.preventDefault();
    filterModal.open($(this).attr('id'));
  });
  //--------------------------------------------------------------------------------------------------
  // Open Filter Modal
  //--------------------------------------------------------------------------------------------------
  var filterModal = new function(){
    return {
      open: function(FilterBtnId){
        switch (FilterBtnId){
          case 'gender_filter_btn':
            onFocus($searchingFilterModal.find('#gender_filter_list'));
          break;
          case 'age_filter_btn':
            onFocus($searchingFilterModal.find('#age_filter_list'));
          break;
          case 'country_filter_btn':
            onFocus($searchingFilterModal.find('#country_filter_list'));
          break;
          default:
            alert('undefined')
          break;
        }
        $searchingFilterModal.modal('show')
      },
      close: function(){
        $searchingFilterModal.modal('hide')
      },
    }
  }
  //----------------------------------
  // 關閉filter
  //----------------------------------
  $searchingFilterModal.find('.cancel-btn').click(function(e){
    e.preventDefault();
    filterModal.close();
  })
  //----------------------------------
  // Filter List Focus
  //----------------------------------
  /*在list上操作，改變底色*/
  $('.filter_item').click('*',function(){
    onFocus($(this));
  });
  //----------------------------------
  // Submit 行為
  //----------------------------------
  $('#filter-submit').click(function(){
    /*gender*/
    var genderOptions = $('input:radio[name=gender_options]:checked').val();
    /*age*/
    var maxAgeValue = $('#max-age-slider-value').text();
    var minAgeValue = $('#min-age-slider-value').text();
    /*country*/
    var country = $('input:radio[name=country_options]:checked').val();
    var region =  $('.serviceRegion').find('input:checkbox[name=region]:checked').map(function(_, el) {
      return $(el).val();
    }).get();
    var content = {
                   gender: {value: genderOptions},
                   age: {maxValue: maxAgeValue, minValue: minAgeValue},
                   servicePlace: {country: country,region: region},
    }
    callbacks.onSend(content);
  })
  function onFocus(list){
    $('.filter_item').removeClass('list_onFocus');
    list.addClass('list_onFocus');
  }
  return {
    updateToolbar: function(content){
      /*Gender*/
      ;(function(){
        if(content.gender == undefined) return;
        var gender = content.gender;
        switch (gender.value){
          case 'M':
            $('#gender_filter_btn').text('男姓');
            break;
          case 'F':
            $('#gender_filter_btn').text('女性');
            break;
          case 'both':
            $('#gender_filter_btn').text('男女皆可');
            break;
        }
      })();
      /*Age*/
      ;(function(){
        if(content.age == undefined) return;
        var age = content.age;
        $('#age_filter_btn').text(age.minValue + '-' + age.maxValue + '歲');
      })();
      /*Country*/
      ;(function(){
        if(content.servicePlace == undefined) return;
        var servicePlace = content.servicePlace;
        /*輸出到toolbar的名稱*/
        var countryName = {hk: 'HongKong',jp: 'Japan',kp: 'Korea',mo: 'Macau',tw: 'Taiwan'};
        //$('#country_filter_btn').text(countryName[servicePlace.country] + servicePlace.region);
      })();

    },
    closeModal: function(){
      filterModal.close();
    }
  }
}
