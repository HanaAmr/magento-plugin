# Gameball Magento Plugin
> This Gameball Magento Plugin allows stores on magento platform to integrate with gameball by requiring its package using composer 

## Table of contents
* [Extension installation](#extension-installation)
* [How to see the results](#how-to-see-the-results)

## Extension installation 
Run the following commands inside the root directory of magento to install the extension.
1. ` composer require gameball/magento-plugin`
2. ` php bin/magento setup:upgrade`
3. ` php bin/magento setup:static-content:deploy -f`

## How to see the results
Go to the backend in magento's admin panel, then navigate to Stores -> Configurations -> Customers
You should find Gameball listed in the side menu


