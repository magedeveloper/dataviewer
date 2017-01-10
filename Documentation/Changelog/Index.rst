.. _changelog:

.. image:: ../Images/logo_dataviewer.png

Changelog
---------

**1.5.0**

- [FEATURE] Send custom headers in the record plugin to generate different content types
- [FEATURE] Select to render only record template or with full site template
- [BUGFIX] Corrected Message when saving a new field
- [BUGFIX] File Relations now saved correctly
- [BUGFIX] Added PartialRootPaths and LayoutRootPaths to AbstractCmsLayout and corrected file positions
- [BUGFIX] Count of CSV Import columns in backend when no title line exists
- [BUGFIX] Minitems and Maxitems for File Relation Fieldtype
- [BUGFIX] Multiple Display Conditions for Fields (AND, OR) as XML
- [BUGFIX] DocHeaderButtons now shows default icon when Datatype has no icon
- [TASK] Return Types in Field-Toolbox on click
- [TASK] Slighly changed colors of the backend form
- [TASK] Added donation link to the extension information in the constants
- [FEATURE] Backend Toolbar Item for fast record creation / access
- [DOC] Documentation update

**1.4.1**

- [BUGFIX] Installation bug fixed

**1.4.0**

- [BUGFIX] Unlimited redirects when hiding records in List Module
- [BUGFIX] Record Title is no more removing X's
- [BUGFIX] Validation is now triggered even when record is not saved before
- [BUGFIX] Record Title is now kept when trying to save an invalid record
- [TASK] Save-Procedure revisited
- [TASK] Selecting a datatype is no more forcing to store a record
- [TASK] Importing 'validation-failed' records is now an option in the CSV Import Assistant
- [FEATURE] Template Switch by Conditions
- [FEATURE] Ajax Request Listener with Signal/Slot for implementing Ajax calls (early alpha, proof of concept)

**1.3.1**

- [BUGFIX] Date(Time) Fields will work now for CSV Import as well as for normal fields
- [TASK] Current Record is now injected into fluid fields
- [TASK] Added possibility to hide Add-Button for New Records in Datatype Configuration
- [DOC] Documentation update

**1.3.0**

- [TASK] Selectable divider for record title when multiple fields are used for title
- [TASK] Selected templates are now linked in the backend CmsLayout
- [TASK] Added new backend form styles for H1-H5, HR when adding HTML into Fluid Fields
- [TASK] Group Field now can return a model
- [TASK] Search through Multiselect/Group Fields with FIND_IN_SET
- [TASK] Displaying correct 'recordName' for all information views
- [FEATURE] Predefined Templates Selectable; Configurable in TypoScript (plugin.tx_dataviewer.templates)
- [FEATURE] Render-TemplateViewHelper works with predefined templates
- [BUGFIX] Hidden record title field on new record creation, when fields are marked as title
- [BUGFIX] Backend List Module: Hiding records results in loosing title
- [BUGFIX] 'Multiple'-Configuration for Select and Group Fieldtype
- [BUGFIX] Deleted Records in Group and Multiselect are now removed
- [TASK] Upload folder for files
- [FEATURE] CSV Import Assistant
- [FEATURE] Custom Folder Icons from the DataViewer Icons of existing Datatypes
- [TASK] Template Variable 'User Session' obtains information from User Session
- [TASK] Template Variable 'Page' for easier page selection
- [BUGFIX] Creating new inline sub-records now redirects back to master record on save
- [BUGFIX] Corrections for T3D Import/Export
- [TASK] Comma Separated Values in Checkbox-Field are now converted to the required integer value

**1.2.1**

- [BUGFIX] FormController Datatype Error correction
- [BUGFIX] Hidden Records on Save

**1.2.0**

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

**1.1.2**

- [BUGFIX] Record Title now saved correctly when Field-Contents is marked as record title
- [BUGFIX] ActionMenuViewHelper missing phpdoc method parameter (Thanks Thomas)
- [TASK] Exclude (see TCA) is now an Option in Field Configuration, Default is 0
- [BUGFIX] Corrected Icon Registration
- [TASK] Added Information to add static template, when no fieldtypes were found
- [DOC] Updated Documentation

**1.1.1**

- [BUGFIX] Record Title is now kept when hiding records
- [BUGFIX] TCA correction for Record->Datatype
- [BUGFIX] Some PHP 7 corrections
- [TASK] Displaying hidden records now as hidden in the module
- [TASK] Buttons for deleting and hidding records are now in the Information Module
- [DOC] Added additional information on the Form-Plugin

**1.1.0**

- [TASK] Compatibility TYPO3 8.3
- [BUGFIX] Creating Fields in DataViewer-Backend-Module
- [BUGFIX] Included missing Radio Field
- [BUGFIX] Removed Session-Value Restoring for FileRelation
- [TASK] Compatibility for Category Field to new SelectTreeElement
- [FEATURE] New Backend Module Option for displaying Record-Information
- [BUGFIX] Corrected Exception return on Database FieldValues
- [TASK] Changed sorting of fields in backend to newest(uid) = top

**1.0.3**

- [BUGFIX] Fixed FieldValues Creation
- [BUGFIX] Removed column 'internal_position'
- [BUGFIX] Deleting Records in DataViewer-Backend-Module
- [DOC] Documentation changes

**1.0.2**

- [BUGFIX] Fixed Fieldtype-Icons Path
- [DOC] Documentation changes

**1.0.1**

- [DOC] Documentation added

**1.0.0**

- Initial release and upload to TER



**To-Do-/Wish- List**

- Fieldtype for implementing a fixed record (Visible or Hidden in Backend)
- Different Data-Sources for a Datatype/or Field (Webservice, XML, External Database)
- Fluid Field as UserFunc for displayCond compatibility
- (Filter with direct record selection)
- Ajax Autocomplete functionality
- Field for merging different record values (e.g. search)
- Change Record DataHandler to use RecordFactory
- Provide Delete link for records in ToolbarItem
- Full Workspaces support
- Record Validation as separate validation class
