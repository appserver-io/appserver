# PHP Application Server

Ziel des Projekts ist die Entwicklung eines multithreaded Application Servers für PHP, geschrieben in PHP.
Geschrieben in PHP um möglichst vielen Entwicklern aus der PHP Gemeinde die Mitarbeit zu ermöglichen und
das Projekt durch die möglichst breite Unterstützung der PHP Community als Standardlösung für Enterprise
Application im PHP Umfeld zu etablieren.

## Highlights

* Doctrine als Standard Persistence Provider
* Session Beans (Stateful, Stateless + Singleton)
* Message Beans
* Timer Service (tbd)
* Servlet Engine
* Integrierte Message Queue
* Einfache Skalierung
* Webservices

## Technical Features

* Verwendung der phtreads Library von Joe Watkins (https://github.com/krakjoe/pthreads)
* Verwendung von DI & AOP innerhalb der jeweiligen Container
* Einsatz von Annotations zur Konfiguration der Beans
* Configration by Exception (optional Verwendung von Deployment Descriptoren möglich)

Die Implementierung einer Webanwendung sowie deren Betrieb im PHP Application Server muss so einfach wie möglich
sein. Hierzu werden zum Einen, wann immer möglich, bereits bestehenden Komponenten als Standardlösung, so z. B.
Doctrine, verwendet, zum Anderen darf, durch das Paradigma Configuration by Exception, für den Betrieb einer
Anwendung nur ein Minimum an Konfiguration notwendig sein. So wird breits durch das Standardverhalten der
jeweiligen Kompenenten ein Großteil der Verwendungsfälle abgedeckt wodurch sich der Enwickler häufig keine
deklarativen Angaben zur Konfiguration machen muss.

Um eine möglichst breite Community anzusprechen muss die Architektur des PHP Application Servers so aufgebaut werden,
das über Adapter eine möglichst große Anzahl an bereits bestehenden Anwendungen einfach migriert werden können.
Weiterhin wird die zukünftige Entwicklung von Webanwendungen auf Basis aller relevanten PHP Frameworks durch die
Bereitstellung von Libraries unterstützt.

## Requirements

* PHP5.3+ on x64 or x86
* ZTS Enabled (Thread Safety)
* Posix Threads Implementierung
* Memcached (2.1+)

Die aktuelle Version bisher lediglich auf Mac OS X 10.7+ getestet. Aufgrund der verwendeten Komponenten sollten
allerdings auch auf anderen Plattformen der Betrieb möglich sein.

### Supported PHP Versionen

Der PHP Application Server sollte auf jeder PHP Version ab 5.3.0 laufen, allerdings traten bei diversen Tests mit
PHP 5.3.x immer wieder Segmentation Faults auf die sich allerdings auf das frühe Entwicklungsstadium der pthreads
Library zurückführen lassen. Aktuell wird für die Entwicklung PHP 5.4.10 verwendet.

## PHP mit pthreads bauen

Je nach Debian Version & PHP Konfiguration müssen vorab folgende Libraries müssen installiert werden:

```
$ apt-get install \
    apache2-prefork-dev \
    php5-dev \
    libxml2 \
    libxml2-dev \
    libcurl3-dev \
    libbz2-dev \
    libxpm4 \
    libxpm-dev \
    libc-client2007e \
    libc-client2007e-dev \
    libmcrypt4 \
    libmcrypt-dev \
    libmemcached-dev \
    libjpeg62 \
    libjpeg62-dev \
    libpng12-0 \
    libpng12-dev \
    libfreetype6 \
    libfreetype6-dev \
    g++
```

Einen guten Überblick über die Fehlermeldungen und die Libraries die für die Behebung notwendig sind findet man
unter http://www.robo47.net/text/6-PHP-Configure-und-Compile-Fehler.

PHP 5.4.x für Debian 6.0.x mit folgender Konfiguration kompilieren:

```
$ ./configure \
    --with-apxs2=/usr/bin/apxs2 \
    --prefix=/usr \
    --with-libdir=lib64 \
    --with-config-file-path=/etc/php5/apache2 \
    --with-config-file-scan-dir=/etc/php5/conf.d \
    --enable-libxml \
    --enable-session \
    --with-pcre-regex=/usr \
    --enable-xml \
    --enable-simplexml \
    --enable-filter \
    --disable-debug \
    --enable-inline-optimization \
    --disable-rpath \
    --disable-static \
    --enable-shared \
    --with-pic \
    --with-gnu-ld \
    --with-mysql \
    --with-gd \
    --with-jpeg-dir \
    --with-png-dir \
    --with-xpm-dir \
    --enable-exif \
    --with-zlib \
    --with-bz2 \
    --with-curl \
    --with-ldap \
    --with-mysqli \
    --with-freetype-dir \
    --enable-soap \
    --enable-sockets \
    --enable-calendar \
    --enable-ftp \
    --enable-mbstring \
    --enable-gd-native-ttf \
    --enable-bcmath \
    --enable-zip \
    --with-pear \
    --with-openssl \
    --with-imap \
    --with-imap-ssl \
    --with-kerberos \
    --enable-phar \
    --enable-pdo \
    --with-pdo-mysql \
    --with-mysqli \
    --enable-maintainer-zts \
    --enable-roxen-zts \
    --with-mcrypt \
    --with-tsrm-pthreads \
    --enable-pcntl
```

Anschließend muss die pthreads Extension aus dem Github Repository ausgecheckt, compiliert und installiert werden:

```
$ git clone https://github.com/krakjoe/pthreads.git
$ cd pthreads
$ phpize
$ ./configure --enable-shared --enable-static
$ make && make install
```

Nicht vergessen die Extension in der php.ini mit:

```
extension = pthreads.so
```

zu aktivieren.

Der PHP Application Server benötigt in der aktuellen Version Memcached. Um die hierfür für PHP notwendige PECL
Extension installieren zu können benötigen wir libmemcache. libmemcache herunterladen, kompilieren + installeren:

```
$ wget https://launchpad.net/libmemcached/1.0/1.0.15/+download/libmemcached-1.0.15.tar.gz
$ tar xvfz libmemcached-1.0.15.tar.gz
$ cd libmemcached-1.0.15
$ ./configure
$ make
$ make install
```

Anschließend kann mit:

```
$ pecl install memcached
```

die PECL Extension installieret werden. Auch diese muss in der php.ini aktiviert werden.

## Installation Application Server

### Build

Für den Build Prozess wir aktuell ANT verwendet. Um eine neue Version des Application Servers zu erzeugen kannst du
auf der Konsole über das ANT-Target

```
$ ant UPDATE-pack
```

ein neues tar.gz erzeugen, das die Ausgangsbasis für deine lokale Installation darstellt.

### Installation

Mit diesem Paket, das du nach Aufruf des ANT Targets im target Verzeichnis findest, kannst du die Installation
über die Kommandozeile schnell und einfach durchführen. Hierzu kopierst du das Archiv auf der Kommandozeile mit

```
$ cp target/appserver-0.4.6beta.tar.gz /var/www
```

in das Basisverzeichnis deines Webservers, in unserem Fall /var/www. Anschließend wechsels du mit

```
$ cd /var/www
```

in das Basisverzeichnis und entpackst die Sourcen mit

```
$ tar xvfz appserver-0.4.0beta.tar.gz
$ ln -s appserver-0.4.0beta appserver
```

Zusätzlich erzeugst du gleich noch einen symbolischen Link. Wenn du später ein Update machen möchtest kannst du diesen
dann einfach auf das neue Verzeichnis umbiegen. Der PHP Application Server verwendet ein internes PEAR Repository für
die Installation von zusätzlich Pakete wie z. B. Doctrine. Dieses kannst du mit

```
$ cd appserver
$ chmod +x bin/webapp
$ bin/webapp setup
```

initialisieren. Da als Standard Persistence Provider Doctrine zum Einsatz kommt, die Sourcen jedoch nicht mit
dem PHP Application Server ausgeliefert werden erfolgt die Installation im integrierten PEAR Repository mit

```
$ bin/webapp channel-discover pear.doctrine-project.org
$ bin/webapp install doctrine/DoctrineORM
```

Anschließend legst du über die MySQL Kommandozeile die Datenbank mit

```
mysql$ create database appserver_ApplicationServer;
mysql$ grant all on appserver_ApplicationServer.* to "appserver"@"localhost" identified by "appserver";
mysql$ flush privileges;
```

an. Abschließend kannst du den PHP Application Server mit

```
$ php -f server.php
```

starten und die notwendigen Tabellen durch Aufruf der URL im Browser

```
http://<appserver-ip>/appserver/examples/index.php?action=createSchema
```

erzeugen lassen. Über die URL

```
http://<appserver-ip>/appserver/examples/
```

ist eine kleine Beispiel Anwendung erreichbar die die Funktionalität des PHP Application Servers anhand eines CRUD
Beispiels demonstriert. Zusätzlich ist über die URL

```
http://<appserver-ip>:8586/example/hello-world.do
```

ein rudimentäres Servlet ansprechbar. Allerdings wird im aktuellen Stand hier lediglich statischer Content
ausgegeben. Über die URL

```
http://<appserver-ip>:8586/example/index.php
```

kann ein PHP Skript, analog zur Ausführung über den Apache, aufgerufen werden. Hierbei wird im Hintergrund ein
Servlet (PhpServlet) aufgerufen, das eine PHP Runtime Umgebung bereitstellt. Allerdings handelt es sich hierbei
lediglich um eine sehr rudimentäre Implementierung, so werden z. B. globale Variablen wie $_REQUEST noch nicht
bereitgestellt.

### Weiterentwicklung

Um dich auch während der Weiterentwicklung zu unterstützen kannst du die Änderungen an deinen Sourcen, ebenfalls
über ein ANT Target jederzeit in deine Entwicklungsinstanz kopieren. Möchtest du die aktuellen Sourcen aus deinem
Projekt in die Entwicklungsinstanz zu kopieren startest du über die Kommandozeile das ANT-Target

```
$ ant deploy
```

Wenn du dich an diese Vorgehensweise hältst, dann kommst du nicht in das Problem, dass du ständig aufpassen musst,
nicht erwünschte Dateien wie die Symfony oder Doctrine Klassen oder irgendwelche Cache-Dateien die PEAR erzeugt, zu
committen.

## Verwendung

### Mit dem PersistenceContainer verbinden

Der Verbindungsaufbau zum PersistenceContainer erfolgt über eine Client Library. Hierbei ist in der aktuellen
Version wichtig, dass bereits eine Session existiert. Nach dem Verbindungsaufbau kann über die lookup() Methode
ein Proxyobjekt des gewünschten SessionBeans geholt werden:

```php
<?php

// initialize the session
session_start();

// initialize the connection, and the initial context
$connection = Factory::createContextConnection();
$session = $connection->createContextSession();
$initialContext = $session->createInitialContext();

// lookup the remote processor implementation
$processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');

// load all sample entities
$allEntities = $processor->findAll();

?>
```

## Vagrant Box

Um sich das Kompilieren und Zusammenstellen der einzelnen Abhänigkeiten einfacher zu gestalten, gibt es eine Vagrant
Konfiguration mit der man sehr einfach eine virtuelle Maschine mit der für den App-Server notwendigen Konfiguration
aufsetzen kann.

### Voraussetzungen

Um die Vagrant Box zu nutzen benötigt man Vagrant, VirtualBox und Librarian Chef.

Zunächst muss VirtualBox wie auf der Projektwebsite beschrieben heruntergeladen und installiert werden:

https://www.virtualbox.org/wiki/Downloads

Anschließend verfährt man ebenso mit Vagrant, dessen Download-Seite sich hier findet:

http://downloads.vagrantup.com/

Schließlich muss noch Chef, Librarian & Librarian-Chef installiert werden. Die Projektseite dazu findet sich auf
Github https://github.com/applicationsonline/librarian Da es sich um ein Ruby Paket handelt, ist die Installation
- bei vorhandem Ruby - recht einfach

```
$ sudo gem install chef librarian librarian-chef --no-rdoc --no-ri --verbose
```

**Wichtig!**: Wenn du Mac OS X verwendest müssen, damit die Kompilierung der Ruby Pakete erfolgreich durchläuft,
zwingend die aktuellsten CommandLineTools via http://connect.apple.com installiert und die ruby version wie folgt
aktualisiert werden. Terminal öffnen und die Ruby Installation als normaler Nutzer durchführen (kein root erforderlich).
Vorest noch homebrew installieren (“The missing package manager for OS X”) um das fehlende automake zu installieren.

```
$ ruby -e "$(curl -fsSkL raw.github.com/mxcl/homebrew/go)"
$ brew install automake
$ brew doctor
```

Wenn brew doctor keine Fehlermeldung ausgibt, mit dem nächsten Schritt fortfahren. Ansonsten Fehler bereinigen.

```
$ \curl -L https://get.rvm.io | bash -s stable --ruby source ~/.rvm/scripts/rvm
$ \curl -L https://get.rvm.io | bash -s stable --rails --autolibs=enabled
```

Wenn alles fertig ist einfach kurz im Terminal aus -/ einloggen. Nun sollte der Installation von
Chef, Librarian & Librarian-Chef nichts mehr im wege stehen.


Nach Installation von Chef, Librarian und Librarian-Chef kannst du die benötigten Cookbooks von Github
automatisch herunterladen. Nachdem du in dein Projektverzeichnis gewechselt bist kannst du den Download
über die Konsole mit

```
$ librarian-chef install
```

starten. Die heruntergeladenen Cookbooks solltest du anschließend im Verzeichnis coobooks finden. Zu guter Letzt fehlt
noch ein hilfreiches Plugin für Vagrant das die VirtualBox Guest Additions auf dem Laufenden hält

https://github.com/dotless-de/vagrant-vbguest

### Starten der Vagrant Box

Im Hauptverzeichnis des Application Servers ```vagrant up``` eingeben. Nach einigen Minuten sollte eine virtuelle
Maschine mit einem angepassten und neu kompilierten PHP laufen. Mit ```vagrant up``` kann man sich in die neue Maschine
einloggen.
