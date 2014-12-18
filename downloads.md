---
layout: default
title: Downloads
position: 20
permalink: /downloads.html
---

## Downloads
***

Get the latest Version of appserver.io free and enjoy the most powerful PHP infrastructure in the world with only one click!
<p><br/></p>

<ul class="nav nav-tabs nav-justified list-unstyled downloads">
    <li class="active"><a href="#mac-osx" data-toggle="tab">Mac OS X</a>
    </li>
    <li class=""><a href="#windows" data-toggle="tab">Windows</a>
    </li>
    <li class=""><a href="#debian" data-toggle="tab">Debian</a>
    </li>
    <li class=""><a href="#fedora" data-toggle="tab">Fedora</a>
    </li>
    <li class=""><a href="#cent-os" data-toggle="tab">Cent OS</a>
    </li>
    <li class=""><a href="#raspbian" data-toggle="tab">Raspbian</a>
    </li>
</ul>
<p><br/></p>

<div class="col-lg-12">

    <div class="tab-content">

        <div class="tab-pane fade active in" id="mac-osx">
            <p>
                <b>Runs and tested on Mac OS X 10.8.x and higher!</b>
            </p>
            <p>
                For Mac OS X > 10.8.x we provide a .mpkg file for download that contains the runtime and
                the distribution. Double-clicking on the `.mpkg` starts and guides you through the installation process.
            </p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-dist-1.0.0-beta1.12.mac.x86_64.pkg" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-dist-1.0.0-beta1.12.mac.x86_64.pkg
            </a>
        </div>

        <div class="tab-pane fade" id="windows">
            <p>
                <b>Runs and tested on Windows 7 (32-bit) and higher!</b>
            </p>
            <p>
                As we deliver the Windows appserver as a .jar file you can download, a installed Java Runtime
                Environment (or JDK that is) is a vital requirement for using it. If the JRE/JDK is not installed
                you have to do so first. You might get it from
                <a href="http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html">
                    Oracle's download page
                </a>. If this requirement is met you can start the installation by simply double-clicking
                the .jar archive.
            </p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-dist-1.0.0-beta1.25.win.x86.jar" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-dist-1.0.0-beta1.25.win.x86.jar
            </a>
        </div>

        <div class="tab-pane fade" id="debian">
            <p>
                <b>Runs and tested on Debian Squeeze (64-bit) and higher!</b>
            </p>
            <p>
                If you're on a Debian system you might try our .deb repository:
{% highlight bash %}
echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
aptitude update
aptitude install appserver-dist
{% endhighlight %}
            </p>
            <p>
                Optionally you can download the .deb files for the runtime and the distribution and install
                them by double-clicking on them. This will invoke the system default package manager and guides
                you through the installation process. Please install the runtime first,
                as this is a dependency for the distribution.
            </p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-runtime-1.0.0-beta.12.linux.debian.x86_64.deb" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-runtime-1.0.0-beta.12.linux.debian.x86_64.deb
            </a>
            <p></p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-dist-1.0.0-beta1.11.linux.debian.x86_64.deb" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-dist-1.0.0-beta1.11.linux.debian.x86_64.deb
            </a>
        </div>

        <div class="tab-pane fade" id="fedora">
            <p>
                <b>Runs and tested on versions Fedora 20 (64-bit) and higher!</b>
            </p>
            <p>
                We also provide .rpm files for Fedora, one for runtime and distribution,
                that you can download and start the installation process by double-clicking on it.
                This will start the systems default package manager and guides you through the installation process.
            </p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-runtime-1.0.0-beta.18.linux.fedora.x86_64.rpm" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-runtime-1.0.0-beta.18.linux.fedora.x86_64.rpm
            </a>
            <p></p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-dist-1.0.0-beta1.33.linux.fedora.x86_64.rpm" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-dist-1.0.0-beta1.33.linux.fedora.x86_64.rpm
            </a>
        </div>

        <div class="tab-pane fade" id="cent-os">
            <p>
                <b>Runs and tested on CentOS 6.5 (64-bit) and higher!</b>
            </p>
            <p>
                Installation and basic usage is the same as on Fedora but we provide different packages for runtime
                and distribution. CentOS requires additional repositories like
                <a href="http://rpms.famillecollet.com/" target="_blank">remi</a> or
                <a href="http://fedoraproject.org/wiki/EPEL" target="_blank">EPEL</a> to satisfy
                additional dependencies.
            </p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-runtime-1.0.0-beta.21.linux.centos.x86_64.rpm" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-runtime-1.0.0-beta.21.linux.centos.x86_64.rpm
            </a>
            <p></p>
            <a href="https://github.com/appserver-io/appserver/releases/download/1.0.0-beta1/appserver-dist-1.0.0-beta1.20.linux.centos.x86_64.rpm" class="btn btn-info btn-lg">
                <i class="fa fa-download"></i>&nbsp;&nbsp;appserver-dist-1.0.0-beta1.20.linux.centos.x86_64.rpm
            </a>
        </div>

        <div class="tab-pane fade" id="raspbian">
            <p>
                As an experiment we offer Raspbian and brought the appserver to an ARM environment.
                What should we say, it worked! :D With
{% highlight bash %}
os.distribution = raspbian
{% endhighlight %}
                you might give it a try to build it yourself (plan at least 5 hours) as we currently do
                not offer prepared install packages.
            </p>
        </div>

    </div>
</div>