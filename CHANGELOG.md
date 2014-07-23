# Version 0.8.2

## Bugfixes

* Bugfix invalid manager + class loader initialization in ContextNode::merge() method
* Bugfix ComposerClassLoader to allow the usage of autoload_files.php also

## Features

* Replace type hint from InitialContext with ContextInterface in SplClassLoader::__construct()
* Add SplClassLoader::get() factory method to allow declarative initialization in application context
* Refactoring SplClassLoader::getIncludePath() to allow pass additional include paths to constructor