.. include:: ../Includes.txt

.. _installation:

.. image:: ../Images/logo_dataviewer.png

Installation & Configuration
----------------------------

Step 1 - Installing the Extension to your TYPO3 instance
========================================================

Please install the extension in your TYPO3 instance.
Once it is correctly installed and fits the version dependency,
it should appear in your extension list as seen in the following screenshot.

.. image:: ../Images/extension_list.jpg


Step 2 - Include Static Template to your Site Template
======================================================

In order to add all fieldtypes and validators, it is necessary to include
the static template ``DataViewer Extension (dataviewer)`` to your Site Template.
Please refer to the following screenshot for more information.

.. image:: ../Images/include_static.jpg


Step 3 - Clear all caches
=========================

It is important to clear all the caches.


Backend Configuration
=====================

Toolbar Item
~~~~~~~~~~~~

.. image:: ../Images/toolbar_item.jpg
   :width: 450px

DataViewer adds an icon to the toolbar in the backend. With this
toolbar item, you can easy manage or create records of existing datatypes.

To deactivate the toolbar, you need to disable it in the User TSconfig with
the following code

.. code-block:: html

	options {
	  disableDataViewerToolbarItem = 0
	}