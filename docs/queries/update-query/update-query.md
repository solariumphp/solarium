Update queries allow you to add, delete, commit, optimize and rollback commands. For all the details about the Solr update handler please see the Solr documentation, but some important notes:

-   Solr has no 'update' command. But if you add a document with a value for the 'unique key' field that already exists in the index that existing document will be overwritten by your new document.
-   You can only add complete documents. If you want to update only a single field you still need to 'add' the whole document.
-   Always use a database or other persistent storage as the source for building documents to add. Don't be tempted to emulate an update command by selecting a document, altering it and adding it. Almost all schemas will have fields that are indexed and not stored. You will loose the data in those fields.
-   The best way to use update queries is also related to your Solr config. If you are for instance using the autocommit feature of Solr you probably don't want to use a commit command in your update queries. Make sure you know the configuration details of the Solr core you use.

