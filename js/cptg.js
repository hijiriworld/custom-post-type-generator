jQuery(function() {
	// slide toggle
	jQuery(".cptg-metabox .handlediv").click(function() {
		jQuery(".inside", jQuery(this).parent()).slideToggle();
	});
	
	// input error check
	jQuery("#cptg_cpt_form").submit(function() {
		if ( !jQuery("#post_type_name").val() ) {
			jQuery("#error").show();
			window.scrollTo(0, 0);
			return false;
		} else {
			return true;
		}
	});
	
	jQuery("#cptg_tax_form").submit(function() {
		jQuery(".error-cptg").hide();
		if ( !jQuery("#tax_name").val() ) {
			jQuery("#error1").show();
			window.scrollTo(0, 0);
			return false;
		} else if ( !jQuery("input:checked[name^=tax_post_types]")[0] ) {
			jQuery("#error2").show();
			window.scrollTo(0, 0);
			return false;
		} else {
			return true;
		}
	});
	
/*
	
	jQuery("#cptg_export_form").submit(function() {
		jQuery(".error-cptg").hide();
		if ( !jQuery('#cptg_cpt').val() ) {
			jQuery("#error1").show();
			window.scrollTo(0, 0);
			return false;
		} else {
			return true;
		}
	});
*/
	
	// sortable
	jQuery(document).ready(function() {
		jQuery("#cptg-list").sortable({
			'items': 'tr',
			'axis': 'y',
			'helper': fixHelper,
			'update' : function(e, ui) {
				jQuery.post( ajaxurl, {
					action: 'update-cptg-order',
					order: jQuery("#cptg-list").sortable("serialize"),
				});
			}
		});
		//jQuery("#the-list").disableSelection();
	});
	
	var fixHelper = function(e, ui) {
		ui.children().children().each(function() {
			jQuery(this).width(jQuery(this).width());
		});
		return ui;
	};

});