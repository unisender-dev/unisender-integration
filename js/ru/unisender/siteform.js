jQuery(document).ready(function() {
	jQuery('.us-subscribeform').on('submit', function() {
		var datas = jQuery('.us-subscribeform').serialize();
		jQuery.ajax({
			type: "POST",
			url: self.location,
			data: datas + '&unisender_subscribe=subscribe',
			dataType: "json"
		}).done(
			function (data) {
				if ( data.status === 'success' ) {
					self.location = data.message;
				} else {
					alert(data.message);
				}
			}
		);
		return false;
	});
});


