# About TYPO3 Dataviewer Core Extension

Build easy and intuitive TCA-powered records with customizable backend forms on the fly without any programming knowledge.
You can create datatypes and select from a list of plugins to display your created records.

This extension is a easy to use data structure builder that saves you a lot of time, because there is no more need of creating an extension for
every need. Great solutions can be done by just a few clicks and fluid templating.

You only need one extension to create a lot of solutions.

Having just one extension to maintain gives you the advantage to
improve your workflows when upgrading TYPO3 versions.

Everything is fully compatible with the TYPO3 core.

## Documentation

The documentation is available here:

[https://docs.typo3.org/p/aix/dataviewer/master/en-us/](https://docs.typo3.org/p/aix/dataviewer/master/en-us/)

## Installation

We recommend installing the extension using [Composer](https://getcomposer.org):

    composer require aix/dataviewer

## Compatibility

+ TYPO3 CMS 12.4.X

## Professional

We are also creating a Dataviewer Professional Version, which helps us to provide additional features by
supporting our development. The Dataviewer Professional Extension is in a continuous process of getting
new features such as plugins and more configuration options, to improve your workflows even more.

Addional fields and components are also available for specific usage like

- API Building
- Creating Blog-like Content within your records
- Combining information and building html fields in the TYPO3 Backend
- and many more

Please refer to this url:

[www.aixdesign.net](https://www.aixdesign.net)


## Highlights

+ Create customized records out of datatypes on the fly
+ Dynamic Configuration of the Plugins to get nearly every solution
+ No extension programming needed
+ Inject dynamic variables of different types to your fluid templates
+ Easy fluid templating with intuitive customizable variable naming
+ Backend Toolbar Item for easy record management (Professional)
+ Language support

## Workflow

1. Create Fields for your custom record that you assign later to a datatype
2. Create a datatype and assign the fields, that you've created before.
3. Create your records
4. Create fluid templates for the records. You can create lists or single views.
5. Insert Record-Plugin to your site to display record(s)

## Configuration

Dataviewer has some additional backend TypoScript Configuration possibilities:

### Adding new templates

```
plugin.tx_dataviewer.templates {
    template1 {
        group = Allgemein
        icon = EXT:dataviewer/Resources/Public/Icons/Datatype/animal-dog.png
        name = Template for Testing
        file = EXT:yourtemplateext/Resources/Private/Templates/Dataviewer/TemplateOne.html
    }
}
```

### Extending generated classes

You can extend the generated classes of your datatypes by using the extbase class mapping.

```
config.tx_extbase{
    persistence{
        objects{
            Aix\Dataviewer\Domain\Model\Record\Your\Domain\Model.className = Vendor\Namespace\Domain\Model\Your\Domain\Model
        }
    }
}
```

## Future Roadmap

+ Provide Plugins for Search, Filter, Sorting and Pagination (Professional)
+ Add additional ViewHelpers to customize fluid templating
+ Add possibilities to customize value types
+ Build predefined plugins for your customers