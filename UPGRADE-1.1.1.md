# Upgrade from 1.1.0 to 1.1.1

## Default Headers

To improve security, we've added some default headers that helps to avoid some high risk security vulnerabilities. The headers are

* X-Frame-Options: Deny
* X-XSS-Protection: 1
* X-Content-Type-Options: Nosniff

The X-Content-Type-Options header with the Nosniff values forces you or your application to set the correct Content-Type header to let the browser render your content. If not, the browser will always render your content as plain text.