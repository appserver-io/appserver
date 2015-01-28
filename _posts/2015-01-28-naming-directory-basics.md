
### Naming Directory
***

Every container running in the application server has a internal registry, we call it Naming Directory. In Java this is call `Enterprise Naming Context` or `ENC`, in short. The naming directory is something like an object store, the container registers references to its resources. Resources can be beans or contexts provided by an application. All that resources are registered in the `Naming Directory` which allows you the access them if needed.

#### Register Resources

When the application server starts, it parses the `META-INF/classes` and `WEB-INF/classes` folders by default to find classes with supported annotations. If a class is found, the class will be registered in the application servers naming directory under the name you specify in the annotations `name` Attribute, in following example `AStatelessSessionBean`. As the `name` attribute is optional, the bean will be registered in the naming directory with the short class name, if not specified.

When you want to inject that bean later, you have to know the name it has been registered with. In the following example, the bean will be registered in the naming directory under `php:global/example/AStatelessSessionBean`, whereas `example` is the name of the application. When using annotations to inject components, you don't have to know the fully qualified name, because the application server knows the actual context, tries to lookup the bean and injects it.
