# Upgrade from 1.1.9 to 1.1.10

## Doctrine Annotations

Instead of simple annotations, wheras parsing them is not bound to the apropriate use statement, up from version 1.1.10 all 
annotations of the apperver-io-psr/epb, appserver-io-psr/servlet and appserver-io/routlt libraries are using the Doctrine
Annotations library instead. Therefore it is necessary to add use statements, for all annotations of that libraries, to your
application's classes.

## Annotation Libraries

Annotations Libraries **MUST** not longer be registered with the `persistence.xml` file, instead they have to be registered
in the `META-INF/context.xml` file of your application. This is necessary, as we use Doctrine Annotations for annotation 
parsing also.