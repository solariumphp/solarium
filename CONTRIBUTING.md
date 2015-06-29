# Contributing to Solarium

So you consider contributing to Solarium? That’s great! 
Here are some pointers to hopefully get a good result.

If you are uncertain about any part or need help please feel free to ask for help.

## Bug reports

* Bugs are intended for problems in the code or missing / faulty documentation. Not for issues with your own environment, questions in how to use feature X etcetera.
* Include info about your environment: the version of Solarium you are using, PHP version, Solr version
* If you get a specific error, include as much info as possible. The PHP exception, a Solr error log line, etcetera.
* When something doesn't work as expected for you, also describe the behaviour you expect.
* Do a quick search to check if the issue has already been reported
* Describe your issue well, especially the title. Instead of ‘Select query exception’ use ‘Using a dash in a filterquery tag causes an exception’.
* Provide steps to reproduce the issue. A unittest is ideal, but a description of manual steps is also very helpful.

## Pull requests

* Your pull requests should target the develop branch, not master. Nothing will be directly merged into master!
* A pull request should be mergeable (fast-forward) if not, you will be asked to update it.
* Ideally any change should include updated or new unittests to cover the changes. You can submit a PR without tests, but it will take longer to merge as someone else will need to fix the test coverage.
* Solarium follows the Symfony2 code standards: http://symfony.com/doc/current/contributing/code/standards.html
* Each PR will be checked by the CI environment automatically. Ofcourse anything other than a 'green' status needs to be fixed before a PR can be merged.
