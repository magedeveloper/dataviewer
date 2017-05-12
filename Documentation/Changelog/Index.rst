.. _changelog:

.. image:: ../Images/logo_dataviewer.png

.. note::
        If you like the Extension and want to support the development, please `donate now`_.
        
        .. _donate now: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQP7AJZXJEWMQ&item_name=DataViewer-Support


Changelog
---------

**2.0.0**

- [TASK] Merged from Github Branch master-7 -> 1.7.5
- [TASK] Compatibilty to TYPO3 8 LTS
- [TASK] Multiselect Configuration now with different render types
- [TASK] Better handling for the FormDataProvider 'PrepareDataviewerTca'
- [BUGFIX] Deleting Records is now working correctly
- [BUGFIX] Fallback when no storage pids are selected in INLINE Field
- [BUGFIX] Installed extension without configuration caused errors in backend
- [BUGFIX] DataViewer Logo in Toolbar Item was not shown under certain circumstances
- [TASK] Rewriting uploadFolder values with correct structure
- [TASK] Variables can now also created directly in the Record Plugin
- [TASK] Escaping the value in filtering
- [TASK] Remove deprecations for TYPO3 9
- [TASK] Using ConnectionPool instead of GLOBALS['TYPO3_DB']





**To-Do-/Wish- List**

- Different Data-Sources for a Datatype/or Field (Webservice, XML, External Database)
- Record Injector Service for Extensions
- Access Rights for Datatypes
- Fluid Field as UserFunc for displayCond compatibility
- (Filter with direct record selection)
- Ajax Autocomplete functionality
- Change Record DataHandler to use RecordFactory
- Include MagicSuggest Into Filter Fields
- Full Workspaces support
- Fluid Fieldtype - Selectable Template File
- Record Validation as separate validation class
- FieldValue Type for different data sources
- Add Records to other external records (e.g. a fe_user gets an additional tab with the form)
- Generators, that run, when a record is saved to modify the information that is stored
- Force Variable Type (e.g. int,bool) of Template Variable 'GET'
