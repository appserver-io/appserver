---
layout: detail
title: Servlet Engine
position: 10
group: Features
permalink: /products/features/servlet-engine.html
subnavigation: features
---


## <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">The Servlet Engine provides a web container that enables developers to load applications and objects, so-called servlets, on application server startup and hold them persistently in memory. Using servlets allows you to optimize your applications bootstrapping to take place at application servers startup time. As a result, the time to process requests will be significantly lower compared to applications running on the common LAMP stack.
                </td>
            </tr>
            <tr>
                <td><b>Availability</b></td>
                <td>since 1.0.0</td>
            </tr>
            <tr>
                <td><b>Type</b></td>
                <td>Main Module</td>
            </tr>
            <tr>
                <td><b>Dependencies</b></td>
                <td>Appserver.io Runtime, Appserver.io Webserver</td>
            </tr>
            <tr>
                <td><b>PSR</b></td>
                <td><a href="https://github.com/appserver-io-psr/servlet">https://github.com/appserver-io-psr/servlet</a></td>
            </tr>
        </tbody>
    </table>
</div>
<p><br/></p>

## <i class="fa fa-bars"></i> Functions
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Features</b></td>
                <td class="col-md-8">
                    <div class="content content-table">
                        <ul>
                            <li>Persistent servlets over request boundaries</li>
                            <li>Bootstrap servlets on application server start</li>
                            <li>Garbage collection of persistent objects</li>
                            <li>DI support to inject Resources, Session and MessageDriven beans</li>
                            <li>AOP support for servlets</li>
                            <li>Use annotations to configure servlets</li>
                            <li>Override annotations by XML configuration</li>
                            <li>Easy to use session management</li>
                            <li>HTTP basic and digest authentication</li>
                            
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

## <i class="fa fa-edit"></i> Use Case
<p>
During the last years, as PHP tries to advance to the enterprise market, PHP developers have had to face the reality that PHP does not provide an enterprise ready infrastructure like Java. That leads to the situation where PHP developers have to build up huge knowledge about external tools and libraries written in languages they are not familiar with as the application server brings the missing infrastructure to the PHP ecosystem; it enables PHP developers to use the services it provides. Extending and using such services will be much more convenient since they are written in their native language.
</p>
<p>
One part of the infrastructure is the Servlet Engine. Using a Servlet Engine gives developers the possibility to write blazing fast applications optimized that perfectly fit their requirements as it provides single functionality.  The controller part of an application, based on the MVC pattern naturally allows developers to integrate third-party libraries like a template engine, whenever needed.
</p>
<p>
The perfect use case for the Servlet Engine are enterprise level applications, that makes massive usage of the application servers services. That applications can benefit from the Servlet Engines performance, flexibility and simplicity in particular.
</p>
