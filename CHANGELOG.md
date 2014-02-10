# 0.5.8beta7

* Logging with monolog
* Generic management API
* HTTP basic + digest authentication for Servlet Container
* Integrate annotations for session beans
* Administration interface with drag-and-drop PHAR installer
* Automated Build- and Deployment using Travis-CI
* Set environment variables in XML configuration files
* Merging XML configuration files
* WebSocket integration
* Running Magento CE 1.7.x + 1.8.x demo applications

# 0.5.8beta2

* Remove PHP 5.2 compatibility from SplClassLoader (5.2 legacy namespaces don't work now)
* Add getServletManager() method to ServletConfiguration
* Add support for multiple sessions by passing new parameter to HttpRequest::getSession($sessionName) method
* Compile PHP and deliver with OpenSSL 1.0.0e
* Enable soap.so by default
* Enable opcache.so by default
* Switching to latest pthreads + APCu versions
* Fixed #17: Servlet init() method will be called exactly once