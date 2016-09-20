(function( $ ) {
	'use strict';

	$( function() {

		var customers = {};

		$( ".scr-date-picker" ).datepicker();

		// Query customers 
		$('#scr-submit-report').on('click', function(event) {
			event.preventDefault();

			var searchDate = $('.scr-date-picker').val();
			var data = {
				action: 'scr_run_report',
				dataType: 'JSON',
				searchDate: searchDate,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-results').css('opacity', 0);
			$('.scr-loading').show();

			$.ajax({
				url: ajax_object.ajax_url,
				data: data,
				success:function(data){
					$('.scr-loading').hide();
					$('#scr-results').fadeTo('fast', 1);
					$('#customer-json').data('customers', data);
					customers = data;
					appendCustomers( data );
				}
			});
		
		});

		// Email customers
		$('#scr-email-customers').on('click', function(event) {
			event.preventDefault();

			var searchDate = $('.scr-date-picker').val();
			var data = {
				action: 'scr_send_email',
				customers:  customers,
				nonce: ajax_object.ajax_nonce 
			};

			$('#scr-email-loader').show();

			$.ajax({
				url: ajax_object.ajax_url,
				action: 'scr_send_email',
				type: "POST",
				data: data,
				// dataType: 'json',
				success:function(data){
					$('#scr-email-success').css('opacity', 1);
					$('#scr-email-loader').hide();
					console.log(data);
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


