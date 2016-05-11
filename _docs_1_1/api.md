---
layout: docs_1_1
title: API
meta_title: appserver.io API
meta_description: appserver.io comes with a build-in RESTFul JSON API.
position: 45
group: Docs
subNav:
  - title: Functionality
    href: functionality
---

appserver.io up from version 1.1.1 comes with a RESTFul JSON API that can be browsed with the bundled Swagger UI via the URL `http://127.0.0.1:9024/swagger-ui/`. 

The API itself has been implemented as a Web Application using the Servlet Engine and follows the [json:api](<http://jsonapi.org/>) specifiction.

## Availability

The API is deployed in a separate container and, for security reasons, by default not available from the internet, but from the local system only. The default port is `9024`. To make it available, the IP for the responsible server has to be changed from `127.0.0.1` to one, that is reachable from the internet or `0.0.0.0` to listen to all IP addresses bound to the server's network interfaces.

## Functionality

The following functionality is provided by the API.

### Index

The welcome page for the RESTFul JSON API.

### Authentication

Provides method's to login to or logout from the API.

### Naming Directories

Load a list with the available Naming Directories and give access to detail information of a Naming Directory.

### Containers

Load a list with the configured Containers and give access to detail information of a Container.

### Applications

Load a list with the deployed Applications and give access to detail information of an Application. Additionally applications can be deployed or removed from a container either as the application thumbnai can be loaded.

### Datasources

Load a list with the deployed Datasources and give access to detail information of a Datasource.

### Persistence Units

Load a list with the deployed Persistence Units and give access to detail information of a Persistence Unit.