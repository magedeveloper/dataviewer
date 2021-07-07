# Attention!

Extension 'dataviewer' is outdated. Please refer to the new successor ["TypoTonic"](https://github.com/typotonic/tonic)


# About TYPO3 DataViewer Extension

Build easy and intuitive TCA-powered records with customizable backend forms on the fly without having to make a new extension. 
Records and Variables are dynamically injected to fluid templates for easy usage.

This extension is a easy to use data handler that saves you a lot of time, because there is no more need of creating an extension for
every need. Great solutions can be done by just a few clicks and fluid templating.

### Documentation
For [documentation have a look at the TYPO3 Docs Page](https://docs.typo3.org/typo3cms/extensions/dataviewer/) or refer
to the [DataViewer Extension Page in the TER](https://typo3.org/extensions/repository/view/dataviewer)

## Compatibility

+ TYPO3 CMS 8.7.X

For the 7 LTS compatibile version, please use the master-7/dev-7 branch.

## Highlights
+ Create customized records on the fly
+ Plugins for List, Detail, Sort, Filter, Pagination, Selection and Search
+ Dynamic Configuration of the Plugins to get nearly every solution
+ No extension programming needed
+ Inject records to other extensions with a InjectViewHelper
+ Inject dynamic variables of different types to the fluid templates
+ Easy fluid templating with intuitive variable naming
+ Signal/Slot Usage for manipulating data if needed
+ Backend Toolbar Item for easy record management
+ Import CSV Files

## Workflow

1. Create Fields for your custom record that you will assign later to a datatype
2. Create a datatype and assign the fields, that you've created before.
3. Create your records
4. Create fluid templates for the records. You can create lists or single views.
5. Insert Record-Plugin to your site to display record(s)
6. Add other plugins like Filter, Search or Sorting to your site
