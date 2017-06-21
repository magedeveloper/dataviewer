/**
 * DataViewer Ajax Request Class
 * ============================================
 * This class does the DataViewer Ajax Request
 * for dynamically loading data
 *
 * @constructor
 */
var DataViewerAjaxRequest = function (sourceId) {

	var $this = this,
		$ = jQuery;
		
	this.sourceId = sourceId;	
	this.$bind = null;
	this.$target = null;
	this.loader = null;
	this.event = null;
	this.url = null;
	this.parameters = {};
	this.fields = [];
	this.elements = [];
	this.attributes = [];
	this.clearDuringLoad = [];

	/**
	 * Sets the source id for the
	 * request
	 * 
	 * @param int sourceId
	 * @return void
	 */
	this.setSourceId = function (sourceId) {
		this.sourceId = sourceId;
	}

	/**
	 * Sets the binding source for the request
	 * 
	 * @param string source
	 * @return void
	 */
	this.bindOn = function (source) {
		this.$bind = $(source);
	};

	/**
	 * Sets the target, where to requested contents
	 * will be pushed in 
	 * @param string target
	 * @return void
	 */
	this.setTarget = function (target) {
		this.$target = $(target);
	};

	/**
	 * Sets the loader element that will be
	 * used as the ajax loader
	 * 
	 * @param string loader
	 * @return void
	 */
	this.setLoader = function (loader) {
		this.loader = loader;
	};

	/**
	 * Sets the event, when the request will
	 * be made. e.g. click, load, keyUp
	 * 
	 * @param string event
	 * @return void
	 */
	this.setOnEvent = function (event) {
		this.event = event;
	};

	/**
	 * Sets the url that will be called 
	 * for the ajax request
	 * 
	 * You may leave it empty for the default index.php url
	 * 
	 * @param string url
	 * @return void
	 */
	this.setUrl = function(url) {
		this.url = url;	
	};

	/**
	 * Adds an parameter to the request
	 * 
	 * @param string param
	 * @param mixed value
	 * @return void
	 */
	this.addParameter = function (param, value) {
		this.parameters[param] = value;
	};

	/**
	 * Adds a value from a field on the page
	 * Uses .val()
	 * 
	 * @param string field	Field Identifier
	 * @param string name	Target Parameter Name
	 * @return void
	 */
	this.addValueFromField = function (field, name) {
		this.fields[name] = field;
	};

	/**
	 * Adds a value from an element on the page
	 * Uses .html()
	 * 
	 * @param string element	Element Name
	 * @param string name		Target Parameter Name
	 * @return void
	 */
	this.addValueFromElement = function (element, name) {
		this.elements[name] = element;	
	};

	/**
	 * Adds an value from an attribute of an element
	 * Uses .attr() on the current element
	 * 
	 * @param attribute		The Attribute of the current element
	 * @param name			Target Parameter Name
	 * @return void
	 */
	this.addValueFromAttribute = function (attribute, name) {
		this.attributes[name] = attribute;
	};

	/**
	 * Adds an html clear of an element
	 * 
	 * @param string elementId		Id of the element
	 * @return void
	 */
	this.addClearDuringLoad = function(elementId) {
		this.clearDuringLoad[elementId] = elementId;
	}

	/**
	 * Updates all parameters
	 * 
	 * @param object e 
	 * @return void
	 */
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

	/**
	 * Final binding method that binds the event to the target
	 * 
	 * @return bool
 	 */	
	this.bind = function () {

		if (this.url === null)
		{
			this.setUrl("index.php");
		}

		this.$bind.on(this.event, function(e) {
			// Update all parameters to obtain final data 
			// for the request
			$this.updateParameters(e);

			// Clearing the elements that shall be cleared during load
			for (var elementId in $this.clearDuringLoad)
			{
				$(elementId).html("");
			}

			// Show the loader
			if ($this.loader)
				$($this.loader).show();
			
			// Performing the ajax request
			$.ajax({
				async: 'true',
				type: "POST",
				url: $this.url,
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

					if ($this.loader)
						$($this.loader).hide();
				},
				error: function(error){
					console.log("Ajax request error");
				}
			});

			return false;
		});
		
	}

}	
