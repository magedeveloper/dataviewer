.. include:: ../../Includes.txt


.. _templating_gs:

.. image:: ../../Images/logo_dataviewer.png

Templating
----------

Here is a quick overview of information about templating with the DataViewer-Extension:

+-----------------------------------------------+-------------------------------------------------------------------------------------------------+
| Condition                                     | Templating                                                                                      |
+===============================================+=================================================================================================+
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

.. note:: Of course you can use ``<f:debug>{_all}</f:debug>`` to find out which variables are available!

Return Types
~~~~~~~~~~~~

+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| Template Command                                        | Returned Class/Method                                                                      |
+=========================================================+============================================================================================+
| {record}                                                | ``MageDeveloper\Dataviewer\Domain\Model\Record``                                           |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the record model                                                                   |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| {record.fieldname}                                      | ``MageDeveloper\Dataviewer\Domain\Model\Value``                                            |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the value model for the field                                                      |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| {record.fieldname.value}                                | ``MageDeveloper\Dataviewer\Form\Fieldvalue\Type->getFrontendValue()``                      |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the computed value for the field. The return type is different, see                |
|                                                         | ``EXT:dataviewer\Form\FieldValues\Type.php->getFrontendValue()`` for more information.     |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| {record.fieldname.fieldtype}                            | ``MageDeveloper\Dataviewer\Form\Fieldtype\Type``                                           |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the according fieldtype for the field                                              |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| {record.fieldname.fieldvalue}                           | ``MageDeveloper\Dataviewer\Form\Fieldvalue\Type``                                          |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the according fieldvalue for the field                                             |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
| {record.fieldname.fieldvalue.valueContent}              | ``MageDeveloper\Dataviewer\Form\Fieldtype\Type->getValueContent()``                        |
|                                                         +--------------------------------------------------------------------------------------------+
|                                                         | Returns the plain value of the field                                                       |
+---------------------------------------------------------+--------------------------------------------------------------------------------------------+
