$(document).ready(function () {
	
	var util = {
	
			updateTask : function (data, $parentRow) {
				if ($parentRow) {
					$parentRow.addClass('saving');
				}
				$.ajax({
					type: 'POST',
					 url: '/tasks/update',
					data: data,
					success: function ( data, textStatus, jqXHR ) {
						
						if( jqXHR.status == 302 ) {
							window.location.reload();
						}
						
						if ($parentRow) {
							$('<div class="saved">Saved</div>').appendTo($parentRow).fadeIn('slow', function () {
								$parentRow.removeClass('saving');
								$('.saved').delay(500).fadeOut('slow', function () {
									$('.saved').remove();
								});
							});
						}
						
					}
				});
			},
			
			pad : function (num) {
				var paddedNum = '0' + num;
				return paddedNum.substring(paddedNum.length - 2);
			},
			
			toMysql : function (date) {
			    return date.getFullYear() + "-" + util.pad(1 + date.getMonth()) + "-" + util.pad(date.getDate());
			},
			
			updateOrder : function ($categoryDiv) {
			
				$.ajax({
					type: 'POST',
					url: '/tasks/sort_tasks',
					data: $categoryDiv.sortable('serialize')
				});
				
			},
			
			handleLink : function ($editBar) {
			
				var $parentRow = $editBar.parent('.task-row'),
					data,
					$linkHref = $('#link-href'),
					linkHrefVal = ($linkHref.val() !== $linkHref.attr('title') && $linkHref.val()) || '',
					$linkText = $('#link-text'),
					linkTextVal = ($linkText.val() !== $linkText.attr('title') && $linkText.val()) || linkHrefVal;
					
				$('#link-editor').remove();
				
				if ( linkHrefVal ) { // If there's actually something to link
					linkHrefVal = linkHrefVal.match(/^https?:\/\//) ? linkHrefVal : 'http://' + linkHrefVal;
					data = {
						id 			: $parentRow.data('id'),
						link_text 	: linkTextVal,
						link_href	: linkHrefVal
					};
					util.updateTask(data, $parentRow);
					if (!$parentRow.hasClass('linked')) {
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
			
			dateInfo : function () {
			
				this.url = window.location.href;
				this.date_seg = this.url.match(/[0-9\-]+$/);
				this.date = this.date_seg ? this.date_seg[0] : util.toMysql( new Date() );
				this.date_arr = this.date.split('-');
				this.year = Number(this.date_arr[0]);
				this.month = Number(this.date_arr[1]);
				this.day = Number(this.date_arr[2]);
		
			}
		
		},
		
		// Initial actions
		load = (function () {
			
			function resizeBodyHeight () {
				$(document.body).height($(window).height());
			}
			
			function documentClick () {
				$('.date-changer').hide();	// Hide date changers
				$('.link:hidden').show();	// Reveal any hidden links
				
				if ($('#link-editor').length) {	// If there's an open link editor
					var $editBar = $('#link-editor').parent()
					util.handleLink($editBar);
				}
		
				$('.edit-bar:visible').siblings('.task').focus().blur();	// Hide any visible edit bars
				$('.task-row').removeClass('edit-bar-open');				// Remove class from task rows			
			}
			
			function categoryClick (e) {
				e.stopPropagation();
			}
					
			return {
			
				init : function () {
				
					// Expand height of body for when clicking outside of tasks
					resizeBodyHeight();
					$(window).resize(this.resizeBodyHeight);
					
					// Give empty categories the "empty" class
					$('.category').each(function () {
						var $this = $(this);
						if ($this.children('.task-row').length < 1) {
							$this.addClass('empty');
						}
					});
		
					// When clicking outside tasklist
					$(document.body).click(documentClick);
		
					// Stop clicks inside category divs from bubbling up
					$('.category').click(categoryClick);
								
				}
			
			};
			
		}()),
		
		// Top date picker
		datePicker = (function () {
		
			var dateConfig = {
			
				showOn 			: 'button',
			    buttonText 		: 'Pick Date',
			    buttonImageOnly : true, 
			    buttonImage 	: '/ui/date-picker.png',
				dateFormat 		: 'yy-mm-dd',
				defaultDate 	: util.dateInfo.date,
				gotoCurrent 	: true,
				maxDate 		: 0,
				nextText 		: 'Next Month',
				prevText 		: 'Previous Month',
				onSelect 		: function (dateText, inst) { 
									window.location = '/tasks/completed/' + dateText;
								}
			};
		
		
			return {
			
				init : function () {
				
					$('#date-input').datepicker( dateConfig );
					
					$('#ui-datepicker-div').appendTo($('#date-pick'));
					
				}
			
			};
			
		}()),
	
		// Create task
		createTask = (function () {
		
			function createTask (e) {
			
				var $createTask = $('.create-task');
				
				e.preventDefault();
				
				if ($createTask.hasClass('creating-task')) {
					$createTask.removeClass('creating-task');
					$('#task-creator').remove();
				} else {
					$.get('/tasks/task_creator', function (data, textStatus, jqXHR) {
						
						if( jqXHR.status == 302 ) {
							window.location.reload();
						}
							
						$createTask.append(data).addClass('creating-task');
						$('#task').focus();
	
					});
				}
			
			}
			
			function linkFocus (e) {
	
				var $this = $(this),
					thisVal = $this.val();
				if (thisVal === $this.attr('title')) {
					$this.val('');
				}
	
			}
			
			function linkBlur (e) {
			
				var $this = $(this),
					thisVal = $this.val();
					
				if (thisVal === '') {
				
					$this.val($this.attr('title'));
					$this.removeClass('has-text');
					
				} else {
				
					$this.addClass('has-text');
					
				}
	
			}
			
			function toggleImportance (e) {
			
				e.preventDefault();
				$(this).parent().parent().parent().toggleClass('important');
	
			}
			
			function cancel (e) {
			
				e.preventDefault();
				$('.create-task').removeClass('creating-task');
				$('#task-creator').hide().remove();
	
			}
			
			function save (e) {
			
				var category = $('#categories').val(),
					$categoryDiv = $('#cat-' + category),
					$linkText = $('#create-link-text'),
					$linkHref = $('#create-link-href'),
					linkHrefVal = $linkHref.val(),
					linkHrefVal = linkHrefVal && (linkHrefVal !== $linkHref.attr('title')) && (linkHrefVal.match(/^https?:\/\//) ? linkHrefVal : 'http://' + linkHrefVal),
					important = ($('#task-creator').hasClass('important') ? 1 : 0),
					newTaskCallback = function () {
						$categoryDiv.removeClass('empty');
						util.updateOrder($categoryDiv);
						$('.category').sortable('refresh');
					};
					
				e.preventDefault();
				$.ajax({
					type: 'POST',
					url: '/tasks/create',
					data: {
						'task' 			: $('#task').val() || '',
						'category_id' 	: category,
						'link_text' 	: ((linkHrefVal === $linkHref.attr('title') || $linkText.val() === $linkText.attr('title')) ? '' : $linkText.val()),
						'link_href'		: (linkHrefVal ? linkHrefVal : ''),
						'important'		: important
					},
					success: function (data, textStatus, jqXHR) {
	
						if( jqXHR.status == 302 ) {
							window.location.reload();
						}
							
						$('.create-task').removeClass('creating-task');
						$('#task-creator').hide().remove();
						
						if (important) { // If important, insert it as the first task
							$(data).insertAfter( $categoryDiv.find('h2') ).hide().fadeIn('fast', newTaskCallback);
						} else { // Otherwise, put it at the end
							$(data).appendTo($categoryDiv).hide().fadeIn('fast', newTaskCallback);
						}
					}
				});
	
			}
			
			function saveOnEnter (e) {
			
				e.preventDefault();
				$('#save-task').trigger('click');
			
			}
			
			return {
			
				init : function () {
				
					// Click add task
					$('#create-task').click(createTask);
				
					// Focus on link input
					$('.create-task').delegate( '.creator-edit-bar input', 	'focus', 	linkFocus );
				
					// Blur from link input
					$('.create-task').delegate( '.creator-edit-bar input', 	'blur', 	linkBlur );
			
					// Toggle Importance
					$('.create-task').delegate( '.flagger', 				'click', 	toggleImportance );
				
					// Cancel Task Creation
					$('.create-task').delegate('#cancel-task', 				'click', 	cancel );
					
					// Create a new task
					$('.create-task').delegate('#save-task', 				'click', 	save );
				
					// Add task on enter
					$('.create-task').delegate('#submit-task', 				'click', 	saveOnEnter);
						
				}
				
			};
				
		}()),
	
		// Task actions
		tasks = (function () {
		
			var taskOrderConfig = {
			
					placeholder : 'ui-placeholder',
					handle 		: '.handle',
					connectWith : '.category',
					remove 		: function (event, ui) {
									var $this = $(this);
									if ($this.children('.task-row').length < 1) {
										$this.addClass('empty');
									}
								},
					update 		: function (event, ui) {
									var $this = $(this);
									if (this === ui.item.parent()[0]) {
										util.updateOrder($this);
										util.updateTask({
											id			: ui.item.data('id'),
											task 		: ui.item.find('.task').val(),
											category_id : $this.data('cat-id')
										}, ui.item);
										$this.removeClass('empty');
									}
								},
					stop 		: function (event, ui) {
									ui.item.find('.task').focus(); // Re-focus the input
								}
								
				},
				
				dateConfig = {
				
					dateFormat 	: 'yy-mm-dd',
					defaultDate : util.dateInfo.date,
					gotoCurrent : true,
					maxDate 	: 0,
					nextText 	: 'Next Month',
					prevText 	: 'Previous Month',
					onSelect 	: function (dateText, inst) { 
									var $parentRow = $(this).parent().parent().parent();
						
									if ( dateText !== util.dateInfo.date ) {
										util.updateTask({
											id				: $parentRow.data('id'),
											date_completed 	: dateText
										});
										$parentRow.fadeOut('slow', function () {
											$parentRow.remove();
										});
						
									}
								}
								
				};
			
			function taskFocus (e) {
			
				$('.edit-bar-open').removeClass('edit-bar-open editing-link');
				$('#link-editor').remove();
				$(this).parent().addClass('edit-bar-open');
				
			}
			
			function toggleCompletion (e) {
				
				var $this = $(this),
					$parentRow = $this.parent('.completed').parent('.task-row'),
					isNowChecked = $this.hasClass('checked'),
					data = {
						id 				: $parentRow.data('id'),
						completed 		: (isNowChecked ? 0 : 1),
						date_completed 	: (isNowChecked ? 'NULL' : util.toMysql(new Date())),
						task 			: $parentRow.find('.task').val(),
						important		: 0
					};
		
				e.preventDefault();
				util.updateTask(data, $parentRow);
				$parentRow.fadeOut('slow', function (){
					$parentRow.remove();
				});
				
			}
			
			function updateOnChange (e) {
			
				var $this = $(this);
		
				e.preventDefault();
				
				util.updateTask({
					id 		: $this.parent('.task-row').data('id'),
					task 	: $this.val()
				}, $this.parent());
				
			}
			
			function updateOnEnter (e) {
			
				var $parentRow = $(this).parent();
		
				e.preventDefault();
				
				util.updateTask({
					id 		: $parentRow.data('id'),
					task 	: $parentRow.find('.task').val()
				}, $parentRow);
			
			}
			
			function toggleImportance (e) {
			
				var $parentRow = $(this).parent().parent();
				
				e.preventDefault();
				$parentRow.toggleClass('important');
				util.updateTask({
					id 			: $parentRow.data('id'),
					important 	: $parentRow.hasClass('important') ? 1 : 0
				}, $parentRow);
			
			}
			
			function deleteFirstClick (e) {
			
				e.preventDefault();
				$('.confirm-delete').hide();
				$('.delete').show();
				$(this).hide().siblings('.confirm-delete').show();
			
			}
			
			function deleteSecondClick (e) {
			
				var $this = $(this),
					url = $this.attr('href'),
					$parentRow = $this.parent().parent('.task-row');
		
				e.preventDefault();
				
				if ($parentRow.siblings('.task-row').length === 0) {
					$parentRow.parent().addClass('empty');
				}
		
				$.ajax({
					url: url,
					success: function (data, textStatus, jqXHR) {
	
						if( jqXHR.status == 302 ) {
							window.location.reload();
						}
							
						$parentRow.fadeOut('slow', function () {
							$parentRow.remove();
						});
						
					}
				});
			
			}
			
			function cancelDelete () {
			
				$('.confirm-delete').hide();
				$('.delete').show();
				
			}
			
			function addLink (e) {
			
				var $editBar = $(this).parent(),
					$parentRow = $editBar.parent('.task-row'),
					$thisLink,
					thisLinkText = 'Label',
					thisLinkHref = 'URL',
					editorClass = '',
					editor;
				
				e.preventDefault();
				
				if ($parentRow.hasClass('editing-link')) { // Link is being edited, save and display it
				
					util.handleLink($editBar);
					
				} else { // Bring up editor
					
					$('#link-editor').remove();
					
					if ($parentRow.hasClass('linked')) { // Link is present, populate editor values
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
				
			}
			
			function linkFocus () {
			
				var $this = $(this),
					thisVal = $this.val();
				if (thisVal === $this.attr('title')) {
					$this.val('');
				}
			
			}
			
			function linkBlur () {
			
				var $this = $(this),
					thisVal = $this.val();
					
				if (thisVal === '') {
					
					$this.val($this.attr('title'));
					$this.removeClass('has-text');
					
				} else {
				
					$this.addClass('has-text');
				
				}
			
			}
			
			function saveLinkOnEnter (e) {
			
				var $editBar = $(this).parent().parent();
				e.preventDefault();
				util.handleLink($editBar);
		
			}
			
			function removeLink (e) {
			
				var $this = $(this),
					$editBar = $this.parent(),
					$parentRow = $editBar.parent('.task-row');
					
				e.preventDefault();
				util.updateTask({
					id 			: $parentRow.data('id'),
					link_text 	: '',
					link_href	: ''
				}, $parentRow);
				$('#link-editor').remove();
				$editBar.find('.link').remove();
				$this.remove();
				$parentRow.removeClass('linked editing-link').find('.task').focus();
				
			}
			
			function changeDate (e) {
			
				var $this = $(this),
					$parentRow = $this.parent().parent().parent();
				e.preventDefault();
				e.stopPropagation();
				$('.task-row').css('z-index', 10);
				$parentRow.css('z-index', 50);
				$this.siblings('.date-changer').fadeIn();
			
			}
		
			return {
			
				init : function () {
					
					// Focus on task
					$('.category').delegate( '.task', 				'focus', 	taskFocus );
							
					// Check or uncheck completion
					$('.category').delegate( '.check', 				'click', 	toggleCompletion );
					
					// Update on change
					$('.category').delegate( '.task', 				'change', 	updateOnChange );
					
					// Update on enter
					$('.category').delegate( '.save-task', 			'click', 	updateOnEnter );
				
					// Toggle importance
					$('.category').delegate( '.flagger', 			'click', 	toggleImportance );
					
					// Click delete circle -> bring up confirm delete button
					$('.category').delegate( '.delete', 			'click', 	deleteFirstClick );
					
					// Confirm delete
					$('.category').delegate( '.confirm-delete', 	'click', 	deleteSecondClick );
					
					// Cancel delete by focusing on task input
					$('.category').delegate( '.task', 				'focus', 	cancelDelete );
					
					// Click add link
					$('.category').delegate( '.add-link', 			'click', 	addLink );
					
					// Focus on link editor input
					$('.category').delegate( '#link-editor input', 	'focus', 	linkFocus );
					
					// Blur from link editor input
					$('.category').delegate( '#link-editor input', 	'blur', 	linkBlur );
								
					// Submit link editor on enter		
					$('.category').delegate( '#save-link', 			'click', 	saveLinkOnEnter );
				
					// Remove link
					$('.category').delegate( '.break-link', 		'click', 	removeLink );
						
					// Order the tasks
					$('.category').sortable( taskOrderConfig );
		
					// Completed task date picker
					$('.date-changer').datepicker( dateConfig );
					$('.date-change > a').click( changeDate );
		
				}
				
			};
				
		}()),
		
		bootstrap = function () {
			
			load.init();
			
			datePicker.init();
			
			createTask.init();
			
			tasks.init();
			
		};
	
	// Let her rip!
	bootstrap();
	
});