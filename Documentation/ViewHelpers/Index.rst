.. include:: ../Includes.txt

.. _viewhelpers:

.. image:: ../Images/logo_dataviewer.png

ViewHelpers
-----------


InjectViewHelper
################

This ViewHelper injects records to other fluid templates. You can use the records of a previous
Record-Plugin and inject them **in other fluid templates (of extensions)**.

To inject records to other fluid templates, just include a Record-Plugin above and use the following
code in the fluid template, where you want to inject the records of that Record-Plugin.

All you need is to use the parameter ``sourceUid``, where this is the Uid of the Record-Plugin, that
is positioned above your fluid template usage.

Usage:

.. code-block:: html

	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:inject sourceUid="51" />


.. note:: The process here is, that the relevant Record Ids are stored in the session, when the "Display Records"-Plugin above this
          is executed. The Ids then are requested when the ViewHelper is called. So there is an additional Database-Query, when you use this
          functionality.

Render-TemplateViewHelper
#########################

This ViewHelper renders template files, either defined in the plugin TypoScript or a manual file path.

+---------------+----------------------------------------------------------------------------------------------+
| Argument      | Description                                                                                  |
+===============+==============================================================================================+
| template      | Please enter a valid file or a template identifier from plugin.tx_dataviewer.templates       |
+---------------+----------------------------------------------------------------------------------------------+
| arguments     | The Arguments for the ViewHelper as an array                                                 |
+---------------+----------------------------------------------------------------------------------------------+

Usage:

.. code-block:: html

   {namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
   <dv:render.template template="movieMini" arguments="{record:record} />
   <dv:render.template template="fileadmin/templates/dataviewer/movies/partials/mini.html" arguments="{record:record} />


Render-PartViewHelper
#####################

The PartViewHelper renders different parts of the Records in case a template was set.

+---------------+-----------------------------+
| Record        | ``{record}``                |
+---------------+-----------------------------+
| RecordValue   | ``{record.recordValues.0}`` |
+---------------+-----------------------------+
| Value         | ``{record.*fieldname*}``    |
+---------------+-----------------------------+

Usage:

.. code-block:: html
	
	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:render.part part="{record}" />
	<dv:render.part part="{record.myfieldname}" />


GetViewHelper
#############

There are serveral GetViewHelper to retrieve data by id.

+--------------------+--------------------------------------------------------------------+
| Retrieved Object   | Usage                                                              |
+====================+====================================================================+
| Datatype           | ``{dv:datatype.get(id:'1',includeHidden:'0')}``                    |
+--------------------+--------------------------------------------------------------------+
| Field              | ``{dv:field.get(id:'1',includeHidden:'0')}``                       |
+--------------------+--------------------------------------------------------------------+
| Record             | ``{dv:record.get(id:'1',includeHidden:'0')}``                      |
+--------------------+--------------------------------------------------------------------+


Record-LinkViewHelper
#####################

This ViewHelper can create a record link with a record parameter and a detail page id.
The Detail Page Id is normally configured in the Plugin itself and is delivered through ``{settings.detail_page_id}``.

.. code-block:: html
	
	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:record.link record="{record}" pid="{settings.detail_page_id}">


Page-TitleViewHelper
####################

The (Page)TitleViewHelper is used for changing the Page Title within fluid, so
you can use you records information to generate a dynamic title for the page, that
is accessed.

.. code-block:: html

   {namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
   <dv:page.title part="{record}">{record.title}</dv:page.title>
