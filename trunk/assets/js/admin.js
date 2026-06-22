(function ($) {
	'use strict';

	var previewRequest = null;
	var previewLoaded = false;
	var noteSubmitted = false;

	function getSelectedNoteType() {
		return $('#mhont_order_note_type').val();
	}

	function syncEditedPreview() {
		var $preview = $('#mhont_preview');
		updatePersonalFavoriteButton();
		var $edited = $('#mhont_edited_note');
		if ($preview.length && $edited.length) {
			var previewIsHtml = $preview.data('preview-html') === true;
			$edited.val(previewIsHtml ? $preview.html() : $preview.text());
		}
	}

	function setAddButtonState(enabled) {
		$('#mhont_add_order_note_button').prop('disabled', !enabled);
	}

	function insertCreatedOrderNotes(notesHtml) {
		if (!notesHtml) {
			return false;
		}

		var $notesList = $('#woocommerce-order-notes ul.order_notes, #order_notes ul.order_notes, ul.order_notes').first();
		if (!$notesList.length) {
			return false;
		}

		$notesList.find('li.no-items').remove();
		var $newNotes = $(notesHtml);
		$newNotes.hide();
		$notesList.prepend($newNotes);
		$newNotes.slideDown(150);
		return true;
	}


	function insertAtEditable(text) {
		var $preview = $('#mhont_preview');
		if (!$preview.length && window.tinymce && tinymce.get('mhont_content_editor')) {
			tinymce.get('mhont_content_editor').execCommand('mceInsertContent', false, text);
			return;
		}
		if (!$preview.length && $('#mhont_content_editor').length) {
			var field = $('#mhont_content_editor').get(0);
			var start = field.selectionStart || 0;
			var end = field.selectionEnd || 0;
			field.value = field.value.substring(0, start) + text + field.value.substring(end);
			field.selectionStart = field.selectionEnd = start + text.length;
			return;
		}
		if (!$preview.length) {
			return;
		}
		$preview.focus();
		if (window.getSelection && document.createRange) {
			var selection = window.getSelection();
			var previewElement = $preview.get(0);
			var range = selection && selection.rangeCount ? selection.getRangeAt(0) : null;
			var rangeContainer = range ? range.commonAncestorContainer : null;
			var rangeInsidePreview = rangeContainer && (rangeContainer === previewElement || $.contains(previewElement, rangeContainer));

			if (range && rangeInsidePreview) {
				range.deleteContents();
				range.insertNode(document.createTextNode(text));
				range.collapse(false);
				selection.removeAllRanges();
				selection.addRange(range);
			} else {
				$preview.append(document.createTextNode(text));
			}
		} else {
			$preview.append(document.createTextNode(text));
		}
		syncEditedPreview();
	}


	function updatePersonalFavoriteButton() {
		var $button = $('#mhont_personal_favorite_button');
		var $option = $('#mhont_template_id option:selected');
		var active = $option.data('personal-favorite') === 'yes';
		$button.prop('disabled', !$option.val()).toggleClass('button-primary', active);
		$button.text((active ? '★ ' : '☆ ') + ($button.data('label') || 'Personal favorite'));
	}

	function loadPreview() {
		var $template = $('#mhont_template_id');
		var templateId = $template.val();
		var orderId = $template.data('order-id');
		var $preview = $('#mhont_preview');

		previewLoaded = false;
		noteSubmitted = false;
		setAddButtonState(false);

		if (previewRequest && typeof previewRequest.abort === 'function') {
			previewRequest.abort();
			previewRequest = null;
		}

		if (!templateId) {
			$preview.data('preview-html', false).empty();
			syncEditedPreview();
			return;
		}

		$preview.data('preview-html', false).text(mhontAdmin.i18n.loading);
		syncEditedPreview();

		var currentRequest = $.post(mhontAdmin.ajaxUrl, {
			action: 'mhont_preview_template',
			nonce: mhontAdmin.previewNonce,
			template_id: templateId,
			order_id: orderId
		});
		previewRequest = currentRequest;

		currentRequest.done(function (response) {
			if (response && response.success && response.data && String($('#mhont_template_id').val()) === String(templateId)) {
				var previewIsHtml = response.data.preview_html === true;
				$preview.data('preview-html', previewIsHtml);
				if (previewIsHtml) {
					$preview.html(response.data.preview);
				} else {
					$preview.text(response.data.preview);
				}
				syncEditedPreview();
				previewLoaded = true;
				setAddButtonState(true);
				if (response.data.note_type) {
					$('#mhont_order_note_type').val(response.data.note_type);
				}
				return;
			}
			$preview.data('preview-html', false).text(mhontAdmin.i18n.error);
			syncEditedPreview();
		}).fail(function (xhr, status) {
			if (status === 'abort') {
				return;
			}
			$preview.data('preview-html', false).text(mhontAdmin.i18n.error);
			syncEditedPreview();
		}).always(function () {
			if (previewRequest === currentRequest) {
				previewRequest = null;
			}
		});
	}

	function filterTemplates() {
		var query = ($('#mhont_template_search').val() || '').toLowerCase();
		$('#mhont_template_id option').each(function () {
			var $option = $(this);
			if (!$option.val()) {
				$option.prop('hidden', false);
				return;
			}
			$option.prop('hidden', $option.text().toLowerCase().indexOf(query) === -1);
		});
	}

	function initTemplateSorting() {
		var params = new URLSearchParams(window.location.search || '');
		var paged = parseInt(params.get('paged') || '1', 10);
		if (paged > 1 || params.get('s') || params.get('orderby') || params.get('post_status') || params.get('mhont_category')) {
			return;
		}

		var $tbody = $('body.post-type-mhont_template table.wp-list-table tbody#the-list');
		if (!$tbody.length || typeof $tbody.sortable !== 'function') {
			return;
		}

		$tbody.sortable({
			items: 'tr',
			handle: '.mhont-sort-handle',
			axis: 'y',
			update: function (event, ui) {
				var order = [];
				var $row = ui.item;
				$tbody.find('tr[id^="post-"]').each(function () {
					order.push(String($(this).attr('id')).replace('post-', ''));
				});

				$row.css('opacity', 0.55);
				$.ajax({
					url: mhontAdmin.ajaxUrl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'mhont_sort_templates',
						nonce: mhontAdmin.sortNonce,
						order: order
					}
				}).done(function (response) {
					if (!response || !response.success) {
						$tbody.sortable('cancel');
						window.alert(response && response.data && response.data.message ? response.data.message : mhontAdmin.i18n.sortError);
					}
				}).fail(function () {
					$tbody.sortable('cancel');
					window.alert(mhontAdmin.i18n.sortError);
				}).always(function () {
					$row.css('opacity', '');
				});
			}
		});
	}

	$(document).on('change', '#mhont_template_id', loadPreview);
	$(document).on('change', '#mhont_order_note_type', function () {
		noteSubmitted = false;
		setAddButtonState(previewLoaded);
	});
	$(document).on('input blur keyup paste', '#mhont_preview', function () {
		noteSubmitted = false;
		syncEditedPreview();
		setAddButtonState(previewLoaded);
	});
	$(document).on('input', '#mhont_template_search', filterTemplates);
	$(document).on('click', '.mhont-insert-placeholder', function () {
		insertAtEditable($(this).data('placeholder') || '');
		if ($('#mhont_preview').length) {
			noteSubmitted = false;
			setAddButtonState(previewLoaded);
		}
	});
	$(document).on('click', '#mhont_add_order_note_button', function () {
		var $button = $(this);
		var templateId = $('#mhont_template_id').val();
		var $container = $button.closest('.mhont-order-form');
		var orderId = $container.data('order-id');
		var nonce = $container.data('note-nonce');

		syncEditedPreview();

		if (!templateId || !previewLoaded || noteSubmitted) {
			$('#mhont_action_message').removeClass('notice-success').addClass('notice-error').text(mhontAdmin.i18n.addError).prop('hidden', false);
			return;
		}

		if (!$button.data('original-text')) {
			$button.data('original-text', $button.text());
		}
		$('#mhont_action_message').prop('hidden', true).removeClass('notice-success notice-error').text('');
		$button.prop('disabled', true).text(mhontAdmin.i18n.saving);

		$.post(mhontAdmin.ajaxUrl, {
			action: 'mhont_add_order_note',
			mhont_nonce: nonce,
			order_id: orderId,
			template_id: templateId,
			note_type: getSelectedNoteType(),
			edited_note: $('#mhont_edited_note').val()
		}).done(function (response) {
			if (response && response.success) {
				noteSubmitted = true;
				if (response.data && response.data.notes_html) {
					insertCreatedOrderNotes(response.data.notes_html);
				}
				var successMessage = response.data && response.data.message ? response.data.message : mhontAdmin.i18n.added;
				$('#mhont_action_message').removeClass('notice-error').addClass('notice-success').text(successMessage).prop('hidden', false);
				return;
			}
			var message = response && response.data && response.data.message ? response.data.message : mhontAdmin.i18n.addError;
			$('#mhont_action_message').removeClass('notice-success').addClass('notice-error').text(message).prop('hidden', false);
		}).fail(function (xhr) {
			var message = mhontAdmin.i18n.addError;
			if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
				message = xhr.responseJSON.data.message;
			}
			$('#mhont_action_message').removeClass('notice-success').addClass('notice-error').text(message).prop('hidden', false);
		}).always(function () {
			$button.prop('disabled', !previewLoaded || noteSubmitted).text($button.data('original-text') || mhontAdmin.i18n.added);
		});
	});


	$(document).on('click', '#mhont_test_preview_button', function () {
		var $button = $(this);
		var orderId = parseInt($('#mhont_test_order_id').val() || '0', 10);
		var $result = $('#mhont_test_preview_result');
		if (!orderId) { $result.text(mhontAdmin.i18n.error).prop('hidden', false); return; }
		$button.prop('disabled', true);
		$.post(mhontAdmin.ajaxUrl, {
			action: 'mhont_test_template_preview',
			template_id: $button.data('template-id'),
			order_id: orderId,
			nonce: $button.data('nonce'),
			content: (window.tinymce && tinymce.get('mhont_content_editor')) ? tinymce.get('mhont_content_editor').getContent() : ($('#mhont_content_editor').val() || '')
		}).done(function (response) {
			if (response && response.success && response.data) { $result.html(response.data.preview).prop('hidden', false); }
			else { $result.text(response && response.data && response.data.message ? response.data.message : mhontAdmin.i18n.error).prop('hidden', false); }
		}).fail(function (xhr) {
			$result.text(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ? xhr.responseJSON.data.message : mhontAdmin.i18n.error).prop('hidden', false);
		}).always(function () { $button.prop('disabled', false); });
	});

	$(document).on('click', '#mhont_personal_favorite_button', function () {
		var $button = $(this);
		var templateId = $('#mhont_template_id').val();
		if (!templateId) { return; }
		$.post(mhontAdmin.ajaxUrl, {
			action: 'mhont_toggle_personal_favorite',
			nonce: $button.data('nonce'),
			template_id: templateId
		}).done(function (response) {
			if (response && response.success) {
				$('#mhont_template_id option:selected').data('personal-favorite', response.data.active ? 'yes' : 'no');
				$button.toggleClass('button-primary', response.data.active);
				$button.text((response.data.active ? '★ ' : '☆ ') + ($button.data('label') || 'Personal favorite'));
			}
		});
	});

	$(function () {
		syncEditedPreview();
		initTemplateSorting();
		updatePersonalFavoriteButton();
	});
})(jQuery);
