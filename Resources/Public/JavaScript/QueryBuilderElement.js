import DocumentService from "@typo3/core/document-service.js";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import $ from 'jquery';
import jqueryext from "jquery-extendext";
import queryBuilder from "query-builder";
import queryBuilderTemplates from "query-builder-templates";

class QueryBuilderElement {

    constructor(id,datatypeUid,valueFieldId,languageUid,pages) {
        this.id = '#'+id;
        this.datatypeUid = datatypeUid;
        this.valueFieldId = '#'+valueFieldId;
        this.languageUid = languageUid;
        this.pages = pages;
        this.configuration = {};

        DocumentService.ready().then((() => {

            // Load query builder field configuration
            const prom = this.loadConfiguration();
            prom.then((config) => {
                if(config.filters) {
                    $(this.id).html('');
                    this.configuration = config;
                    this.initQueryBuilder();
                } else {
                    this.showError('Error loading filters. No filters returned. Please check configuration.');
                }
            }).catch(error => {
                console.log(error);
                this.showError('Connection problems while loading filters.<br />Please try again.');
            })
        }));
    }

    saveRules(rules) {
        if (rules.rules && rules.rules.length > 0) {
            $(this.valueFieldId).val(JSON.stringify(rules, null, 2));
        } else {
            $(this.valueFieldId).val('');
        }
    }

    initQueryBuilder() {

        let rules = $(this.valueFieldId).val();
        const $builder = $(this.id);

        this.configuration.filters.forEach(filter=>{
            if(filter.input == 'FUNC') {
                filter.input = function(rule, name) {
                    let $container = rule.$el.find('.rule-value-container');
                    let optionsHtml = '';

                    for(const [key,value] of Object.entries(filter.options)) {
                        optionsHtml += '<option value="'+ value +'">'+key+'</option>';
                    }

                    return '\
                      <div class="row" style="margin-left:2px;"> \
                          <div class="col"> \
                              <select class="form-control" name="'+ name +'_1">'+optionsHtml+'</select> \
                          </div> \
                          <div class="col"> \
                            <input class="form-control" placeholder="value" type="text" name="'+ name +'_2" /> \
                          </div>  \
                      </div> \
                    ';
                };
                filter.valueGetter = function(rule) {
                    let val1 = rule.$el.find('.rule-value-container [name$=_1]').val(),
                        val2 = rule.$el.find('.rule-value-container [name$=_2]').val()
                    ;
                    return val1+'|'+val2;
                },
                filter.valueSetter = function(rule, value) {
                    if (rule.operator.nb_inputs > 0) {
                        var val = value.split('|');
                        rule.$el.find('.rule-value-container [name$=_1]').val(val[0]).trigger('change');
                        rule.$el.find('.rule-value-container [name$=_2]').val(val[1]).trigger('change');
                    }
                }
            }
        });

        $builder.queryBuilder(this.configuration);

        if (rules !== '') {
            try {
                $builder.queryBuilder('setRules', JSON.parse(rules));
            } catch (e) {
                console.log('QueryBuilder Error: '+e.message);
            }
        }

        const eventHandler = e => {
            const newRules = $builder.queryBuilder('getRules');
            if(newRules !== null) {
                this.saveRules($builder.queryBuilder('getRules'));
            }
        };

        $builder.on('afterUpdateRuleOperator.queryBuilder', eventHandler);
        $builder.on('afterUpdateRuleOperator.queryBuilder', eventHandler);
        $builder.on('afterUpdateRuleValue.queryBuilder', eventHandler);
        $builder.on('afterAddGroup.queryBuilder', eventHandler);
        $builder.on('afterAddRule.queryBuilder', eventHandler);
        $builder.on('afterDeleteGroup.queryBuilder', eventHandler);
        $builder.on('afterDeleteRule.queryBuilder', eventHandler);
        $builder.on('afterCreateRuleFilters.queryBuilder', eventHandler);
        $builder.on('afterClear.queryBuilder', eventHandler);
        $builder.on('afterUpdateGroupCondition.queryBuilder', eventHandler);
        $builder.on('afterUpdateRuleOperator.queryBuilder', eventHandler);
        $builder.on('afterUpdateRuleValue.queryBuilder', eventHandler);
    }

    loadConfiguration() {
        const status = new AjaxRequest(TYPO3.settings.ajaxUrls.dataviewer_querybuilder_configuration_get)
            .post({
                pids: this.pages,
                datatype: this.datatypeUid,
                languageUid: this.languageUid,
            }, {
            })
            .then(async (response)=>{
               const resolved = await response.resolve();
               const configuration = JSON.parse(resolved);

               if(configuration.filters) {
                   return configuration;
               } else {
                   return false;
               }

               return false;
            })
            .catch(error => {
                return false;
            });

            return status;
        ;
    }

    showError(error) {
        $(this.id).html('<div class=\'alert alert-danger\'>'+error+'</div>');
    }
}

export default QueryBuilderElement;
