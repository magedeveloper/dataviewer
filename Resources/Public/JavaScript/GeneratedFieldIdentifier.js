import DocumentService from "@typo3/core/document-service.js";
import FormEngineValidation from "@typo3/backend/form-engine-validation.js";

class GeneratedFieldIdentifier {
    constructor() {
        this.frontendLabelFieldName = null,
        this.frontendLabelValueFieldName = null,
        this.variableNameFieldName = null,
        this.variableValueFieldName = null,

        this.frontendLabel = null,
        this.variableName = null,
        this.variableNameValue = null,
        this.canChangeVariableName = false,

        this.fieldId = 'code_gen',
        this.generatedCodeFieldId = 'generated_code',
        this.field = document.querySelector('div#'+ this.fieldId)
        DocumentService.ready().then((() => {
            let dataset = this.field.dataset;
            this.frontendLabelFieldName = dataset.frontendLabelFieldName;
            this.frontendLabelValueFieldName = dataset.frontendLabelValueFieldName;
            this.variableNameFieldName = dataset.variableNameFieldName;
            this.variableValueFieldName = dataset.variableValueFieldName;

            // Initialize values
            this.frontendLabel = this.getFrontendLabelValue();
            this.variableName = this.getVariableNameValue();
            this.variableNameValue = this.variableName;

            if(this.variableNameValue == '') {
                // Initialize we can change the variable name, because
                // the form initializes empty
                this.canChangeVariableName = true;
            }

            // Assign keyup to frontend label field
            this.assignFrontendLabelListener();

            // Assign keyup to variable name field
            this.assignVariableNameListener();
        }));
    }

    getFrontendLabelValue() {
        return document.querySelector(this.frontendLabelValueFieldName).value;
    }

    getVariableNameValue() {
        return document.querySelector(this.variableValueFieldName).value;
    }

    getLiveVariableNameValue() {
        return document.querySelector(this.variableNameFieldName).value;
    }

    assignVariableNameListener() {
        document.querySelector(this.variableNameFieldName).addEventListener("keyup", (e)=>{
            let val = this.slug(e.target.value);
            this.showVariableName(val);
        });

        FormEngineValidation.validate();
    }

    assignFrontendLabelListener() {
        document.querySelector(this.frontendLabelFieldName).addEventListener("keyup", (e) => {

            if(this.getLiveVariableNameValue() == '') {
                this.canChangeVariableName = true;
            }


            if(this.canChangeVariableName == true) {
                let val = this.slug(e.target.value);
                document.querySelector(this.variableNameFieldName).value = val;
                document.querySelector(this.variableValueFieldName).value = val;
                this.showVariableName(val);
            }

            FormEngineValidation.validate();
        });
    }

    showVariableName(variableName) {
        document.querySelector('#'+this.fieldId+' '+'#'+this.generatedCodeFieldId).innerHTML = variableName;
    }

    slug(str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
        var to   = "aaaaaeeeeeiiiiooooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z -]/g, '') // remove invalid chars
                 .replace(/\s+/g, '') // collapse whitespace
                 .replace(/-+/g, ''); // collapse dashes

        return str;
    }
}

export default new GeneratedFieldIdentifier;
