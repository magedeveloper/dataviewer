.. include:: ../Includes.txt

.. _viewhelpers:

.. image:: ../Images/logo_dataviewer.png

ViewHelpers
-----------


InjectViewHelper
################

This ViewHelper injects records to other fluid templates. You can use the records of a previous
Record-Plugin and inject them in other fluid templates (of extensions).

To inject records to other fluid templates, just include a Record-Plugin above and use the following
code in the fluid template, where you want to inject the records of that Record-Plugin.

All you need is to use the parameter ``sourceUid``, where this is the Uid of the Record-Plugin, that
is positioned above your fluid template usage.

Usage:

.. code-block:: html

	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:inject sourceUid="51" />


(Render)PartViewHelper
######################

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


(Record)LinkViewHelper
######################

This ViewHelper can create a record link with a record parameter and a detail page id.

.. code-block:: html
	
	{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
	<dv:record.link record="{record}" pid="{settings.detail_page_id}">


(Page)TitleViewHelper
####################

The (Page)TitleViewHelper is used for changing the Page Title within fluid, so
you can use you records information to generate a dynamic title for the page, that
is accessed.

.. code-block:: html

   {namespace dv = MageDeveloper\Dataviewer\ViewHelpers}
   <dv:page.title part="{record}">{record.title}</dv:page.title>
