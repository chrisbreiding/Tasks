$(document).ready(function() {
	
	var updateTask = function(data, $parentRow) {
			if($parentRow) {
				$parentRow.addClass('saving');
			}
			$.ajax({
				type: 'POST',
				 url: '/tasks/update',
				data: data,
				success: function() {
					if($parentRow) {
						$('<div class="saved">Saved</div>').appendTo($parentRow).fadeIn('slow', function() {
							$parentRow.removeClass('saving');
							$('.saved').delay(500).fadeOut('slow', function() {
								$('.saved').remove();
							});
						});
					}
				}
			});
		},
		twoDigits = function(num) {
			var s = '0'+num;
			return s.substring(s.length-2);
		},
		toMysql = function(date) {
		    return date.getFullYear() + "-" + twoDigits(1 + date.getMonth()) + "-" + twoDigits(date.getDate());
		},
		updateOrder = function($categoryDiv) {
			$.ajax({
				type: 'POST',
				url: '/tasks/sort_tasks',
				data: $categoryDiv.sortable('serialize')
			});
		},
		handleLink = function($editBar) {
			var $parentRow = $editBar.parent('.task-row'),
				data,
				$linkHref = $('#link-href'),
				linkHrefVal = ($linkHref.val() !== $linkHref.attr('title') && $linkHref.val()) || '',
				$linkText = $('#link-text'),
				linkTextVal = ($linkText.val() !== $linkText.attr('title') && $linkText.val()) || linkHrefVal;
				
			$('#link-editor').remove();
			
			if( linkHrefVal ) { // If there's actually something to link
				linkHrefVal = linkHrefVal.match(/^http:\/\//) ? linkHrefVal : 'http://' + linkHrefVal;
				data = {
					id 			: $parentRow.data('id'),
					link_text 	: linkTextVal,
					link_href	: linkHrefVal
				};
				updateTask(data, $parentRow);
				if(!$parentRow.hasClass('linked')) {
					$editBar.find('.linker').before('<a class="linker break-link" href="#">Remove Link</a>');
					$parentRow.addClass('linked');
				} else { 
					$editBar.find('.link').remove(); // Remove previous link
				}
				$('<a class="link" href="' + linkHrefVal + '" target="_blank">' + linkTextVal+ '</a>').appendTo($editBar);
			} else {
				$parentRow.removeClass('linked');
			}
			
			$parentRow.removeClass('editing-link').find('.task').focus();
			
		},
		cbTasks = {};
		
	cbTasks.url = window.location.href;
	cbTasks.date_seg = cbTasks.url.match(/[0-9\-]+$/);
	cbTasks.date = cbTasks.date_seg ? cbTasks.date_seg[0] : toMysql( new Date() );
	cbTasks.date_arr = cbTasks.date.split('-');
	cbTasks.year = Number(cbTasks.date_arr[0]);
	cbTasks.month = Number(cbTasks.date_arr[1]);
	cbTasks.day = Number(cbTasks.date_arr[2]);
	
	// Expand height of body for when clicking outside of tasks
	$(document.body).height($(window).height() - 20);
	
	// Give empty categories the "empty" class
	$('.category').each(function() {
		var $this = $(this);
		if($this.children('.task-row').length < 1) {
			$this.addClass('empty');
		}
	});
	
	// Click add task
	$('#create-task').click(function(e) {
		var $createTask = $('.create-task');
		
		e.preventDefault();
		
		if($createTask.hasClass('creating-task')) {
			$createTask.removeClass('creating-task');
			$('#task-creator').remove();
		} else {
			$.get('/tasks/task_creator', function(data) {
				$createTask.append(data).addClass('creating-task');
				$('#task').focus();
			});
		}
	});
	
	// Focus on link input
	$('.create-task').delegate('.creator-edit-bar input', 'focus', function() {
		var $this = $(this),
			thisVal = $this.val();
		if(thisVal === $this.attr('title')) {
			$this.val('');
		}
	});

	// Blur from link input
	$('.create-task').delegate('.creator-edit-bar input', 'blur', function() {
		var $this = $(this),
			thisVal = $this.val();
		if(thisVal === '') {
			$this.val($this.attr('title'));
			$this.removeClass('has-text');
		} else {
			$this.addClass('has-text');
		}
	});
	
	// Toggle Importance
	$('.create-task').delegate('.flagger', 'click', function(e) {
		e.preventDefault();
		$(this).parent().parent().parent().toggleClass('important');
	});
		
	// Cancel Task Creation
	$('.create-task').delegate('#cancel-task', 'click', function(e){
		e.preventDefault();
		$('.create-task').removeClass('creating-task');
		$('#task-creator').hide().remove();
	});
	
	// Create a new task
	$('.create-task').delegate('#save-task', 'click', function(e){
		var category = $('#categories').val(),
			$categoryDiv = $('#cat-' + category),
			$linkText = $('#create-link-text'),
			$linkHref = $('#create-link-href'),
			linkHrefVal = $linkHref.val(),
			linkHrefVal = linkHrefVal && (linkHrefVal !== $linkHref.attr('title')) && (linkHrefVal.match(/^http:\/\//) ? linkHrefVal : 'http://' + linkHrefVal);
			
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: '/tasks/create',
			data: {
				'task' 			: $('#task').val() || '',
				'category_id' 	: category,
				'link_text' 	: ((linkHrefVal === $linkHref.attr('title') || $linkText.val() === $linkText.attr('title')) ? '' : $linkText.val()),
				'link_href'		: (linkHrefVal ? linkHrefVal : ''),
				'important'		: ($('#task-creator').hasClass('important') ? 1 : 0)
			},
			success: function(data) {
				$('.create-task').removeClass('creating-task');
				$('#task-creator').hide().remove();
				$(data).appendTo($categoryDiv).hide().fadeIn('fast', function() {
					updateOrder($categoryDiv);
					$('.category').sortable('refresh');
				});
			}
		});
	});
	
	// When clicking outside tasklist
	$(document.body).click(function() {
		$('.date-changer').hide();									// Hide date changers
		$('.link:hidden').show();									// Reveal any hidden links
		
		if($('#link-editor').length) {								// Handle an open link editor
			var $editBar = $('#link-editor').parent()
			handleLink($editBar);
		}

		$('.edit-bar:visible').siblings('.task').focus().blur();	// Hide any visible edit bars
		$('.task-row').removeClass('edit-bar-open');				// Remove class from task rows			
	});
	
	// Stop clicks inside category divs from bubbling up
	$('.category').click(function(e) {
		e.stopPropagation();
	});
	
	/**** All handlers below here delegated in case they're used after a new task is created ****/
	
	// Focus on task
	$('.category').delegate('.task', 'focus', function(e){
		$('.edit-bar-open').removeClass('edit-bar-open editing-link');
		$('#link-editor').remove();
		$(this).parent().addClass('edit-bar-open');
	});
		
	// Check or uncheck completion
	$('.category').delegate('.check', 'click', function(e){
		var $this = $(this),
			$parentRow = $this.parent('.completed').parent('.task-row'),
			isNowChecked = $this.hasClass('checked'),
			data = {
				id 				: $parentRow.data('id'),
				completed 		: (isNowChecked ? 0 : 1),
				date_completed 	: (isNowChecked ? 'NULL' : toMysql(new Date())),
				task 			: $parentRow.find('.task').val(),
				important		: 0
			};

		e.preventDefault();
		updateTask(data, $parentRow);
		$parentRow.fadeOut('slow', function(){
			$parentRow.remove();
		});
	});
	
	// Update on change
	$('.category').delegate('.task', 'change', function(e){
		var $this = $(this);

		e.preventDefault();
		updateTask({
			id 		: $this.parent('.task-row').data('id'),
			task 	: $this.val()
		}, $this.parent());
	});
	
	// Update on enter
	$('.category').delegate('.save-task', 'click', function(e) {
		var $parentRow = $(this).parent();

		e.preventDefault();
		updateTask({
			id 		: $parentRow.data('id'),
			task 	: $parentRow.find('.task').val()
		}, $parentRow);
	});

	// Flag or unflag as important
	$('.category').delegate('.flagger', 'click', function(e) {
		var $parentRow = $(this).parent().parent();
		
		e.preventDefault();
		$parentRow.toggleClass('important');
		updateTask({
			id 			: $parentRow.data('id'),
			important 	: $parentRow.hasClass('important') ? 1 : 0
		}, $parentRow);
	});
	
	// Click delete circle -> bring up confirm delete button
	$('.category').delegate('.delete', 'click', function(e) {
		e.preventDefault();
		$('.confirm-delete').hide();
		$('.delete').show();
		$(this).hide().siblings('.confirm-delete').show();
	});
	
	// Cancel delete by focusing on task input
	$('.category').delegate('.task', 'focus', function() {
		$('.confirm-delete').hide();
		$('.delete').show();
	});
	
	// Confirm delete
	$('.category').delegate('.confirm-delete', 'click', function(e) {
		var $this = $(this),
			url = $this.attr('href'),
			$parentRow = $this.parent().parent('.task-row');

		e.preventDefault();

		$.ajax({
			url: url,
			success: function() {
				$parentRow.fadeOut('slow', function() {
					$parentRow.remove();
				});
			}
		});
	});
	
	// Click add link
	$('.category').delegate('.add-link', 'click', function(e) {
		var $editBar = $(this).parent(),
			$parentRow = $editBar.parent('.task-row'),
			$thisLink,
			thisLinkText = 'Label',
			thisLinkHref = 'URL',
			editorClass = '',
			editor;
		
		e.preventDefault();
		
		if($parentRow.hasClass('editing-link')) { // Link is being edited, save and display it
		
			handleLink($editBar);
			
		} else { // Bring up editor
			
			$('#link-editor').remove();
			
			if($parentRow.hasClass('linked')) { // Link is present, populate editor values
				$thisLink = $editBar.find('.link').hide();
				thisLinkText = $thisLink.text();
				thisLinkHref = $thisLink.attr('href');
				editorClass = 'has-text';
			} 

			editor = [
					'<form id="link-editor" accept-charset="utf-8" method="post" action="http://chrisbreiding.com/tasks/update">',
						'<input type="text" id="link-text" class="' + editorClass + '" value="' + thisLinkText + '" title="Label" />',
						'<input type="text" id="link-href" class="' + editorClass + '" value="' + thisLinkHref + '" title="URL" />',
						'<input type="submit" id="save-link" value="Save Link">',
					'</form>'
			];
			$(editor.join('')).appendTo($editBar);
			$parentRow.addClass('editing-link');
			
		}
		
	});
	
	// Submit link editor on enter		
	$('.category').delegate('#save-link', 'click', function(e) {
		var $editBar = $(this).parent().parent();
		e.preventDefault();
		handleLink($editBar);
	});

	// Remove link
	$('.category').delegate('.break-link', 'click', function(e) {
		var $this = $(this),
			$editBar = $this.parent(),
			$parentRow = $editBar.parent('.task-row');
			
		e.preventDefault();
		updateTask({
			id 			: $parentRow.data('id'),
			link_text 	: '',
			link_href	: ''
		}, $parentRow);
		$('#link-editor').remove();
		$editBar.find('.link').remove();
		$this.remove();
		$parentRow.removeClass('linked editing-link').find('.task').focus();
	});
				
	// Focus on link editor input
	$('.category').delegate('#link-editor input', 'focus', function() {
		var $this = $(this),
			thisVal = $this.val();
		if(thisVal === $this.attr('title')) {
			$this.val('');
		}
	});
	
	// Blur from link editor input
	$('.category').delegate('#link-editor input', 'blur', function() {
		var $this = $(this),
			thisVal = $this.val();
		if(thisVal === '') {
			$this.val($this.attr('title'));
			$this.removeClass('has-text');
		} else {
			$this.addClass('has-text');
		}
	});
				
	// Order the tasks
	$('.category').sortable({
		placeholder: 'ui-placeholder',
		handle: '.handle',
		connectWith: '.category',
		remove: function(event, ui) {
			var $this = $(this);
			if($this.children('.task-row').length < 1) {
				$this.addClass('empty');
			}
		},
		update: function(event, ui) {
			var $this = $(this);
			if (this === ui.item.parent()[0]) {
				updateOrder($this);
				updateTask({
					id			: ui.item.data('id'),
					task 		: ui.item.find('.task').val(),
					category_id : $this.data('cat-id')
				}, ui.item);
				$this.removeClass('empty');
			}
		},
		stop: function(event, ui) {
			ui.item.find('.task').focus(); // Re-focus the input
		}
	});

	// Top date picker
	$('#date-input').datepicker({
		showOn: 		'button',
	    buttonText: 	'Pick Date',
	    buttonImageOnly: true, 
	    buttonImage: 	'/tasks/ui/date-picker.png',
		defaultDate: 	cbTasks.date,
		gotoCurrent: 	true,
		maxDate: 		0,
		nextText:		'Next Month',
		prevText:		'Previous Month',
		dateFormat: 	'yy-mm-dd',
		onSelect: 		function(dateText, inst) { 
			window.location = '/tasks/completed/' + dateText;
		}
	});
	
	$('#ui-datepicker-div').appendTo($('#date-pick'));
		
	// Completed task date picker
	$('.date-changer').datepicker({
		defaultDate: 	cbTasks.date,
		gotoCurrent: 	true,
		maxDate: 		0,
		nextText:		'Next Month',
		prevText:		'Previous Month',
		dateFormat: 	'yy-mm-dd',
		onSelect: 		function(dateText, inst) { 
			var $parentRow = $(this).parent().parent().parent();

			if( dateText !== cbTasks.date ) {
				updateTask({
					id				: $parentRow.data('id'),
					date_completed 	: dateText
				});
				$parentRow.fadeOut('slow', function() {
					$parentRow.remove();
				});

			}
		}
	});

	$('.date-change > a').click(function(e) {
		var $this = $(this),
			$parentRow = $this.parent().parent().parent();
		e.preventDefault();
		e.stopPropagation();
		$('.task-row').css('z-index', 10);
		$parentRow.css('z-index', 50);
		$this.siblings('.date-changer').fadeIn();
	});

});
