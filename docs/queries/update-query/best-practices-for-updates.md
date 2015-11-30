Here are some tips and best practices for using update queries. Some are related to use of the Solarium library, some are general Solr usage tips.

### Combine commands

The update query can issue multiple commands in a single request, even commands of different types. So if you need to execute some deletes and add some documents you can do this in a single request. This is much more efficient than multiple requests.

### Command order

If you combine multiple commands in a single update they will be executed in the exact order they were added. So an add command followed by a delete all query will result in an empty index!

### Combine commands of the same type

If you need to add 15 documents try to use a single add command with 15 documents, instead of 15 separate add commands with a single document. The same goes for multiple deletes.

### Don't build huge update queries

Combining multiple commands into a single update request is efficient, but don't go too far with this. If you build huge update requests you might reach request limits or have other issues. And if any command fails further execution of your commands by Solr will stop, resulting in partially executed update query. This mainly occurs with bulk imports, use the BufferedAdd plugin for bulk imports.

### Use a commit manager

For performance it's important to avoid concurrent Solr commit and optimize commands. You can issue concurrent update queries without commit/optimize commands safely, but you should only do one commit at a time. You can solve this by using a commit manager, a single point for issueing commits that avoids concurrent commits. This can be a manager in your application, but most times the Solr autocommit option is sufficient.

### Don't use rollbacks

If you need to use rollbacks (outside of testing) that usually indicates there is something wrong with your update strategy. Try to find the root cause of the faulty updates, instead of rolling them back.

### Optimizing

While 'optimizing' sounds like it's always a good thing to do, you should use it with care, as it can have a negative performance impact *during the optimize process*. If possible use try to use it outside peak hours / at intervals.
