(function( $ ) {
	'use strict';

	$( function() {

		var customers = {};

		$( ".scr-date-picker" ).datepicker( {
			dateFormat: 'mm/dd/yy'
		} );

		// Query customers 
		$('#scr-submit-report').on('click', function(event) {
			event.preventDefault();

			$('#scr-no-results').hide();

			var searchDate = $('.scr-date-picker').val();
			var data = {
				action: 'scr_run_report',
				dataType: 'JSON',
				searchDate: searchDate,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-email-customers').prop('disabled', false);
			$('#scr-results').hide();
			$('#scr-email-success').hide();
			$('.scr-loading').show();

			$.ajax({
				url: ajax_object.ajax_url,
				data: data,
				success:function(data){
					$('.scr-loading').hide();
					if (data) {
						$('#scr-results').show();
						$('#customer-json').data('customers', data);
						customers = data;
						appendCustomers( data );
					} else {
						$('#scr-no-results').show();
					}
				}
			});
		
		});

		// Email customers
		$('#scr-email-customers').on('click', function(event) {
			event.preventDefault();

			var searchDate = $('.scr-date-picker').val();
			var data = {
				action: 'scr_build_email',
				customers:  customers,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-email-loader').show();

			$.ajax({
				url: ajax_object.ajax_url,
				type: "POST",
				data: data,
				// dataType: 'json',
				success:function(data){
					$('#scr-email-success').show();
					$('#scr-email-loader').hide();
					$('#scr-email-customers').prop('disabled', 'true');
				}
			});
		
		});


		function appendCustomers( data ) {
			
			$('.single-customer').remove();

			$.each(data, function(index, val) {
				var template = wp.template( 'customers' );
				$('#scr-results .card').append( 
					template( { 
						name: val.name, 
						email: val.email, 
						order_id: val.order_id, 
					} ) 
				);

			});
		}

	} );

})( jQuery );


