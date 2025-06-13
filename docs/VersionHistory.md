# Version History

## 1.0.24 (released 2025-06-13)
* Triggering workflows when creating a new record

## 1.0.23 (released 2025-06-13)
* Added Record::delete() method

## 1.0.22 (released 2025-06-02)
* Calculating Quote total and total_after_discount of Quote load, search and list

## 1.0.21 (released 2024-09-03)
* Copied the updateWithoutWf() method to ZohoCRMModules

## 1.0.20 (released 2024-05-14)
* Added missing return statement

## 1.0.19 (released 2024-05-14)
* Skipped version 1.0.18 to allow push to packagist

## 1.0.18 (released 2024-05-14)
* Account extracts the 'dm_price_list_id' property

## 1.0.17 (released 2021-02-26)
* Fixed exception handling

....

## 1.0.14 (released 2021-02-26)
* A quick cleanup allowing search to work

##  1.0.13 (released 2020-12-11)
* Copied RMAManagement and Vendor models from the RMA repo.
* Copied LayoutFieldsExtractor helper and several functions in the Lead model from Zoho Forms

##  1.0.12 (released 2020-12-10)
* Made sure the Quote::get() always includes the dm_contact_id value.

##  1.0.11 (released 2020-12-09)
* Added zohocrm/php-sdk-archive as dependency. Looks like Composer ignores this.