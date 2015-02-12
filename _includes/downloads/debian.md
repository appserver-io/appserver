**Runs and tested on Debian Squeeze (64-bit) and higher!**

If you're on a Debian system you might try our .deb repository:

```bash
echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
aptitude update
aptitude install appserver-dist
```

Optionally you can download the .deb files for the runtime and the distribution and install
them by double-clicking on them. This will invoke the system default package manager and guides
you through the installation process. Please install the runtime first,
as this is a dependency for the distribution.