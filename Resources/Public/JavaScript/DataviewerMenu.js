define(['jquery',
	'TYPO3/CMS/Backend/Modal',
	'TYPO3/CMS/Backend/Icons',
	'TYPO3/CMS/Backend/Notification'
], function($, Modal, Icons, Notification) {
	'use strict';

	/**
	 *
	 * @type {{options: {containerSelector: string, toolbarIconSelector: string, toolbarMenuSelector: string, shortcutItemSelector: string, shortcutDeleteSelector: string, shortcutEditSelector: string, shortcutFormTitleSelector: string, shortcutFormGroupSelector: string, shortcutFormSaveSelector: string, shortcutFormCancelSelector: string}}}
	 * @exports MageDeveloper/Dataviewer/DataviewerMenu
	 */
	var DataviewerMenu = {
		options: {
			containerSelector: '#magedeveloper-dataviewer-hooks-toolbaritem',
			toolbarIconSelector: '.dropdown-toggle span.icon',
			toolbarMenuSelector: '.dropdown-menu',
			recordItemSelector: '.dropdown-menu .record',
			recordDeleteSelector: '.dv-record-delete',
			recordHideSelector: '.dv-record-hide'
		}
	};

	/**
	 * removes an existing short by sending an AJAX call
	 *
	 * @param {Object} $shortcutRecord
	 */
	DataviewerMenu.hideShowRecord = function($dataviewerRecord) {
		var $toolbarItemIcon = $(DataviewerMenu.options.toolbarIconSelector, DataviewerMenu.options.containerSelector),
			$existingIcon = $toolbarItemIcon.clone();

		Icons.getIcon('spinner-circle-light', Icons.sizes.small).done(function(spinner) {
			$toolbarItemIcon.replaceWith(spinner);
		});

		$.ajax({
			url: TYPO3.settings.ajaxUrls['dataviewer_hideshow'],
			data: {
				recordId: $dataviewerRecord.data('recordid')
			},
			type: 'post',
			cache: false
		}).done(function() {
			// a reload is used in order to restore the original behaviour
			// e.g. remove groups that are now empty because the last one in the group
			// was removed
			$(DataviewerMenu.options.toolbarIconSelector, DataviewerMenu.options.containerSelector).replaceWith($existingIcon);
			DataviewerMenu.refreshMenu();
		});
	};

	/**
	 * removes an existing short by sending an AJAX call
	 *
	 * @param {Object} $shortcutRecord
	 */
	DataviewerMenu.deleteRecord = function($dataviewerRecord) {
		Modal.confirm(TYPO3.lang['record.delete'], TYPO3.lang['record.confirmDelete'])
			.on('confirm.button.ok', function() {

				var $toolbarItemIcon = $(DataviewerMenu.options.toolbarIconSelector, DataviewerMenu.options.containerSelector),
					$existingIcon = $toolbarItemIcon.clone();

				Icons.getIcon('spinner-circle-light', Icons.sizes.small).done(function(spinner) {
					$toolbarItemIcon.replaceWith(spinner);
				});
			
				$.ajax({
					url: TYPO3.settings.ajaxUrls['dataviewer_remove'],
					data: {
						recordId: $dataviewerRecord.data('recordid')
					},
					type: 'post',
					cache: false
				}).done(function() {
					// a reload is used in order to restore the original behaviour
					// e.g. remove groups that are now empty because the last one in the group
					// was removed
					$(DataviewerMenu.options.toolbarIconSelector, DataviewerMenu.options.containerSelector).replaceWith($existingIcon);
					DataviewerMenu.refreshMenu();
				});
				$(this).trigger('modal-dismiss');
			})
			.on('confirm.button.cancel', function() {
				$(this).trigger('modal-dismiss');
			});
	};

	/**
	 * reloads the menu after an update
	 */
	DataviewerMenu.refreshMenu = function() {

		// Show Loading Icon
		Icons.getIcon('spinner-circle-light', Icons.sizes.small).done(function(spinner) {
			var html = spinner + "&nbsp;" + TYPO3.lang['dataviewer.loading'];
			$(DataviewerMenu.options.toolbarMenuSelector, DataviewerMenu.options.containerSelector).html(html);
		});
	
		$.ajax({
			url: TYPO3.settings.ajaxUrls['dataviewer_list'],
			type: 'get',
			cache: false
		}).done(function(data) {
			$(DataviewerMenu.options.toolbarMenuSelector, DataviewerMenu.options.containerSelector).html(data);
			$(DataviewerMenu.initializeEvents);
		});
	};

	/**
	 * Registers listeners
	 */
	DataviewerMenu.initializeEvents = function() {

		// Unbind previous events
		$(DataviewerMenu.options.containerSelector).unbind("click");
		$(DataviewerMenu.options.recordHideSelector).unbind("click");
		$(DataviewerMenu.options.recordDeleteSelector).unbind("click");
	
		$(DataviewerMenu.options.containerSelector).on('click', function(evt) {
		
			if($(DataviewerMenu.options.containerSelector).hasClass("open") == false)
			{
				// Only refresh when menu is going to open
				DataviewerMenu.refreshMenu();
			}
			return;
		});

		$(DataviewerMenu.options.recordHideSelector).on('click', function(evt) {
			evt.preventDefault();
			evt.stopImmediatePropagation();
			DataviewerMenu.hideShowRecord($(this).closest(DataviewerMenu.options.recordItemSelector));
		});

		$(DataviewerMenu.options.recordDeleteSelector).on('click', function(evt) {
			evt.preventDefault();
			evt.stopImmediatePropagation();
			DataviewerMenu.deleteRecord($(this).closest(DataviewerMenu.options.recordItemSelector));
		});
	};

	$(DataviewerMenu.initializeEvents);

	// expose as global object
	TYPO3.DataviewerMenu = DataviewerMenu;

	return DataviewerMenu;
});
