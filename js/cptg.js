(function($){
	
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
	
	// auto select - cpt
	
	var input_cpt_public = $('#input_cpt_public');
	var input_cpt_exclude_from_search = $('#input_cpt_exclude_from_search');
	var input_cpt_publicly_queryable = $('#input_cpt_publicly_queryable');
	var input_cpt_show_ui = $('#input_cpt_show_ui');
	var input_cpt_show_in_nav_menus = $('#input_cpt_show_in_nav_menus');
	var input_cpt_show_in_menu = $('#input_cpt_show_in_menu');
	var input_cpt_show_in_admin_bar = $('#input_cpt_show_in_admin_bar');
	
	input_cpt_public.on( 'change', function(){
		if ( $('#input_cpt_public_check').is(':checked') ) {
			if ( $(this).val() == 1 ) opposite_public_val = 0;
			else opposite_public_val = 1;
			input_cpt_exclude_from_search.val( opposite_public_val );
			input_cpt_publicly_queryable.val( $(this).val() );
			input_cpt_show_ui.val( $(this).val() );
			input_cpt_show_in_nav_menus.val( $(this).val() );
			input_cpt_show_in_menu.val( $(this).val() );
			input_cpt_show_in_admin_bar.val( $(this).val() );
		}
	});
	
	var input_cpt_has_archive = $('#input_cpt_has_archive');
	var input_cpt_rewrite_feeds = $('#input_cpt_rewrite_feeds');
	
	input_cpt_has_archive.on( 'change', function(){
		if ( $('#input_cpt_has_archive_check').is(':checked') ) {
			input_cpt_rewrite_feeds.val( $(this).val() );
		}
	});
	
	// auto select - tax
	
	var input_tax_public = $('#input_tax_public');
	var input_tax_show_ui = $('#input_tax_show_ui');
	var input_tax_show_in_nav_menus = $('#input_tax_show_in_nav_menus');
	var input_tax_show_tagcloud = $('#input_tax_show_tagcloud');
	
	input_tax_public.on( 'change', function(){
		if ( $('#input_tax_public_check').is(':checked') ) {
			input_tax_show_ui.val( $(this).val() );
			input_tax_show_in_nav_menus.val( $(this).val() );
			input_tax_show_tagcloud.val( $(this).val() );
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
		} else if ( !$('input:checked[class=input_tax_post_types]')[0] ) {
			$('#error2').show();
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
