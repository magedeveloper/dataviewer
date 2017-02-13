.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _fieldtypes:

.. image:: ../Images/logo_dataviewer.png

Available Fieldtypes
--------------------

DataViewer delivers various fieldtypes as preconfigured TCA. With these, you are more flexible and faster
to create a form as you need it.


.. toctree::
   :maxdepth: 1
   :titlesonly:
   :glob:

   Category/Index
   Checkbox/Index
   Database/Index
   Datatype/Index
   Date/Index
   DateTime/Index
   DynamicInput/Index
   File/Index
   FileRelation/Index
   Flex/Index
   Fluid/Index
   Group/Index
   Image/Index
   Inline/Index
   Input/Index
   Link/Index
   MultiSelect/Index
   Page/Index
   Radio/Index
   Rte/Index
   Select/Index
   Tca/Index
   Textarea/Index
   TypoScript/Index
   UserFunc/Index


----------------------

Creating new Fieldtypes (Developer)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 
Fieldtypes can be added via TypoScript in the Plugin Settings.
See the following example code to add a custom field.

Here is the example code for adding a new fieldtype to the configuration.

**TypoScript**

.. code-block:: typoscript

	plugin.tx_dataviewer.fieldtypes {
	     
	    # Fieldtype Declaration for a new field #
	    customfieldtype {
	        fieldClass = Vendor\Extension\Fieldtype\CustomFieldtype
	        valueClass = Vendor\Extension\Fieldvalue\CustomFieldtype
	        icon = EXT:extension/Resources/Public/Icons/customfieldtype.gif
	        label = LLL:EXT:extension/Resources/Private/Language/locallang.xlf:type.customfieldtype
	        flexConfiguration = EXT:extension/Configuration/FlexForms/Fieldtype/CustomFieldtype.xml
	    }
	
	}

The fieldtype needs to have a fieldClass and a valueClass.

The **fieldClass** is used for generating the field in the backend and has to extend 
:php:`\MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype` and implement 
:php:`\MageDeveloper\Dataviewer\Form\Fieldtype\FieldtypeInterface`

The **valueClass** has to extend :php:`\MageDeveloper\Dataviewer\Form\Fieldvalue\AbstractFieldvalue` 
and implement :php:`\MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface`

*Fieldtype Configuration*
It is possible to add a custom configuration to the fieldtype with :php:`flexConfiguration =`.
The flexConfiguration-File has to contain valid flexform for the additional configuration.

That configuration is available in the files through :php:`$this->getField()->getConfig('yourFlexformConfigNode')`.
