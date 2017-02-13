.. include:: ../../Includes.txt

.. _fluid:

.. image:: ../../Images/Fieldtype/fluid.gif
   :align: left

Fluid
--------

This field can contain fluid and/or html code that will be displayed in the backend and is also rendered in the frontend.


.. note::

    Since 1.3.1, this field can also be used to combine different record parts as a single computed value.
    
    For example, if you are having an address record with 'title', 'firstname' and 'lastname', these values can be combined to
    a fluid field 'fullname' which generates a full name out of the record parts. This fluid field would have something like
    
    ``{record.title.value} {record.firstname.value} {record.lastname.value}``

.. note::

    Since 1.6.0, the output can be rendered to the database. This helps you to generate custom content
    or create search strings for the search/filter field.
    


Screenshot
~~~~~~~~~~

.. image:: ../../Images/fieldtype_fluid.jpg

Additional Configuration Options
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Show in Backend
   Shows the rendered output in the backend
   
Generated Output as Record Value to the Database
	If this checkbox is marked, the output of this field is rendered to the database and
	is used as the value content for the field. 

Variable Injection
	You can inject variables to the fluid view to access different variables when the 
	output is rendered.







 
