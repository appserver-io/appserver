---
layout: sections
title: Home
meta_title: Next-generation PHP infrastructure – appserver.io
meta_description: Next-generation PHP infrastructure consisting of a lightning fast webserver written in PHP plus additional useful services in one powerful bundle...
position: 0
permalink: /
slider:
  - title: Join us on our 1st appserver.io Hackathon!
    link-href: /community/1st-hackathon.html
    link-label: <i class="fa fa-ticket"></i> Get your ticket now!
    poster: /assets/vid/programming-ide.jpg
    videos:
      - src: /assets/vid/programming-ide.mp4
        type: video/mp4
      - src: /assets/vid/programming-ide.webm
        type: video/webm
      - src: /assets/vid/programming-ide.ogv
        type: video/ogg
    opacity: .8
  - title: Iron-Knight has arrived!
    link-href: /downloads.html
    link-detail-href: /release/2015/11/11/1.1.0-released.html
    link-label: Download
    img: /assets/img/appserver-header-image-iron-knight.jpg
    opacity: .5
    countdown: false

boxes-targetgroups:
  - title       : For Developers
    text        : Easy to use on all platforms and just PHP
    link-href   : /downloads.html
    link-label  : Learn more
  - title       : For Vendors
    text        : More performance and reduced maintenance
    link-href   : /partners.html
    link-label  : Learn more
  - title       : For Agencies
    text        : Increases efficiency and sales potential
    link-href   : /partners.html
    link-label  : Learn more
  - title       : For Companies
    text        : Easier distribution and seamless integration
    link-href   : /partners.html
    link-label  : Learn more
---

<section class="black small midsizefont text-center">
<div class="container">
<a href="{{ "/get-started/documentation.html" | prepend: site.baseurl }}" class="white"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;Documentation</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="{{ site.github_repository }}"><i class="fa fa-github"></i>&nbsp;&nbsp;&nbsp;Github Project</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="{{ site.github_gitter }}"><i class="fa fa-weixin"></i>&nbsp;&nbsp;&nbsp;Gitter Support Chat</a>
</div>
</section>

<section class="darkgrey">
<div class="container">

<h1><i class="fa fa-cubes"></i> Comparison of PHP infrastructural components</h1>
appserver.io is a next-generation PHP infrastructure consisting of a lightning fast webserver completely
written in PHP including additional frequently needed services in one powerful bundle. You can use all of
the services or only specifically selected services in your existing application with no additional tweaks.
This is just what you need since we have eliminated the need for additional tools or additional services,
appserver.io and PHP and you´re done!

</div>
</section>

<section class="grey">
<div class="container">

<div class="bs-example" data-example-id="simple-table">
  <table class="table table-responsive">
      <thead>
          <tr>
              <th class="col-md-3"><h4>Feature</h4></th>
              <th class="col-md-2 text-center"><h4>Apache</h4></th>
              <th class="col-md-2 text-center"><h4>nginx</h4></th>
              <th class="col-md-2 text-center"><h4>Zend Server</h4></th>
              <th class="col-md-2 text-center"><h4>WildFly</h4></th>
              <th class="col-md-2 text-center"><h4>appserver.io</h4></th>
          </tr>
      </thead>
      <tbody>
          <tr>
              <td>Language</td>
              <td class="text-center">C</td>
              <td class="text-center">C</td>
              <td class="text-center">C</td>
              <td class="text-center">Java</td>
              <td class="text-center">PHP</td>
          </tr>
          <tr>
              <td><a href="{{ "/products/features/webserver.html" | prepend: site.baseurl }}">Webserver HTTP Compliant <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/rewrite-engine.html" | prepend: site.baseurl }}">Rewrite Engine <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td>Rewrite Map</td>
              <td class="text-center"><i class="icon-ok fa fa-check"></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
              <td>Fast CGI Interface</td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/servlet-engine.html" | prepend: site.baseurl }}">Servlet Engine <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/message-queue.html" | prepend: site.baseurl }}">Message Queue <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/timer-service.html" | prepend: site.baseurl }}">Timer Service <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/persistence-container.html" | prepend: site.baseurl }}">Persistence Container <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-remove"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check"></i></td>
          </tr>

</tbody>
</table>
</div>
</div>
</section>


<section class="text-center">
<div class="container">
<h2><i class="fa fa-dot-circle-o"></i>&nbsp;&nbsp;Learn more about our target groups</h2>
<p><br/></p>
{% include widgets/boxes.html boxes = page.boxes-targetgroups %}
</div>
</section>

<!--
## <i class="fa fa-hand-o-right"></i> Lightning speed is only one part of the benefits of appserver.io...
<p><br/></p>
<div class="row">
    <div class="col-md-6">With the appserver.io plattform an increase in performance of your application is not to far away. If you use the services the infrastructure is offering a tremendous boost is absolutely possible and it comes along with an improvement for different parts of your software. 
    <p><br/></p>
    <a class="btn btn-info btn-lg"
                   href="{{ "/products/features.html" | prepend: site.baseurl }}">
                    <i class="fa fa-bars"></i>&nbsp;&nbsp;learn more about the possibilies
                </a>

    </div>
    <div class="col-md-6"><img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">     </div>
</div>
<p><br/></p>
-->

<section class="black text-center">
    <div class="container">
        <h2><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;... and best of all it´s completely open source!</h2>
    </div>
</section>
