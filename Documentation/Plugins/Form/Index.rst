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
   
File Upload Folder
   If the form contains upload-fields, the data will be saved in the selected folder.
   
Allowed Actions on Controller
   Please select the allowed actions that this plugin will support. If you try to call an invalid action,
   the plugin will add an error-message to the screen.

Record Storage Page
   Please select the record storage page.

Redirect Settings
#################

Redirect after successful creation
   The redirect page, when a new record is successfully created.
   
   
Redirect after successful editing
   The redirect page, when a record is successfully saved after editing.
   
   
Redirect after successful creation
   The redirect page, when a record was successfully deleted.


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
	
	<label for="type">Cover:</label>
	<f:form.upload name="cover" />
	<f:if condition="{record.cover}">
		<br />
		Current Cover: {record.cover.value.0.publicUrl}
	</f:if>
	
	<f:form.submit name="submit" value="Submit this record" />


Link to Edit a Record
~~~~~~~~~~~~~~~~~~~~~

.. code-block:: html

	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:record.link record="{record}" pid="{formPageId}" action="index" controller="Form" extension="Dataviewer" plugin="Form">Edit {record.title}</dv:record.link>

Link to Delete a Record
~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: html

	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:record.link record="{record}" pid="{formPageId}" action="delete" controller="Form" extension="Dataviewer" plugin="Form">Delete {record.title}</dv:record.link>
