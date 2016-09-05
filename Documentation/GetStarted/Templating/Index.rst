.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _templating:

.. image:: ../../Images/logo_dataviewer.png

Templating
----------

Here is a quick overview of information about templating with the DataViewer-Extension:

+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| Include Namespace to the fluid template       | ``{namespace dv = MageDeveloper\Dataviewer\ViewHelpers}``                                       |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| Part Rendering                                | Part Rendering is possible for ``{record}``, ``{record.fieldname}``, ``{part}``.                |
|                                               | If a template has been set for a part, you can use to following code to render the part:        |
|                                               | ``<dv:render.part part="{record.fieldname}" />``                                                |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| Records are injected                          | The default available marker for all records is ``{records}``                                   |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| A single record is injected                   | A single record can be called with default ``{record}``                                         |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| A value for a field                           | To retrieve a value, use ``{record.fieldname.value}`` (e.g. ``{record.myfield.value}``.         |
|                                               | The values are of different return types. Please see the FieldValue-Classes for more details    |
|                                               | ``(\MageDeveloper\Dataviewer\Form\Fieldvalue\*)``                                               |
|                                               | or use ``<f:debug>{record.fieldname.value}</f:debug>`` to get debug information                 |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| Getting all items from a select box           | Use ``{record.fieldname.fieldtype.items}`` to get all items of a field                          |
+-----------------------------------------------+-------------------------------------------------------------------------------------------------+



