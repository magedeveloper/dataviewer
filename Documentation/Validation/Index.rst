.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _validation:

.. image:: ../Images/logo_dataviewer.png

Validation
----------

Fields can be validated during the save procedure. All available validators can be selected in
the field tab ``Validation`` as showed in the screenshot.

.. image:: ../Images/validation.jpg

Add custom validator
####################

Adding a custom validator is made within the Plugin TypoScript.
The following lines show you how to add a new custom validator.

The validator itself has to implement :php:`\TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface`

.. code-block:: typoscript

	plugin.tx_dataviewer.validators {
		custom {
			validatorClass = Vendor\Extension\Validation\Validator\CustomValidator
			label = LLL:EXT:extension/Resources/Private/Language/locallang.xlf:validator.custom
		}
	}
