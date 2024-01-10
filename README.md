### Changes

- **Adding remember_token in accounts tables**
- **Make nullable did_id column in camp table**
- **Remove did_id column from campaign table**
- **Create table campaign_dids table with id, campaign_id and did_id**
- **Rename campid to campaign_id in table target**
- **make null modifieddate in table target**
- **make campaignname null in table target**

***table Campaign***
- **convert customer_id into bigint**
- **convert calltimeout, ringtimeout into int**
- **convert threading, active into tinyint**
- **convert camp-mode into smallint or int**

***table call_block***
- **convert customer_id into bigint**
- **convert status into tinyint**

