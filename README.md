# dm-zoho-crm-wrapper
A custom wrapper around the ZCRM PHP SDK

* [Version History](docs/VersionHistory.md)


## Pushing to Packagist.
- Make sure everything is pushed to GitHub.
- git tag 1.**
- git push --tags



## Triggering CRM Workflows
-  must be triggered by the createRecords / updateRecords methods explicitly. See details here: https://help.zoho.com/portal/community/topic/zoho-crm-api-v2-not-triggering-workflows


Used by:
- Product views
- Foss Report


## Tests to be written
Quote:
- Quote::get() always returns dm_account_id, dm_contact_id, dm_potential_name (and dm_potential_id)