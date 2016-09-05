.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _variable:

.. image:: ../../Images/logo_dataviewer.png

Creating a new Template Variable
--------------------------------

Template Variables are injected to the fluid templates. You can select them in the Records-Plugin.

.. image:: ../../Images/new_variable.jpg

Configuration
~~~~~~~~~~~~~   

* **Type**

+-----------------------+------------------------------------------------------------+
| Fixed Value           | A fixed text value                                         |
+-----------------------+------------------------------------------------------------+
| TypoScript Value      | Parsed TypoScript value                                    |
+-----------------------+------------------------------------------------------------+
| GET Variable          | Value from the GET Parameters of the page                  |
+-----------------------+------------------------------------------------------------+
| POST Variable         | Value from the POST Parameters of the page                 |
+-----------------------+------------------------------------------------------------+
| Record                | Single record instance                                     |
+-----------------------+------------------------------------------------------------+
| Record Field Value    | Value from a field of a record                             |
+-----------------------+------------------------------------------------------------+
| Database Value        | Database Result Value from a given query                   |
+-----------------------+------------------------------------------------------------+
| Frontend User         | The current frontend user                                  |
+-----------------------+------------------------------------------------------------+

* **Name**
The variable name that can be used in the fluid template.
