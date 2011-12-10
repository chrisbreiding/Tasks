$(document).ready(function() {

	var startSaving = function() {
			$('#saving-settings').show()
		},
		endSaving = function() {
			$('#saving-settings').hide();
			$('#saved-message').fadeIn().delay(1000).fadeOut();
		},
		updateCategory = function(data) {
			startSaving();
			$.ajax({
				type: 'POST',
				 url: '/categories/update',
				data: data,
				success: function() {
					endSaving();
				}
			});	
		},
		updateOrder = function($catContainer) {
			$.ajax({
				type: 'POST',
				url: '/categories/sort_categories',
				data: $catContainer.sortable('serialize')
			});
		};
	
	// Click document body
	$(document.body).click(function() {
		$('.cat-editor').remove();
	});
	
	// Stop propagation when clicking inside category container
	$('.cat-settings-col').click(function(e) {
		e.stopPropagation();
	});
	
	// Layout Chooser
	$('.col-select li').click(function() {
		var $this = $(this);
		
		if( $this.hasClass('col-select-1') && $(document.body).hasClass('layout-2') ) {
			startSaving();
			$.ajax({
				type 	: 'POST',
				url		: '/users/update_layout',
				data	: {
					'layout' : 1
				},
				success : function() {
					$(document.body).removeClass('layout-2').addClass('layout-1');
					$('.in-col-2').appendTo($('.cat-settings-col-1'));
					$('.cat-settings-col-2').remove();
					endSaving();
				}
			});
		}
		
		if( $this.hasClass('col-select-2') && $(document.body).hasClass('layout-1') ) {
			startSaving();
			$.ajax({
				type 	: 'POST',
				url		: '/users/update_layout',
				data	: {
					'layout' : 2
				},
				success : function() {
					$(document.body).removeClass('layout-1').addClass('layout-2');
					$('.categories-section .content').append('<ul class="cat-settings-col cat-settings-col-2" />');
					$('.in-col-2').appendTo($('.cat-settings-col-2'));
					endSaving();
				}
			});
		}
		
	});

	// Order the categories
	$('.cat-settings-col').sortable({
		placeholder: 'ui-settings-placeholder',
		handle: '.cat-handle',
		connectWith: '.cat-settings-col',
		update: function(event, ui) {
			var $this = $(this);
			if (this === ui.item.parent()[0]) {
				updateOrder($this);
				updateCategory({
					id			: ui.item.data('id'),
					column 		: $this.data('col')
				});
			}
		}
	});

	// Add category
	$('#add-cat').click(function(e) {
		var $this = $(this),
			$newCat = $('#new-cat'),
			$catCreator = $('#cat-creator');
		e.preventDefault();
		if($catCreator.hasClass('adding-cat')) {
			if($newCat.val() !== '') {
				$.ajax({
					type 	: 'POST',
					url 	: '/categories/create',
					data 	: {
						'category' : $newCat.val()
					},
					success : function(data) {
						$(data).appendTo($('.cat-settings-col-1'));
						updateOrder($('.cat-settings-col-1'));
					}
				});
			}
			$newCat.hide().val('');
			$catCreator.removeClass('adding-cat').css('width', 0);
		} else {
			$catCreator.animate({'width': 152}).addClass('adding-cat');
			$newCat.fadeIn().focus();
		}
	});
	
	// Add category on enter
	$('#submit-cat').click(function(e) {
		e.preventDefault();
		$('#add-cat').trigger('click');
	});
	
	// Click category name to edit
	$('.cat-settings-col').delegate('.cat-name', 'click', function() {
		var $this = $(this),
			$cat = $this.parent(),
			editor = [
				'<form class="cat-editor" accept-charset="utf-8" method="post" action="/categories/update">',
					'<input type="text" class="edit-cat-name" value="' + $this.html() + '" />',
					'<input type="submit" class="save-cat-name" value="Save">',
				'</form>'
			];
		$(editor.join('')).appendTo($cat).fadeIn('fast', function() {
			$cat.find('.edit-cat-name').focus();
		});
	});
	
	// Save task
	$('.cat-settings-col').delegate('.save-cat-name', 'click', function(e) {
		var $this = $(this),
			$editor = $this.parent(),
			$cat = $editor.parent(),
			catName = $editor.children('.edit-cat-name').val();
		e.preventDefault();
		updateCategory({
			id			: $cat.data('id'),
			category 	: catName
		});
		$cat.children('.cat-name').html(catName);
		$editor.remove();
	});
	
	// Toggle display
	$('.cat-settings-col').delegate('.toggle-display', 'click', function(e) {
		e.preventDefault();
		var $cat = $(this).parent().parent();
		updateCategory({
			id		: $cat.data('id'),
			display : ($cat.hasClass('no-display') ? 1 : 0)
		});
		$cat.toggleClass('no-display');
	});
		
});