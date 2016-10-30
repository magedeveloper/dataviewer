.. _changelog:

.. image:: ../Images/logo_dataviewer.png

Changelog
---------

**2016-10-XX** - 1.2.0

- [BUGFIX] Empty selection is now delivering no records
- [BUGFIX] Unlimited redirects on empty selection
- [BUGFIX] Record now delivers the tstamp
- [TASK] Additional message in Record-Plugin lower to the Logo when no Record Storage Page is configured
- [FEATURE] Form Plugin now stores File Uploads (Configurable in Plugin)
- [FEATURE] Additional Template Variable Type "Server" from $_SERVER
- [FEATURE] Additional Template Variable Type "Dynamic Record" from the LinkViewHelper
- [BUGFIX] Multiple Plugins of the same kind on the same page is now working
- [FEATURE] Better information about the Uid of the "Display Records"-Plugin
- [FEATURE] Delete Action in the Form Controller for deleting records in the frontend
- [FEATURE] Allowed Actions configurable for the Form Controller
- [FEATURE] Final redirect on successful new/edit/delete in the Form-Plugin
- [DOC] Updated documentation and new Examples

**2016-10-07** - 1.1.2

- [BUGFIX] Record Title now saved correctly when Field-Contents is marked as record title
- [BUGFIX] ActionMenuViewHelper missing phpdoc method parameter (Thanks Thomas)
- [TASK] Exclude (see TCA) is now an Option in Field Configuration, Default is 0
- [BUGFIX] Corrected Icon Registration
- [TASK] Added Information to add static template, when no fieldtypes were found
- [DOC] Updated Documentation

**2016-09-23** - 1.1.1

- [BUGFIX] Record Title is now kept when hiding records
- [BUGFIX] TCA correction for Record->Datatype
- [BUGFIX] Some PHP 7 corrections
- [TASK] Displaying hidden records now as hidden in the module
- [TASK] Buttons for deleting and hidding records are now in the Information Module
- [DOC] Added additional information on the Form-Plugin

**2016-09-14** - 1.1.0

- [TASK] Compatibility TYPO3 8.3
- [BUGFIX] Creating Fields in DataViewer-Backend-Module
- [BUGFIX] Included missing Radio Field
- [BUGFIX] Removed Session-Value Restoring for FileRelation
- [TASK] Compatibility for Category Field to new SelectTreeElement
- [FEATURE] New Backend Module Option for displaying Record-Information
- [BUGFIX] Corrected Exception return on Database FieldValues
- [TASK] Changed sorting of fields in backend to newest(uid) = top

**2016-09-07** - 1.0.3

- [BUGFIX] Fixed FieldValues Creation
- [BUGFIX] Removed column 'internal_position'
- [BUGFIX] Deleting Records in DataViewer-Backend-Module
- [DOC] Documentation changes

**2016-09-06** - 1.0.2

- [BUGFIX] Fixed Fieldtype-Icons Path
- [DOC] Documentation changes

**2016-09-03** - 1.0.1

- [DOC] Documentation added

**2016-09-02** - 1.0.0

- Initial release and upload to TER



**To-Do List**

- RecordFactory for creating records with a simple array
