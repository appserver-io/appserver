---
layout: detail
title: Rewrite Engine
position: 20
group: Features
permalink: /product/features/rewrite-engine.html
subnavigation: features
author: all
---

### <i class="fa fa-info"></i> Info
<div class="bs-example" data-example-id="simple-table">
    <table class="table">
        <tbody>
            <tr>
                <td class="col-md-2"><b>Short description</b></td>
                <td class="col-md-8">Provides a module for rewriting URLs and redirecting requests by using classical regular expressions. Rules can be varied, nested and chained to even fulfill complex requirements.
In addition, the rewrite engine also serves as a basis for complex rewrite maps which can be loaded from external datasources as e.g. databases.
                </td>
            </tr>
            <tr>
                <td><b>Availability</b></td>
                <td>since 1.0.0</td>
            </tr>
            <tr>
                <td><b>Type</b></td>
                <td>Webserver Module</td>
            </tr>
            <tr>
                <td><b>Dependencies</b></td>
                <td>appserver.io Webserver</td>
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
                            <li>PCRE compliant with additional fixed expressions</li>
                            <li>URI based rewriting as well as external redirecting</li>
                            <li>Based on Apache rewrite concepts </li>
                            <li>Wide variety of backreferences allow for complex dynamic handling of requests</li>
                            <li>Support for flags and file system based conditions</li>
                            <li>Arbitrary extension of the module through import of external data sources </li>
                            
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

### <i class="fa fa-edit"></i> Use Case
<p>
The rewrite module is a webserver module included within the appserver.io Webserver environment by default. It provides sophisticated rewriting and redirecting capabilities based on complex request analysis and is oriented on similar solutions of other webserver components. Especially functionality of the Apache solution mod_rewrite got adopted and is offered by the Rewrite Module.
</p>
<p>
Use cases begin with application specific rewrite solutions developers can port rules from, so they can centrally manage them, increase interoperability between applications and increase performance by omitting unnecessary calls to the applications.
But also more complex solutions are already integrated in the module. A company might want to manage external redirects and rewrites within their web presence with their own toolset rather than the appserver.io Dashboard. This allows them to enable different employees lacking the technical background and allow for e.g. marketing campaigns without querying the IT department. This is possible due to the module's ability to dynamically enhance its rewrite chain from external datasources such as databases or querying them in real time.
This makes managing a dynamic web presence a breeze, empowers the complete team and keeps dedicated work where the business background is.
</p>
<p>
The module still presents its full potential to any technical personal by offering a fully PCRE compliant condition engine together with a wide set of dynamically available backreferences such as the user agent or server variables. This allows for  complex and fine grained configurations which optimally aid applications running on the webserver.
</p>
