(function($){
	
	// cpt rewrite input controll
	
	var input_rewrite_rewrite = $('#input_rewrite_rewrite');
	var input_rewrite_slug = $('#input_rewrite_slug');
	if ( input_rewrite_rewrite.val() == 1 ) {
		input_rewrite_slug.attr('disabled', 'desabled');
	}
	
	input_rewrite_rewrite.on('change', function() {
		if ( $(this).val() == 1 ) {
			input_rewrite_slug.attr('disabled', 'desabled');
		} else {
			input_rewrite_slug.removeAttr('disabled');
		}
	});
	
	// slide toggle
	
	var handle = $('.cptg-metabox .handlediv');
	handle.on('click', function() {
		$('.inside', $(this).parent()).toggle();
		var postbox = $(this).parent('.postbox');
		if ( postbox.hasClass('closed') ) {
			postbox.removeClass('closed');
		} else {
			postbox.addClass('closed');
		}
	});
	
	// input error check - cpt
	
	$('#cptg_cpt_form').submit(function() {
		$('.error-cptg').hide();
		var post_type_name = $('#post_type_name').val();
		if( !post_type_name ) {
			$('#error1').show();
			window.scrollTo(0, 0);
			return false;
		} else if( post_type_name.match( /[^\x01-\x7E]/ ) ) { // 全角と半角カタカナをはじく
			$('#error2').show();
			window.scrollTo(0, 0);
			return false;
		} else {
			return true;
		}
	});
	
	// input error check - tax
	
	$('#cptg_tax_form').submit(function() {
		$('.error-cptg').hide();
		var tax_name = $('#tax_name').val();
		if ( !tax_name ) {
			$('#error1').show();
			window.scrollTo(0, 0);
			return false;
		} else if( tax_name.match( /[^\x01-\x7E]/ ) ) { // 全角と半角カタカナをはじく
			$('#error2').show();
			window.scrollTo(0, 0);
			return false;
		} else if ( !$('input:checked[name^=tax_post_types]')[0] ) {
			$('#error3').show();
			window.scrollTo(0, 0);
			return false;
		} else {
			return true;
		}
	});

	// sortable objects

	$('#cptg-list').sortable({
		'items': 'tr',
		'axis': 'y',
		'helper': fixHelper,
		'update' : function(e, ui) {
			$.post( ajaxurl, {
				action: 'update-cptg-order',
				order: $('#cptg-list').sortable('serialize'),
			});
		}
	});
	//$('#the-list').disableSelection();
	
	var fixHelper = function(e, ui) {
		ui.children().children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	
	// Export All check

	$('#cptg_export_allcheck').on('click', function(){
		var items = $('#cptg_export_objects input');
		if ( $(this).is(':checked') ) {
			$(items).prop('checked', true);
		} else {
			$(items).prop('checked', false);	
		}
	});


})(jQuery);
