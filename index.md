---
layout: sections
title: Home
position: 0
permalink: /
slider:
  - title : PHP revolution is only a few days away
    link-href : /downloads.html
    link-label : DOWNLOAD BETA NOW
    img : /assets/img/slider_jday.png
    countdown : true
  - title : Apply for a revolutionary partnership 
    link-href : /partners.html
    link-label : APPLY NOW
    img : /assets/img/Slider_02_Handshake_grey.png
    countdown : false

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

<section>
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
  <table class="table">
      <thead>
          <tr>
              <th class="col-md-3"><h4>Feature</h4></th>
              <th class="col-md-3 text-center"><h4>Apache</h4></th>
              <th class="col-md-3 text-center"><h4>nginx</h4></th>
              <th class="col-md-3 text-center"><h4>appserver.io</h4></th>
          </tr>
      </thead>
      <tbody>
          <tr>
              <td>Language</td>
              <td class="text-center">C</td>
              <td class="text-center">C</td>
              <td class="text-center">PHP</td>
          </tr>
          <tr>
              <td><a href="{{ "/products/features/webserver.html" | prepend: site.baseurl }}">Webserver HTTP Compliant <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/rewrite-engine.html" | prepend: site.baseurl }}">Rewrite Engine <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td>Rewrite Map</td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
              <td>Fast CGI Interface</td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/servlet-engine.html" | prepend: site.baseurl }}">Servlet Engine <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/message-queue.html" | prepend: site.baseurl }}">Message Queue <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/timer-service.html" | prepend: site.baseurl }}">Timer Service <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
          </tr>
          <tr>
          <td><a href="{{ "/products/features/persistence-container.html" | prepend: site.baseurl }}">Persistence Container <i class="icon-ci fa fa-info-circle"></i></a></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-nok fa fa-minus-square"></i></td>
              <td class="text-center"><i class="icon-ok fa fa-check-square-o"></i></td>
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

<section class="grey text-center">
<div class="container">
<h2><i class="fa fa-video-camera"></i>&nbsp;&nbsp;Watch the Video from the Imagine Conference</h2>
<p><br/></p>
<div class="elastic-video">
<iframe width="854" height="510" src="//www.youtube.com/embed/D5rkJ1bznKo#t=10" frameborder="0" allowfullscreen></iframe>
</div>
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
