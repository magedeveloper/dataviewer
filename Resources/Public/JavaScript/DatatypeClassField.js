import $ from 'jquery';
import DocumentService from "@typo3/core/document-service.js";

class DatatypeClassField {
    constructor() {
        this.statusFieldId = 'div#dataviewer_class_status',
        this.loaderId = 'div#dataviewer_class_status_loader',
        this.refreshButtonId = 'button#dataviewer_class_status_refresh',
        this.deleteButtonId = 'button#dataviewer_class_delete',
        this.migrateButtonId = 'button#dataviewer_class_status_migrate',
        this.messages = {
            error: null,
            delete_class: null
        }
        ;

        DocumentService.ready().then((() => {
            let dataset = $(this.statusFieldId).data();

            this.fieldName = dataset.fieldName;
            this.fieldId = dataset.fieldId;
            this.datatypeId = dataset.datatypeId;
            this.datatypeNameFieldId = dataset.datatypeNameFieldId;

            this.messages.error = dataset.messagesError;
            this.messages.delete_class = dataset.messagesDeleteClass;

            this.datatypeNameValueFieldId = '[data-formengine-input-name="' + this.datatypeNameFieldId + '"]';
            this.datatypeNameValueField = $(this.datatypeNameValueFieldId);

            // Assign refresh button
            $(this.refreshButtonId).click( () => {
                this.refresh();
            });

            // Initialize class status
            this.refresh();
        }));
    }

    refresh() {
        this.request('dataviewer_class_status');
    }

    migrate() {
        this.request('dataviewer_class_migrate');
    }

    delete() {
        this.request('dataviewer_class_delete');
    }

    showError(error) {
        var buttonHtml = '<button type="button" class="btn btn-default" id="dataviewer_class_delete">' + this.messages.delete_class + '</button>';
        $(this.statusFieldId).html('<div class=\"callout callout-danger\">' + error + '<br /><br />' + buttonHtml + '</div>');
        $(this.deleteButtonId).click(function () {
            this.delete();
        });
    }


    request(type) {
        $(this.loaderId).show();
        $(this.statusFieldId).html('');
        $(this.statusFieldId).show();

        $.ajax({
            context: this,
            url: TYPO3.settings.ajaxUrls[type],
            data: {
                tableName: $(this.tableField).val(),
                datatypeId: this.datatypeId
            },
            type: 'post',
            dataType: 'json',
            cache: false
        }).done(function (msg) {
            $(this.loaderId).hide();
            if (msg.success && msg.success == true) {
                if (msg.html) {
                    $(this.statusFieldId).html(msg.html);

                    // Allow migration of table when button is shown
                    $(this.migrateButtonId).click(() => {
                        this.migrate();
                    });
                }
            } else {
                if (msg.success == false) {
                    if (msg.html) {
                        this.showError('<strong>Error:</strong> ' + msg.html);
                        return;
                    }
                }
                this.showError('<strong>Error:</strong> ' + this.messages.error);
            }
        }).fail(function (e) {
            this.showError('<strong>Error:</strong> ' + this.messages.error);
        });
    }

}

export default new DatatypeClassField;
