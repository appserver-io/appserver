# Upgrade from 1.0.1 to 1.0.2

## Development Mode

After an update, all files will be reset to the original ownership and rights. So if you've switched to [Development-Mode](http://appserver.io/get-started/documentation/basic-usage.html#setup-script), you need to run the setup script again to re-activate the development mode again.

To do so, open a console and type the following

```sh
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```

## Updating Mac OS X

When updating a Mac OS X installation, we actually do **NOT** take care about changes in your configuration files. Please **BACKUP** all customized files, especially `etc/appserver/appserver.xml` and `etc/appserver/conf.d/virtual-hosts.xml` to not loose your changes.

## Configuration

We've made minor changes in some configuration files. This changes **SHOULD NOT** affect you in most cases.

### Move of authentication to appserver-io/http

Up to version 1.0.2 the authentication used within the webserver and the appserver were implemented separately. This has changed and will force an update of server configurations using authentication.
So please change the `type` param of `authentication` elements to use the new `\AppserverIo\Http\Authentication` namespace as shown below.

Change

```xml
<authentications>
    <authentication uri="^\/auth\/basic\/.*">
        <params>
            <param name="type" type="string">\AppserverIo\WebServer\Authentication\BasicAuthentication</param>
            <param name="realm" type="string">PhpWebServer Basic Authentication System</param>
            <param name="file" type="string">var/www/auth/basic/.htpasswd</param>
        </params>
    </authentication>
    ...
</authentications>
```

to the following

```xml
<authentications>
    <authentication uri="^\/auth\/basic\/.*">
        <params>
            <!-- this HAS been changed with 1.0.2
            <param name="type" type="string">\AppserverIo\Http\Authentication\BasicAuthentication</param>
            -->
            <param name="realm" type="string">PhpWebServer Basic Authentication System</param>
            <param name="file" type="string">var/www/auth/basic/.htpasswd</param>
        </params>
    </authentication>
    ...
</authentications>
```
