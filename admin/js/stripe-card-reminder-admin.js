(function( $ ) {
	'use strict';

	$( function() {

		var allSubscriptions;
		var customerIndex = 0;

		// show customers about to email
		$('#scr-results-text').on('click', function(event) {
			event.preventDefault();
			if ($(this).data( 'state' ) == 'all') {

				$(this).text( 'Show which customers you\'re about to email' );
				$(this).data( 'state', 'results' );

			} else {

				$(this).text( 'Show all results' );
				$(this).data( 'state', 'all' );

			}
			$('.single-customer.false').toggle();
		});

		$( ".scr-date-picker" ).datepicker( {
			dateFormat: 'mm/dd/yy'
		} );

		// Query customers 
		$('#scr-submit-report').on('click', function(event) {
			event.preventDefault();
			var searchDate = $('.scr-date-picker').val();

			if( !checkDate( searchDate ) )
				return false;

			allSubscriptions = false;
			customerIndex = 0;

			$('.single-customer').remove();
			$('#scr-no-results').hide();
			$('#scr-email-customers').hide();
			$('#scr-results-text').hide();

			var data = {
				action: 'scr_count_subscriptions',
				dataType: 'JSON',
				searchDate: searchDate,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-results .ex-by').text( searchDate );
			$('#scr-email-customers').prop('disabled', false);
			$('#scr-email-success').hide();
			$('.scr-loading').css('opacity', 1);

			$.ajax({
				url: ajax_object.ajax_url,
				data: data,
				success:function(data){

					$('#sub-count .numb-sub').text( countSubscriptions( data ));
					$('#sub-count').show();
					$('#scr-results').show();
					checkCustomers( allSubscriptions );

				}
			});
		
		});

		// Email customers
		$('#scr-email-customers').on('click', function(event) {
			event.preventDefault();

			var emails = [];
			$.each($('.single-customer.true .customer-email'), function(index, val) {
				emails[index] = $(val).text();
			});

			var data = {
				action: 'scr_build_email',
				customers:  emails,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-email-loader').show();

			$.ajax({
				url: ajax_object.ajax_url,
				type: "POST",
				data: data,
				success:function(data){
					$('#scr-email-success').show();
					$('#scr-email-loader').hide();
					$('#scr-email-customers').prop('disabled', 'true');
				}
			});
		
		});

		function checkCustomers( allSubs ) {

			var searchDate = $('.scr-date-picker').val();
			var data = {
				action: 'scr_check_customer',
				customer:  allSubs[customerIndex],
				searchDate:  searchDate,
				nonce: ajax_object.ajax_nonce 
			};

			$.ajax({
				url: ajax_object.ajax_url,
				type: "POST",
				data: data,
				success:function(data){
					console.log(data);
					var checked = customerIndex + 1;
					$('#sub-count .numb-checked').text( checked );
					appendCustomer( data );
					customerIndex++;
					if (customerIndex < allSubscriptions.length) {
						checkCustomers(allSubscriptions);
					} else{
						$('.scr-loading').css('opacity', 0);
						$('#scr-email-customers').show();
						$('#scr-results-text').show();
					}
				}
			});
		}

		function appendCustomer( data ) {
			
			var template = wp.template( 'customers' );
			$('#scr-results .card').append( 
				template( { 
					name: data.customer_meta.name, 
					email: data.customer_meta.email, 
					order_id: data.customer_meta.order_id, 
					is_expires: data.is_expire,
					error: data.customer_meta.error
				} ) 
			);

		}

		function countSubscriptions( data ) {
			var count = 0;
			var subscriptions = [];

			$.each(data, function(index, val) {
				$.each(val, function(index, sub) {
					subscriptions.push( sub );
				});
			});

			allSubscriptions = subscriptions;
			return subscriptions.length;
		}

		function checkDate(val){
		  // regular expression to match required date format
		  var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

		  if(val != '' && !val.match(re)) {
		    alert("Invalid date format: " + val);
		    return false;
		  } else if ( val.length == 0 ){
		  	alert("Please enter a date :)");
		  	return false;
		  }

		  return true;
		}

	} );

})( jQuery );