(function( $ ) {
	'use strict';

	$(function() {
		//alert('DOM is ready');
		$('#crawl-form').on('submit', function (e) {
			e.preventDefault();
			var data = {
				_ajax_nonce: ajax_object.ajax_nonce,
				action: 'crawl_now_ajax_hook'
			};

			$.post(ajax_object.ajax_url, data, function (data) {
				// Handle the response data here
				if(data){
					console.log(data);
					$('#admin_page_field_setting').val(data.data['my_data']);
				}
			})
		});

	});

})( jQuery );
