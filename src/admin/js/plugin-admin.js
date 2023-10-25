(function( $ ) {
	'use strict';

	$(function() {
		//alert('DOM is ready');
		$('#crawl-form').on('submit', function (e) {
			e.preventDefault();
			var data = {
				action: 'crawl_now',
			};
			$.post(ajax_object.ajax_url, data, function (response) {
				// Handle the response data here
				$('#admin_page_field_setting').val(response);
			});
		});

	});

})( jQuery );
