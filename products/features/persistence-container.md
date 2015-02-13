---
layout: detail
title: Persistence Container
meta_title: appserver.io persitence container
meta_description: The Persistence Container enables developers to hold objects, so-called beans, in memory giving you completely new possibilities in PHP.
position: 10
group: Features
permalink: /products/features/persistence-container.html
subnavigation: features
---

## <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">The Persistence Container enables developers to hold objects, so-called beans, in memory whereby it is possible to define whether a bean will be a singleton, bound to a HTTP session or has no state at all. In addition to that, the Persistence Container allows you to make transparent use of a beans local or remote business interface. This allows application developers to implement independent, loose coupled, and reusable components that can be distributed across a network without the need change any source code.
                </td>
            </tr>
            <tr>
                <td><b>Availability</b></td>
                <td>from 1.0.0</td>
            </tr>
            <tr>
                <td><b>Type</b></td>
                <td>Main Module</td>
            </tr>
            <tr>
                <td><b>Dependencies</b></td>
                <td>Appserver.io Runtime, Appserver.io</td>
            </tr>
            <tr>
                <td><b>PSR</b></td>
                <td><a href="https://github.com/appserver-io-psr/epb">https://github.com/appserver-io-psr/epb</a></td>
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
                            <li>Support for singleton, stateless, stateful and message driven bean types</li>
                            <li>Lifecycle callbacks</li>
                            <li>Local and remote (network) business interfaces</li>
                            <li>DI support to inject Resources, Sessions and MessageDriven beans</li>
                            <li>AOP support for all bean types</li>
                            <li>Use annotations to configure beans</li>
                            <li>Override annotations by XML configuration</li>
                            <li>Supports HTTP for remote calls</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

## <i class="fa fa-edit"></i> Use Case
<p>
Nearly everybody who has been involved in development of an online shop system, neither for the own company or as project for a customer, has been faced the developers question: „How many products will be listed in the catalogue finally?“. The background of that question, in most cases, will be the problem that with a growing number of products the response time will become worse. This problem is partly a result of PHP's “shared nothing” paradigm. This paradigm makes it complicated to hold products, that has been loaded once, in memory and avoid to reload them on every request again and again. A workaround, that has been established in most PHP applications is the heavy usage of cache systems. At first sight, this solves the problem, but having a deeper look at the insights of many applications shows new problems that cache systems introduce. So massive usage of a cache system leads to the need to refresh to cached values, but the selective refreshing of cached data can be very complicated and will lead to critical mistakes in many cases. Using the Persistence Container offers the possibility to load objects into memory, which gives developers high performant access to them and avoids the usage of complicated cache systems in many cases. </p>
