import $ from 'jquery';
import DocumentService from "@typo3/core/document-service.js";
import FormEngineValidation from "@typo3/backend/form-engine-validation.js";
import Modal from "@typo3/backend/modal.js";
import Severity from "@typo3/backend/severity.js";

class DatatypeTableField {
    constructor() {
        this.tableStatusFieldId = 'div#dataviewer_table_status',
        this.loaderId = 'div#dataviewer_table_status_loader',
        this.refreshButtonId = 'button#dataviewer_table_status_refresh',
        this.deleteButtonId = 'button#dataviewer_table_delete',
        this.migrateButtonId = 'button#dataviewer_table_status_migrate',
        this.messages = {
            error: null,
            delete_class: null,
            really: null,
            yes: null,
            no: null
        }
        ;

        DocumentService.ready().then((() => {

            let dataset = $(this.tableStatusFieldId).data();

            this.changeAllowed = (dataset.changeAllowed == 'true')? true : false;
            this.fieldName = dataset.fieldName;
            this.fieldId = dataset.fieldId;
            this.datatypeId = dataset.datatypeId;
            this.datatypeNameFieldId = '[data-formengine-input-name="'+dataset.datatypeNameFieldId+'"]';
            this.tablenameFieldId = dataset.tablenameFieldId;

            this.messages.error = dataset.messagesError;
            this.messages.delete_class = dataset.messagesDeleteTable
            this.messages.really = dataset.messagesReally
            this.messages.yes = dataset.messagesYes
            this.messages.no = dataset.messagesNo

            // Assign refresh button
            $(this.refreshButtonId).click(() => {
                 this.refresh();
            });

            // Initialize table status
            this.refresh();

            // Assign keyup to datatype name field
            this.assignDatatypeNameListener();
        }));
    }

    refresh() {
        this.request('dataviewer_table_status');
    }

    migrate() {
        this.request('dataviewer_table_migrate');
    }

    del() {
      const message = this.messages.really;
      Modal.confirm($('#'+this.fieldId).val(), message, Severity.error, [
          {
            text: this.messages.yes,
            active: true,
            btnClass: "btn-danger",
            trigger: () => {
                this.request('dataviewer_table_delete');
                Modal.dismiss();
            }
          }, {
            text: this.messages.no+'!',
            trigger: function() {
              Modal.dismiss();
            }
          }
      ]);

    }
    assignDatatypeNameListener() {
        $(this.datatypeNameFieldId).on("keyup", (e) => {

            if($('#'+this.fieldId).val() == '') {
                this.changeAllowed = true;
            }

            if (this.changeAllowed == true) {
                let val = 'tx_dataviewer_domain_model_record_' + this.slug(e.target.value);
                $('#' + this.fieldId).val(val);
                this.refresh();
            }
        });
        FormEngineValidation.validate();
    }

    showError(error) {
        var buttonHtml = '<button type="button" class="btn btn-default" id="dataviewer_table_delete">' + this.messages.delete_table + '</button>';
        $(this.tableStatusFieldId).html('<div class=\"callout callout-danger\">' + error + '<br /><br />' + buttonHtml + '</div>');
        $(this.deleteButtonId).click(function () {
            this.del();
        });
    }

    request(type) {
        $(this.loaderId).show();

        $(this.tableStatusFieldId).html('');
        $(this.tableStatusFieldId).show();

        $.ajax({
            context: this,
            url: TYPO3.settings.ajaxUrls[type],
            data: {
                tableName: $('#'+this.fieldId).val(),
                datatypeId: this.datatypeId
            },
            type: 'post',
            dataType: 'json',
            cache: false
        }).done(function (msg) {
            $(this.loaderId).hide();
            if (msg.success && msg.success == true) {
                if (msg.html) {
                    $(this.tableStatusFieldId).html(msg.html);

                    // Allow migration of table when button is shown
                    $(this.migrateButtonId).click(() => {
                        this.migrate();
                    });

                    $(this.deleteButtonId).click(() => {
                        this.del();
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

    slug(str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
        var to = "aaaaaeeeeeiiiiooooouuuunc------";
        for (var i = 0, l = from.length; i < l; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z -]/g, '') // remove invalid chars
            .replace(/\s+/g, '_') // collapse whitespace
            .replace(/-+/g, '_'); // collapse dashes

        return str;

    }
}

export default new DatatypeTableField;
