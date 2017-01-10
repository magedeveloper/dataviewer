/**
 * DataViewer Ajax Request Class
 * ============================================
 * This class does the DataViewer Ajax Request
 * for dynamically filtering records from
 * the records plugin by using the search form
 * as the request source
 *
 * @constructor
 */
var DataViewerAjaxRequest = function () {

	var $this = this,
		$ = jQuery;
		
	this.sourceId = null;	
	this.$bind = null;
	this.$target = null;
	this.$loader = null;
	this.event = null;
	this.parameters = {};
	this.fields = [];
	this.elements = [];
	this.attributes = [];

	this.setSourceId = function (sourceId) {
		this.sourceId = sourceId;
	}

	this.bindOn = function (source) {
		this.$bind = $(source);
	};

	this.setTarget = function (target) {
		this.$target = $(target);
	};

	this.setLoader = function (loader) {
		this.$loader = $(loader);
	};

	this.setOnEvent = function (event) {
		this.event = event;
	};
	
	this.addParameter = function (param, value) {
		this.parameters[param] = value;
	};
	
	this.addValueFromField = function (field, name) {
		this.fields[name] = field;
	};
	
	this.addValueFromElement = function (element, name) {
		this.elements[name] = element;	
	};
	
	this.addValueFromAttribute = function (attribute, name) {
		this.attributes[name] = attribute;
	};
	
	this.updateParameters = function (e) {

		var $eventTarget = $(e.target);

		for (var name in $this.fields)
		{
			var field = $this.fields[name];
			var $field = $(field);
			
			$this.parameters[name] = $field.val();
		}

		for (var name in $this.elements)
		{
			var element = $this.elements[name];
			var $element = $(element);
			$this.parameters[name] = $element.html();
		}
		
		for (var name in $this.attributes)
		{
			var attribute = $this.attributes[name];
			$this.parameters[name] = $eventTarget.attr(attribute);
		}
		
	};

	this.bind = function () {
	
		this.$bind.on(this.event, function(e) {

			// Update all parameters to obtain final data 
			// for the request
			$this.updateParameters(e);
		
			$.ajax({
				async: 'true',
				type: "POST",
				url: "index.php",
				dataType: 'html',
				data: {
					tx_dataviewer_record: {
						controller: 'Record',
						action: 'ajaxResponse',
						uid: $this.sourceId,
						parameters: $this.parameters
					},
					type: 100444
				},
				success: function(response) {
					$this.$target.html(response);
				},
				error: function(error){
					alert("error");
				}
			});
		});
		
	}

}	
