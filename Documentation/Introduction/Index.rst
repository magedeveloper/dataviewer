.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

.. image:: ../Images/logo_dataviewer.png

Introduction
------------

This extension can render records by TCA-based configuration without the need of creating a new
extension for every record-type you need. You can create new record-types on the fly with every
advantage of TCA.

These records are accessible and rendered in fluid-templates.

Fields in this extension are rendered in mostly the same way as the normal TCA rendering happens, but there
is no need for creating an extra extension for every record-type. 

*You need News on your page?* Create a "News"-Datatype and include the plugins... 
*You need a Job-List?* Create a "Job"-Datatype and start including plugins... 
*You need an Event-List?* Create a "Event"-Datatype and guess what...? :)

The extension contains various Frontend-Plugins to help and support you with displaying records as of your needs.
It is dynamically in every way, even to include records to another extension is possible!

Highlights
==========
- Create records on the fly
- Plugins for List, Detail, Sort, Filter, Selection and Search
- Dynamic Configuration of the Plugins to get nearly every solution
- Inject records to other extensions with a InjectViewHelper
- Inject dynamic variables of different types to the fluid templates
- Easy fluid templating with intuitive variable naming

Please see the Screenshots-Section for more information about the possibilities.
 

Workflow
========
.. image:: ../Images/workflow.jpg
