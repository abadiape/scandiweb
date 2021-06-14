# scandiweb
Tasks for M2 test

Task #1 - Only BE

The client has a multi-site setup with some CMS pages that are shared across different
websites. The problem that they are having is that this is causing duplicate content issues and
affecting their SEO rankings.
To counter this we will create a new module that will do the following:
1. Add a block to the head;
2. The block should be able to read the CMS page’s id and to check if the page is used on
multiple store views/websites;
3. If so it should add a hreflang meta tag to the head;
4. If the meta tag is displayed - it should display language of the store, like “en-gb”, “en-us”,
etc. As metatag should have specific values for each country;
5. Support the fact that each store should have a different language pair.

The structure of the meta tag is as follows -
<link rel="alternate" hreflang="' . $storeLanguage . '" href="' . $baseUrl . $cmsPageUrl . '" />

The expected outcome in the sample
The example below assumes the following:
There are 2 websites set up within Magento, a UK one and a US one.
The UK language is set to en-gb and the US site is set to en-us.
The UK base URL is https://www.scandiweb.co.uk
The US base URL is https://www.scandiweb.com
If there is a CMS page for "about-us" and this is assigned to both websites when the page loads
the new block in the head will add the following meta tags -
<link rel="alternate" hreflang=“en-gb" href="https://scandiweb.co.uk/about-us'" />
<link rel="alternate" hreflang=“en-us" href="https://scandiweb.com/about-us'" />
You need to make sure the code will work with any number of stores within a Magento
installation, as long as the store has a custom language set and it adds a meta tag for each of
the stores that the CMS page is assigned to.
You can use any Magento 2 installation that you have as long as there are multiple stores setup
on it.
Please also attach the documented installation process and the user with general explanations.
So we will be super sure to understand them correctly.
                                                                             
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
                                                                             
Task #3 - Only FE
                                                                             
You are allowed to go completely nuts here. Just go and hack the
BEST-EVER-LOVED-BY-EVERYONE part in Magento 2 - Mr. Checkout.
You need to -
1. Remove 2 random fields from the Shipping step;
2. All other field names, which should be written vice versa, like Name, should be emaN.
3. And the next step button should redirect back to the cart.

Please document all your changes for step 1. So we will know what was removed.
You can use any Magento 2 installation that you have.
Please also attach the documented installation process and the user with general explanations.
So we will be super sure to understand them correctly.
