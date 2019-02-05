
### Solarium PHP Solr client

Solarium is a Solr client library for PHP. It is developed with these goals in mind:

* Releave developers of the ‘raw communication’ with Solr, ie. setting params, building strings, hiding all this with an easy to use API, allowing you to focus on business logic.
* Allow for reuse, for instance a query can be extended to modify it
* Be flexible. For instance the query and result models are not tied to a specific Solr client implementation. There are multiple Solr Client adapters for Solr communication. All models can be extended by your own implementation if needed and a plugin system is available.
* Be usable in any PHP application. No dependencies on other frameworks. Solarium tries to follow the Symfony 2 standard and integrates nicely with SF2, but doesn’t in rely on it. You can use Solarium just as easily in Zend Framework or any other PHP framework.
* Accurately model Solr. For instance the updating of a Solr index. Most clients have separate add, delete and commit methods that also issue separate requests. But Solr actually has an update handler that supports all those actions in a single request. The model should reflect this while keeping it easy to use.
* Find a good balance between nice and feature-rich code and performance. A library/framework for general use will never be as fast as a custom implementation that only contains the bare minimum for your use case. But the performance difference between the two should be at a reasonable level. And because of the dynamic nature of PHP the models can’t be too extensive, yet they should not be over-simplified.
* Only implement basic functionality in the standard models. All additional functionality should be in separate code that only gets loaded when used. This benefits performance, but also helps to prevent classes with huge APIs. The query components and plugins are a good example.

### Quick start using Composer

If you are familiar with Composer and have it set up in your project just issue the following command in your project root directory:

```
$ composer require solarium/solarium
```

For more info see the 'Getting started' section in the docs.

### Documentation

You can find a lot of documentation, including a 'Getting started' section here: http://solarium.readthedocs.org/en/stable/

### Contributing

Any help on Solarium is very welcome. Please take a look at the pointers below, but your PR is always welcome. If there are any issues someone might be able to help you or continue working on the PR.

* Your pull requests should target the develop branch, not master. Nothing will be directly merged into master!
* A pull request should be mergeable (fast-forward) 
* Ideally any change should include updated or new unittests to cover the changes and updates to the docs. You can submit a PR without tests / docs, but it might take longer to merge as someone else will need to fix this.
* Solarium follows the Symfony2 code standards: http://symfony.com/doc/current/contributing/code/standards.html
* Each PR will be checked by the CI environment automatically. Ofcourse anything other than a 'green' status needs to be fixed before a PR can be merged.

### Bug reports

Solarium uses the Github issues for tracking bugs and feature requests: https://github.com/solariumphp/solarium/issues. Please keep the following in mind when creating a bug report:

* Bugs are intended for problems in the code or missing / faulty documentation. Not for issues with your own environment, questions in how to use feature X etcetera. For these questions you can use platforms like StackOverflow.
* Include info about your environment: the version of Solarium you are using, PHP version, Solr version
If you get a specific error, include as much info as possible. The PHP exception, a Solr error log line, anything that might help.
* When something doesn't work as expected for you, also describe the behaviour you expect.
* Do a quick search to check if the issue has already been reported
* Describe your issue well, especially the title. Instead of ‘Select query exception’ use ‘Using a dash in a filterquery tag causes an exception’.
* Provide steps to reproduce the issue. A unittest is ideal, but a description of manual steps is also very helpful.
