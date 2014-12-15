# Introduction

The deploy directory in the appserver.io Application Server distribution is the location end users can place their 
deployment content (e. g. phar files) to have it deployed into the server runtime.

Users, particularly those running production systems, are encouraged to use the appserver.io AS management APIs to 
upload and deploy deployment content.

# Deployment Modes

The scanner actually only suports manual deployment mode which means that you have to restart the server to process 
deployment of your content. In this mode, the scanner will not attempt to directly monitor the deployment content and 
decide if or when the end user wishes the content to be deployed or undeployed. Instead, the scanner relies on a system 
of marker files, with the user's addition or removal of a marker file serving as a sort of command telling the scanner 
to deploy, undeploy or redeploy content.

It is also possible to copy your unzipped content directly into the webapps folder. After restarting the webserver
your content will then be deployed without having any impact on the deployment scanner, because only zipped (.phar)
content will be recognized.

# Marker Files

The marker files always have the same name as the deployment content to which they relate, but with an additional file 
suffix appended. For example, the marker file to indicate the example.phar file should be deployed is named 
example.phar.dodeploy. Different marker file suffixes have different meanings.

The relevant marker file types are:

| Marker       | Description                                                     |
|:-------------|:----------------------------------------------------------------|
| .dodeploy    | Placed by the user to indicate that the given content should be deployed or redeployed into the runtime.                     |
| .deploying   | Placed by the deployment scanner service to indicate that it has noticed a .dodeploy file and is in the process of deploying the content. This marker file will be deleted when the deployment process completes.                                   |
| .deployed    | Placed by the deployment scanner service to indicate that the given content has been deployed into the runtime. If an end user deletes this file and no other marker is available, the content will be undeployed.                                     |
| .failed      | Placed by the deployment scanner service to indicate that the given content failed to deploy into the runtime. The content of the file will include some information about the cause of the failure. Note that, removing this file will make the deployment eligible for deployment again.                       |
| .undeploying | Placed by the deployment scanner service to indicate that it has noticed a .deployed file has been deleted and the content is being undeployed. This marker file will be deleted when the undeployment process completes.                        |
| .undeployed  | Placed by the deployment scanner service to indicate that the given content has been undeployed from the runtime. If an end content is being undeployed. This marker file will be deleted user deletes this file, it has no impact.                       |

# Basic workflows

All examples assume variable $AS points to the root of the appserver.io AS distribution.

Windows users: the examples below use UNIX shell commands; see the [Windows Notes](#windows-notes) below.

1. Add new zipped (.phar) content and deploy it:

```
$ cp target/example.phar $AS/deploy
$ touch $AS/deploy/example.phar.dodeploy
```

2. Undeploy currently deployed zipped (.phar) content:

```
$ rm $AS/deploy/example.phar.deployed
```

3. Replace currently deployed zipped (.phar) content with a new version and redeploy it:

```
$ cp target/example.phar $AS/deploy
$ mv $AS/deploy/example.phar.deployed $AS/deploy/example.phar.dodeploy
```

# Windows Notes

The above examples use UNIX shell commands. Windows equivalents are:

| UNIX           | Windows                 |
|:---------------|:------------------------|
| cp src dest    | xcopy /y src dest       |
| cp -r src dest | xcopy /e /s /y src dest |
| rm afile       | del afile               |
| touch afile    | echo >> afile           |

Note that the behavior of ```touch``` and ```echo``` are different but thedifferences are not relevant to the usages 
in the examples above.