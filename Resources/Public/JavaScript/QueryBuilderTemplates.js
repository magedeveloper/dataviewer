import QueryBuilder from "query-builder";

QueryBuilder.templates.filterSelect = ({ rule, filters, icons, settings, translate, builder }) => {
  let optgroup = null;
  return `
<select class="form-select" name="${rule.id}_filter">
  ${settings.display_empty_filter ? `
    <option value="-1">${settings.select_placeholder}</option>
  ` : ''}
  ${filters.map(filter => `
    ${optgroup !== filter.optgroup ? `
      ${optgroup !== null ? `</optgroup>` : ''}
      ${(optgroup = filter.optgroup) !== null ? `
        <optgroup label="${translate(settings.optgroups[optgroup])}">
      ` : ''}
    ` : ''}
    <option value="${filter.id}" ${filter.icon ? `data-icon="${filter.icon}"` : ''}>${translate(filter.label)}</option>
  `).join('')}
  ${optgroup !== null ? '</optgroup>' : ''}
</select>`;
};


QueryBuilder.templates.operatorSelect = ({ rule, operators, icons, settings, translate, builder }) => {
  let optgroup = null;
  return `
${operators.length === 1 ? `
<span>
${translate("operators", operators[0].type)}
</span>
` : ''}
<select class="form-select ${operators.length === 1 ? 'hide' : ''}" name="${rule.id}_operator">
  ${operators.map(operator => `
    ${optgroup !== operator.optgroup ? `
      ${optgroup !== null ? `</optgroup>` : ''}
      ${(optgroup = operator.optgroup) !== null ? `
        <optgroup label="${translate(settings.optgroups[optgroup])}">
      ` : ''}
    ` : ''}
    <option value="${operator.type}" ${operator.icon ? `data-icon="${operator.icon}"` : ''}>${translate("operators", operator.type)}</option>
  `).join('')}
  ${optgroup !== null ? '</optgroup>' : ''}
</select>`;
};

export default {};