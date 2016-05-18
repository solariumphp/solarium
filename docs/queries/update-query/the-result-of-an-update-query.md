The result of an update has two result values (both reported by Solr in the response):

-   status: Solr status code. This is not the HTTP status code! The normal value for success is 0.
-   querytime: Solr index query time. This doesn't include things like the HTTP responsetime.

By default 'omitHeader' is enabled so these results are missing. If you need the status and/or querytime you can call setOmitHeader(false) on your update query before execution.

In case of an error an exception will be thrown.
