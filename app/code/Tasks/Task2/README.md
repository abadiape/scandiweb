Task #2 - BE + FE

A client wants to change the color of their buttons on a daily basis to attract more customers
and to find the perfect one for their store. They do not want to create a ticket on a daily basis, so
their IT technicians should be able to change everything without any Magento knowledge.

You need to create a Magento console command, which will have HEX color and a Store View
IDs as parameters. And change the color of all buttons on that Store view into the color
provided.

Example of usage

./bin/magneto scandiweb:color-change 000000 1

After running this command in DEV mode all the buttons on the store view with id 1 should be
black.

It is also a good idea to check if the client’s IT guys will not type a non-existing store or wrong
format color.
You can use any Magento 2 installation that you have as long as there are multiple stores set
up on it.
Please also attach the documented installation process and the user with general explanations.
So we will be super sure to understand them correctly.
