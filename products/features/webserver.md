---
layout: detail
title: Webserver
position: 10
group: Features
permalink: /products/features/webserver.html
subnavigation: features
---

## <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">Provides a fully HTTP/1.1 compliant webserver which can process requests over HTTP as well as HTTPS. Basic functionality can be extended by the usage of completely independent, configurable modules interacting with the request/response chain.
The actual request processing can be delegated any given backend following supported standards.</td>
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
                <td>appserver.io Runtime or a similar PHP Thread safe Runtime with pthreads support</td>
            </tr>
            <tr>
                <td><b>PSR</b></td>
                <td><a href="https://github.com/appserver-io-psr/http-message">https://github.com/appserver-io-psr/http-message</a></td>
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
                            <li>Full support for HTTP/1.1 specification</li>
                            <li>Support for TLS/SSL</li>
                            <li>Support for fully configurable Virtual Hosts</li>
                            <li>Support for chainable, configurable and independent modules for request pre- and post-processing </li>
                            <li>Including module for Environment Variables support</li>
                            <li>Including module for native PHP execution</li>
                            <li>Included Rewrite Module</li>
                            <li>FastCGI compliant backend interface</li>
                            <li>Analytics connector for direct traffic post-processing with third party solutions as e.g. Google Analytics</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

## <i class="fa fa-edit"></i> Use Case
<p>
A central part of classical PHP web infrastructure is a web server which delivers static data upon a clients request and helps with the delivery of dynamical content through additional service backends.
The appserver.io ecosystem also offers a web server implementation completely written in PHP which handles these exact same tasks.
</p>
<p>
The appserver.io Webserver is compliant with the HTTP protocol in version 1.0 and 1.1 as a generally used base for client server communication in the web.
It also supports communication via the secure HTTPS protocol by supporting the use of a SSL/TLS certificate which can be configured using the appserver.io Dashboard or the XML based configuration files.
</p>
<p>
To also enhance the abilities of the server it is equipped with a sophisticated chain-like plugin mechanism for modules which independently act upon the request/response processing and allow for further features. 
Most common functionality like rewriting, the access to common backend services processing dynamic content as well as the appserver.io unique Servlet Engine for PHP are delivered with an appserver.io installation. But if it is desired to influence request processing in a way not already present, any additional custom module following provided interfaces can be used to customize the environment without doubling logic in applications.
The used combination of these modules can be freely configured for all webserver instances allowing for a truly flexible environment tailored to customer needs.
</p>