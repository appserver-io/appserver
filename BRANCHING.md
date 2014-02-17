To create a new branch, in this case we talk of a new branch for the appserver itself, 
the following steps are necessary.

As the appserver core is build from several packages, it is neceessary to create a new
branch for the following packages:

* TechDivision_Runtime
* TechDivision_Socket
* TechDivision_Stream
* TechDivision_ApplicationServer
* TechDivision_ServletContainer
* TechDivision_WebSocketContainer
* TechDivision_PersistenceContainer
* TechDivision_PersistenceContainerClient
* TechDivision_MessageQueue
* TechDivision_MessageQueueClient

To create and tag a new branch for the appserver, e. g. a branch named 0.5.9-beta and tag it with
0.5.9-beta1, you have two possibilities. First is to branch each of the packages separately.

```sh
root@debian:~# git branch 0.5.9-beta
root@debian:~# git checkout 0.5.9-beta
Switched to branch '0.5.9-beta'
root@debian:~# git tag -m 'Comment what the branch is all about' 0.5.9-beta1
root@debian:~# git push origin 0.5.9-beta
Total 0 (delta 0), reused 0 (delta 0)
To git@github.com:techdivision/TechDivision_Runtime.git
 * [new branch]      0.5.9-beta -> 0.5.9-beta
root@debian:~# git push origin 0.5.9-beta1
Counting objects: 1, done.
Writing objects: 100% (1/1), 198 bytes, done.
Total 1 (delta 0), reused 0 (delta 0)
To git@github.com:techdivision/TechDivision_Runtime.git
 * [new tag]         0.5.9-beta1 -> 0.5.9-beta1
```

If you want to create a new tag only and you are currently working on your own fork,
you have to do the following steps.

```sh
root@debian:~# cd ../TechDivision_Runtime
root@debian:~# git remote add techdivision git@github.com:techdivision/TechDivision_Runtime.git
root@debian:~# git fetch techdivision
remote: Counting objects: 18, done.
remote: Compressing objects: 100% (10/10), done.
remote: Total 13 (delta 6), reused 9 (delta 3)
Unpacking objects: 100% (13/13), done.
From github.com:techdivision/TechDivision_Runtime
 * [new branch]      0.5.9-beta -> techdivision/0.5.9-beta
 * [new tag]         0.5.9-beta1 -> 0.5.9-beta1
root@debian:~# git checkout techdivision/0.5.9-beta
Note: checking out 'techdivision/0.5.9-beta'.

You are in 'detached HEAD' state. You can look around, make experimental
changes and commit them, and you can discard any commits you make in this
state without impacting any branches by performing another checkout.

If you want to create a new branch to retain commits you create, you may
do so (now or later) by using -b with the checkout command again. Example:

  git checkout -b new_branch_name

HEAD is now at 09b003a... Merge pull request #3 from wagnert/0.5.9-beta
root@debian:~# git tag -m 'A new tag for 0.5.9-beta' 0.5.9-beta2
root@debian:~# git push techdivision 0.5.9-beta1
Counting objects: 1, done.
Writing objects: 100% (1/1), 176 bytes, done.
Total 1 (delta 0), reused 0 (delta 0)
To git@github.com:techdivision/TechDivision_Runtime.git
 * [new tag]         0.5.9-beta2 -> 0.5.9-beta2
```

As you have to do these steps for each package that is part of the appserver core
sometimes this will be a bit annoying. Therefore, if you're working on Mac OS X, you
can automate these steps a little bit.

Assuming you're actually in your workspace and have only cloned the necessary packages,
with the following command you can checkout the branch 0.5.9-beta for all packages.
 
```sh
for i in *; do cd $i; git checkout 0.5.9-beta; cd ..; done
```

After you've made some changes, e. g. raising the dependencies for all composer.json 
files in each of the packages, you can commit the changes with the following command. 
 
```sh
for i in *; do cd $i; git add .; git commit -m 'Change composer.json dependencies to ~0.5.9@beta'; git push; cd ..; done
```

The following command combines these steps, checks the techdivision/0.5.9-beta branch
out, tags it, pushes the tag and check the 0.5.9-beta branch of your fork out. 

```sh
for i in *; do cd $i; git fetch techdivision; git checkout techdivision/0.5.9-beta; git tag -m 'A new tag' 0.5.9-beta3; git push techdivision 0.5.9-beta3; git checkout 0.5.9-beta; cd ..; done
```