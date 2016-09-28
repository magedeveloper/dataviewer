.. include:: ../../Includes.txt

.. _formplugin:

.. image:: ../../Images/logo_dataviewer.png

Form Plugin
-----------

With this plugin you can create new records or edit existing records by creating a fluid templates with fields, that
are equal to the records fields. The record will be automatically validated by the field-validation on form post.

.. image:: ../../Images/plugin_form.jpg

Configuration
~~~~~~~~~~~~~

DataViewer Settings
###################

Record-Datatype for the Storage
   Please select the record type which will be stored when the form is posted.

Record Storage Page
   Please select the record storage page.

Template Settings
#################

Fluid-Template
   The template that displays the form.

Variable Injection
   Select the variables, that will be injected into the fluid template.

Developer Settings
##################

Debug
   Enable this setting to get an debug output when the form is posted.
   This setting can help a developer to check for possible errors and to validate
   the form.


Example
~~~~~~~

This is an example code for the fluid template form of a move record.
You don't need to add the <f:form>-Tag, because this is automatically done
by the Plugin itself.

Each field has to get the same name as the code of the field is.

.. code-block:: html

	<label for="title">Title:</label>
	<f:form.textfield name="title" value="{record.title}" />
	
	<!-- {record.type.fieldtype.items} is used to retrieve all items from the select box -->
	<label for="type">Type:</label>
	<f:form.select options="{record.type.fieldtype.items}" name="type" value="{record.type.value}" />
	
	<label for="type">Length:</label>
	<f:form.textfield name="length" value="{record.length.value}" />
	
	<label for="type">Age Rating:</label>
	<f:form.select options="{record.agerating.fieldtype.items}" name="agerating" value="{record.agerating.value}" />
	
	<f:form.submit name="submit" value="Submit this record" />
