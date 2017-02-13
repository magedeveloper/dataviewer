.. include:: ../../Includes.txt

.. _select:

.. image:: ../../Images/Fieldtype/user.gif
   :align: left

UserFunc
--------

This is the most powerfull field, because you are free with everything you want
to do. Parameters can be passend through the Fieldtype Configuration to deliver
fixed values aswell as template variables.

.. note::

    It is recommended, that you only use the key 'parameters' of the first configuration
    argument in the UserFunc Method because the other information changes between
    frontend and backend. For additional information see the 'Important Information'
    section in the field configuration.


Additional Configuration Options
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

userFunc
   The class path of the userFunc. Written like 
   ``VendorName\ExtensionName\UserFunc\YourUserFunc->userFuncMethod``

Parameters
   You can attach parameters to your userFunc by adding them in this 
   configuration. You can also use fluid code in the Value-Input to
   attach template variables or manipulated information.





 
