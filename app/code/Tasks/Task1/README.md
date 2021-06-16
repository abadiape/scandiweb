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
