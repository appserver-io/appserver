---
layout: tutorial
title: Building WebApps with AngularJS and appserver.io
description: A guide how to build a single page app with AngularJS and appserver.io
date: 2015-02-13 14:45:00
author: zelgerj
position: 5
group: Tutorials
subNav:
  - title: Prerequirements
    href: prerequirements
  - title: Preparations
    href: preparations
  - title: Login Form
    href: login-form
  - title: Frontend Authentication
    href: frontend-authentication
  - title: RESTful Service
    href: restful-service
  - title: Input Validation
    href: input-validation
  - title: That's it!
    href: that's-it!
permalink: /get-started/tutorials/building-webapps-using-angular-and-appserver-io.html
---
![Building WebApps with AngularJS and appserver.io]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/angular_and_appserver.jpg" | prepend: site.baseurl }})
***

This tutorial shows how to build a webapp using AngularJS as a single page app in the frontend and **appserver.io** as
a RESTful service in the backend using **Servlets**, **Dependency-Injection**, **AOP** and **Annotated-Validation**.

<br/>
**Prerequisite**: *Your system should be well prepared for Javascript, HTML, and CSS/SASS development.
We will generate an AngularJS app using [Yeoman](http://yeoman.io), that allows to kickstart an AngularJS app,
prescribing best practices and tools to help you stay productive*
So please check out and follow the [Instructions](http://yeoman.io/codelab/setup.html) at Yeoman guide to setup your
system correctly.

You will need a running installation of **appserver.io** *(>= Version 1.0.0)*. If you are new to this
project you can easily [download](http://127.0.0.1:4000/get-started/downloads.html) and follow the
[installation guide](http://127.0.0.1:4000/get-started/documentation/installation.html) for your specific OS.


<br/>
## Preparations

At first switch your local **appserver.io** installation to *dev mode*. This will set the correct filesystem
permissions for your user account and also let the **appserver** process itself run as a current user that makes it a lot easier for local development.

```bash
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```

Now you are ready to create our webapp called `myapp`

```bash
# Goto **appserver.io** webapps folder
cd /opt/appserver/webapps/

# Create myapp
mkdir myapp

# Go into myapp
cd myapp

# Open it with your favorite editor if you want to by doing...
pstorm .
wstorm .
brackets .
atom .
```

To start the AngularJS app via Yeoman, you need the correct yeoman generator installed globally first.

```bash
sudo npm install -g generator-angular
```

Start your AngularJS app right under our webapp folder ```/opt/appserver/webapps/myapp```. Decide if you want to use Sass or include Bootstrap. Just hit enter for default values.

```bash
yo angular
# Hit enter for any questions
```

Before you can open our webapp in the browser please add some Virtual-Hosts to the **appserver** configuration. Do so
by opening `/opt/appserver/et/conf.d/virtual-hosts.xml` with your favorite editor and add this:

```xml
<virtualHost name="myapp.dist">
    <params>
        <param name="documentRoot" type="string">webapps/myapp/dist</param>
    </params>
</virtualHost>

<virtualHost name="myapp.dev">
    <params>
        <param name="documentRoot" type="string">webapps/myapp</param>
    </params>
    <rewrites>
        <rewrite condition="-f{OR}.*\.do.*" target="" flag="L" />
        <rewrite condition="^/(.*\.css)$" target="/.tmp/$1" flag="L" />
        <rewrite condition="^/(.*)$" target="/app/$1" flag="L" />
    </rewrites>
</virtualHost>
```

Add those hostnames to your `/etc/hosts` file to associate it with your local IP
address.

```
127.0.0.1   myapp.dev myapp.dist
::1         myapp.dev myapp.dist
```

Now restart the **appserver** and build the kickstarted AngularJS app by just calling grunt in our webapps folder `/opt/appserver/webapps/myapp`

```bash
# OSX
sudo /opt/appserver/sbin/appserverctl restart

# Debian / Ubuntu / CentOS
sudo /etc/init.d/appserver restart

# Fedora
sudo systemctl restart appserver

cd /opt/appserver/webapps/myapp
grunt
```

Open [http://myapp.dist:9080] in your browser and it should look like this.

![AngularJS appserver.io dist start]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-start-dist.png" | prepend: site.baseurl }} "AngularJS appserver.io dist start")

Does it look awesome... :)

If you use `grunt` or the similar `grunt build` command, grunt will build the app into a subdirectory called `dist`,
where everything has been optimized (concatenated, uglified etc...) for production usage.

For local development, it is highly recommended to use the `grunt watch` command that observes all the javascript app
files and builds it automatically. If anything has been changed without uglifing or doing other optimizations, so you are
still able to debug your app. That is the reason why we configured the Virtual-Host `myapp.dev`, where you can reach the
debuggable version of your javascript app. So let's try it by typing...

```bash
grunt watch
```

Open [http://myapp.dev:9080] in your browser and it should look like as shown above in the dist screenshot.

Cool... everything is fine! Ready for take off? :)

<br/>
## Login Form

Now enhance the AngularJS app by adding a login form that will make use of an Authentication-Service on the
backend side, which we'll implement later on. First step is to create a new route `login` vi Yeoman by doing:

```bash
yo angular:route login
```

This creates a controller including its view template and adds the route configuration to your app which can be
found under `app/scripts/app.js`. Now we've to add a link to the new `login` route at the app navigation by
editing the `app/index.html` file. Find the line where it's says `<ul class="nav navbar-nav">` and add a new `<li>`
element as last one:

```html
<li><a ng-href="#/login">Login</a></li>
```

Refresh your browser at [http://myapp.dev:9080] and click on the new `login` navigation element.

![AngularJS appserver.io login route]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-route.png" | prepend: site.baseurl }} "AngularJS appserver.io login route")

Cool... the route is reachable. Now add a login form by editing the login template located in `app/views/login.html`.

```html
<form name="loginForm" ng-controller="LoginCtrl"
    ng-submit="login(credentials)" novalidate>
    <h2 class="form-signin-heading">Please sign in</h2>
    <label for="username" class="sr-only">Username</label>
    <input type="text" id="username" class="form-control"
           placeholder="Username" required="" autofocus=""
           ng-model="credentials.username">
    <label for="password" class="sr-only">Password</label>
    <input type="password" id="password" class="form-control"
           placeholder="Password" required=""
           ng-model="credentials.password">
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    <p><br/></p>
</form>
```

Refresh your browser and click on the `Login` Button located at the navigation.

![AngularJS appserver.io login form]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-form.png" | prepend: site.baseurl }} "AngularJS appserver.io login form")

For being able to submit the login form, you will need a backend as well as a frontend implementation of an `AuthService`.

<br/>
## Frontend Authentication

Let us start building a simple `AuthService` in AngularJS by kickstarting the service easily via Yeoman...

```bash
yo angular:service AuthService
```

This generates the service implementation file `app/scripts/services/authservice.js` and adds it automatically to the
script includes section of `app/index.html`. Open the service file and edit it:

```js
angular.module('myappApp')
  .service('AuthService', function ($http, Session) {
    var login = function (credentials) {
      return $http
        .post('/login.do', credentials)
        .then(function (res) {
            Session.create(res.data.id, res.data.username);
            return res.data.username;
        });
    };
    var isAuthenticated = function () {
      return !!Session.id;
    };
    return {
      login: login,
      isAuthenticated: isAuthenticated
    };
  });
```

As we are using a `Session` singleton object here in the login method to keep the user’s session information, we have
to kickstart it via Yeoman too:

```bash
yo angular:service Session
```

Just open that generated `Session` singleton located at `app/scripts/services/session.js` and add simple
functionality like `create` and `destroy` as shown below:

```js
angular.module('myappApp')
  .service('Session', function () {
    this.create = function (id, username) {
      this.id = id;
      this.username = username;
    };
    this.destroy = function () {
      this.id = null;
      this.username = null;
    };
    return this;
  });
```

To make use of our simple `AuthService` we have to inject it in the login controller and add the `login` method to the
scope which is used by the login form via `ng-submit="login(credentials)"`. So, open `app/scripts/controllers/login.js`
and add let it look like...

```js
angular.module('myappApp')
  .controller('LoginCtrl', function ($scope, $location, AuthService) {
    $scope.credentials = {
      username: '',
      password: ''
    };
    $scope.login = function (credentials) {
      AuthService.login(credentials).then(function (username) {
        $scope.setErrorMessage(null);
        $scope.setCurrentUsername(username);
        $location.path('/');
      }, function (response) {
        $scope.setErrorMessage(response.data.error.message);
      });
    };
  });
```

For any global application logic like `$scope.setCurrentUsername(username);` or to know if someone is authenticated
and has a Session-Id we need to create another controller called `app`. Create it with yeoman...

```bash
yo angular:controller app
```

... and add some global functionality to it `app/scripts/controllers/app.js`

```js
angular.module('myappApp')
  .controller('AppCtrl', function ($scope, AuthService) {
    $scope.currentUser = null;
    $scope.isAuthenticated = AuthService.isAuthenticated;
    $scope.setErrorMessage = function (message) {
      $scope.errorMessage = message;
    };
    $scope.setCurrentUsername = function (username) {
      $scope.currentUsername = username;
    };
  });
```

Finally we want something to be happen if the user has authenticated. First let's hide the `Login` navigation element
by opening `app/index.html` where we added the login button at the beginning and modify it this way.

```html
<li ng-if="!isAuthenticated()"><a ng-href="#/login">Login</a></li>
```

It would be nice if the current username will be present at top top navigation as well, so just get into `app/index.html`
again add a welcome paragraph just before the `<ul class="nav navbar-nav">`.

```html
...
<p ng-if="isAuthenticated()" class="navbar-text"><span class="welcome">Logged in as <b>{% raw %}{{ currentUsername }}{% endraw %}</b></span></p>
```

All error messages should also be displayed. In `app/index.html` search for `<div ng-view=""></div>` and add this
before and right after the main container div `<div class="container">`...

```html
<div ng-if="errorMessage" class="alert alert-danger alert-error">
  <span class="close" ng-click="setErrorMessage(null)">&times;</span> 
  <strong>Error!</strong><div ng-bind-html="errorMessage"></div>
</div>
```


Until we can test our frontend auth mechanism we have to implement the backend `AuthService` as well.

> If you want to get more information about authentication techniques in AngularJS applications just check out this
[Link](https://medium.com/opinionated-angularjs/techniques-for-authentication-in-angularjs-applications-7bbf0346acec)
where you can find a collection of ideas for authentication and access control.

<br/>
## RESTful Service

Here is where the **appserver** comes into place. Make use of the **Servlet-Engine** and **Dependency-Injection**
as we did in the [My First WebApp](<{{ "/get-started/tutorials/my-first-webapp.html" | prepend: site.baseurl }}>)
for providing as [Service-Oriented architecture](http://en.wikipedia.org/wiki/Service-oriented_architecture).
Also take advantage of **AOP** for the need of building a RESTful service api based on json format to keep it solid.

Start implementing the `AuthService` by creating it `META-INF/classes/MyVendor/MyApp/AuthService.php` and
implement some simple auth functionality with hardcoded valid credentials, which can of course easily be replaced using
a CredentialProvider if you want to enhance the tutorial later on.

```php
<?php

namespace MyVendor\MyApp;

/**
 * @Stateless
 * @Processing("exception")
 */
class AuthService
{
    protected $credentials = array(
        'admin' => 'admin',
        'user'  => 'pass',
        'guest' => 'guest'
    );
    protected $username;
    protected $password;

    protected function setUsername($username)
    {
        $this->username = $username;
    }

    protected function setPassword($password)
    {
        $this->password = $password;
    }

    protected function auth()
    {
        if (isset($this->credentials[$this->username])
        && ($this->credentials[$this->username] === $this->password)) {
            return $this->username;
        }
        throw new \Exception('Username or Password invalid', 401);
    }

    public function login($credentials)
    {
        $this->setUsername($credentials->username);
        $this->setPassword($credentials->password);
        return $this->auth();
    }
}
```

Next is a Servlet `WEB-INF/classes/MyVendor/MyApp/LoginServlet.php` which listens to `http://myapp.dev:9080/login.do`
where our AngularJS app `AuthService` is connected to. Inject the `AuthService` and implement the `doPost`
method since there will only be credentials sent via HTTP-Post Method.

```php
<?php

namespace MyVendor\MyApp;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * @Route(name="login", urlPattern={"/login.do", "/login.do*"}, initParams={})
 */
class LoginServlet extends HttpServlet
{
    /**
     * @EnterpriseBean(name="AuthService")
     */
    protected $authService;

    /**
     * @param HttpServletRequestInterface $servletRequest
     * @param HttpServletResponseInterface $servletResponse
     */
    public function doPost(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {
        $username = $this->authService->login($this->data);
        $session = $servletRequest->getSession(true);
        $session->start();

        return array(
            'id' => $session->getId(),
            'username' => $username
        );
    }
}
```

Ok, looks good... but how does it work without `json_encode` the returned array and where is the `$this->data`
property created from? This can easily be done by using one of the powerful features the **appserver** comes with. It's
called AOP or [Aspect-oriented programming](http://en.wikipedia.org/wiki/Aspect-oriented_programming). Just click on the
link if you are not familiar with it.

All we have to do is to introduce an `JsonHandlingAspect` class which is annotated with `@Aspect` and registers a
pointcut triggered by `do*()` methods like `doGet()` or `doPost()` for all Servlet-Classes found in the
`\MyVendor\MyApp` namespace. This pointcut will be used by an Around-Advices which wraps around the actual method logic.

For our `JsonHandlingAspect` example we will validate the requests body content if it can be decoded by using the
`json_decode` function and set the decoded json object in the `data` property of the servlet instance. The actual
return value of the servlet's `do*` methods will be automatically encoded to valid json strings via `json_encode`
and appended to the response body stream. The same will happen if an exception is thrown in any service business logic
used by the servlet with the addition that the response status code is filled with the code provided by the
exception and it's message is sent via an error json format.

Do so by creating `META-INF/classes/MyVendor/MyApp/JsonHandlingAspect.php` and implementing it with...

```php
<?php

namespace MyVendor\MyApp;

use AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface;

/**
 * @Aspect
 */
class JsonHandlingAspect
{
    /**
     * @Pointcut("call(\MyVendor\MyApp\*Servlet->do*())")
     */
    public function allServletDoMethods() {}

    /**
     * @Around("pointcut(allServletDoMethods())")
     */
    public function jsonHandlingAdvice(MethodInvocationInterface $methodInvocation)
    {
        // get servlet method params to local refs
        $parameters = $methodInvocation->getParameters();
        $servletRequest = $parameters['servletRequest'];
        $servletResponse = $parameters['servletResponse'];

        // try to handle request processing
        try {
            // only if request has valid json
            if (!is_object(json_decode($servletRequest->getBodyContent()))) {
                throw new \Exception('Invalid request format', 400);
            }
            // set json parsed object into data property of servlet object
            $methodInvocation->getContext()->data = json_decode(
                $servletRequest->getBodyContent()
            );
            // call orig function
            $responseJsonObject = $methodInvocation->proceed();
        } catch(\Exception $e) {
            $servletResponse->setStatusCode(
                $e->getCode() ? $e->getCode() : 400
            );
            // create error json response object
            $responseJsonObject = new \stdClass();
            $responseJsonObject->error = new \stdClass();
            $responseJsonObject->error->message = nl2br($e->getMessage());
        }
        // add json encoded string to response body stream
        $servletResponse->appendBodyStream(json_encode($responseJsonObject));
    }
}
```

I hope the inline comments are good enough to understand whats going on. You may also checkout our
[AOP Documentation Section](<{{ "/get-started/documentation/aop.html" | prepend: site.baseurl }}>)
if you want to get more details about AOP within the appserver.

Let's give it a try if that works! :) Restart the **appserver** and do a browser-refresh at [http://myapp.dev:9080].

> If the browser can not connect to the **appserver** you better check any **appserver** log files which are located at
`/opt/appserver/var/log` for any errors.

You should see the app still unchanged if everything went fine. Now just click the `Login` Button and sign in using
any valid credentials like `admin/admin`, `guest/guest` or `user/pass`.

![AngularJS appserver.io login success]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-success.png" | prepend: site.baseurl }} "AngularJS appserver.io login success")

If the `Login` Button has disappeared and a welcome paragraph is showing `Logged in as {username}` everything works as
expected! Please also check if invalid credentials will bring up the error message box.

![AngularJS appserver.io login error]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-error.png" | prepend: site.baseurl }} "AngularJS appserver.io login error")

<br/>
## Input Validation

Imagine if you could easily add input validation of client-side form data via annotation using the most awesome
validation engine ever created for PHP [Respect\Validation](https://github.com/Respect/Validation)... Sounds great?
Works great! :)

Let's say we wanna validate that the username field value of your login form is not an email address format and the
password field value is not allowed to be empty. All we have to do is add the following annotations to the `setUsername`
and `setPassword` methods of our `AuthService` and introduce `Respect\Validation\Validator` as `v` via use-statement.

```php
<?php
...
use Respect\Validation\Validator as v;
...
    /**
     * @Requires(type="RespectValidation", constraint="v::not(v::email()->setName('Username'))->check($username)")
     */
    protected function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @Requires(type="RespectValidation", constraint="v::notEmpty()->setName('Password')->check($password)")
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }
```

Restart the **appserver** and check it out...

![AngularJS appserver.io login validation username]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-validation-username.png" | prepend: site.baseurl }} "AngularJS appserver.io login validation username")
![AngularJS appserver.io login validation password]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/yo-angular-login-validation-password.png" | prepend: site.baseurl }} "AngularJS appserver.io login validation password")


<br/>
## Done!

We hope you enjoyed this tutorial, and it helps for a quick overview how easy it is to create a RESTful service
backend by using great features like **Servlets**, **Dependency-Injection**, **AOP** and **Annotated-Validation** that
appserver provides out of the box. Even when there is no need to use a specific framework as the **appserver** is not only a
powerful PHP infrastructure, but also a fully featured enterprise solution for PHP.

Every feedback is appreciated so please do not hesitate to share experiences or any issue you may encounter with us.
Cheers! :)
