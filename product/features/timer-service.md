---
layout: detail
title: Timer Service
position: 10
group: Features
permalink: /product/features/timer-service.html
subnavigation: features
author: all
---

### <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">The Timer Service enables the execution of methods at a determined point of time whereby the configuration, as execution in the range of seconds is also possible, allows a significant higher differentiation then services like CRON. In contrast to most available solutions, developers don't have to configure scripts that'll be executed, instead they configure methods of message driven, stateless or singleton session beans. Beside scheduled execution it is possible to create timers programmatically what allows you to invoke methods once or scheduled at a dedicated point of time in future.
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
                <td>Appserver.io Runtime, Appserver.io, Persistence Container</td>
            </tr>
            <tr>
                <td><b>PSR</b></td>
                <td><a href="https://github.com/appserver-io-psr/epb">https://github.com/appserver-io-psr/epb</a></td>
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
                            <li>Programmatically created timers with execution on a dedicated point of time in future using microseconds</li>
                            <li>Scheduled execution of methods of message driven, stateless, or singleton session beans using seconds</li>
                            <li>Asynchronous execution</li>
                            <li>Simple configuration through annotations on the respective method</li>
                            <li>Use annotations to configure scheduled timers</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

### <i class="fa fa-edit"></i> Use Case
<p>
The need to schedule tasks is given in nearly every application, especially for applications in an enterprise environment. In most cases, PHP applications use system services like CRON to schedule their tasks. Several e-commerce applications provides script like cron.sh, that has to be scheduled by CRON every minute and itself will again execute configured, long running tasks, like updating the table indexes, by a complicated, internal functionality. As PHP is single-threaded by default, this leads to several problems, e. g. long running tasks blocks others, also very important tasks, that can be executed only if the previous task has been finished. The Message Queue is a simple solution to solve such problems, because every task will be executed asynchronously in a separate thread that will never block other tasks.
</p>