---
layout: detail
title: Message Queue
position: 10
group: Products
permalink: /products/features/message-queue.html
subnavigation: features
author: all
---


### <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">The Message Queue provides services that enables developers to process messages asynchronously. Therefore messages, which are plain PHP objects, are stacked on the queue whereas receivers, represented by MessageDriven beans, consumes them in a chronological order. By prioritizing messages, software developers are enabled to optimize a system for optimal resource utilization 
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
                <td>Appserver.io Runtime, Appserver.io, Persistence Container</td>
            </tr>
            <tr>
                <td><b>PSR</b></td>
                <td><a href="https://github.com/appserver-io-psr/messagequeueprotocol">https://github.com/appserver-io-psr/messagequeueprotocol</a></td>
            </tr>
        </tbody>
    </table>
</div>
<p><br/></p>

### <i class="fa fa-bars"></i> Functions
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Features</b></td>
                <td class="col-md-8">
                    <div class="content content-table">
                        <ul>
                            <li>Asynchronous processing of message objects</li>
                            <li>Uses MessageDriven beans to process messages</li>
                            <li>Possibility to priories messages and their execution</li>
                            <li>DI support to inject queue sender into all bean types and servlets</li>
                            <li>High performance and best usage of resources through independent thread for each message</li>
                            <li>Usage of standardized protocols like HTTP</li>
                            <li>Interface for integrating other protocols like STOMP</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

### <i class="fa fa-edit"></i> Use Case
<p>
Importing a huge number of products, e. g. into an online shop system can take some time using a single-threaded PHP environment. So if you have to import 100.000 products on a server with 4 CPU's each providing 8 cores, the import process will make, by default, use of exactly 1 of the 32 available cores. This will lead to an import time of round about 14 hours, what in reality will be a really huge problem. Using a Message-Queue can be a solution, because it allows you to chunk the data, supposed 100 rows per chunk, and import them in separate threads by sending each to the queue. This can speed up the import process up to 0.5 hours in best case.
</p>
