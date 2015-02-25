# -*- mode: ruby -*-
# vi: set ft=ruby :

print "You may be asked for your sudo password to use NFS shares\n"
print "More Information: https://docs.vagrantup.com/v2/synced-folders/nfs.html\n\n"

unless Vagrant.has_plugin?("vagrant-vbguest")
  print "please execute the following command to enable automated vbguest installation\n\n"
  print "vagrant plugin install vagrant-vbguest\n\n"
  exit
end


Vagrant.configure("2") do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box     = "chef/debian-7.6-x64"
  config.vm.box_url = "https://github.com/jose-lpa/packer-debian_7.6.0/releases/download/1.0/packer_virtualbox-iso_virtualbox.box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network :forwarded_port, guest: 9080, host: 9080

  config.vm.hostname = "appserver.dev"
  config.vm.network :private_network, ip: "192.168.31.56"



  unless ((/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM) == nil) then
    print "detected, that you run vagrant on windows ...\n"
    unless Vagrant.has_plugin?("vagrant-winnfsd") then
      print "falling back to smb share \n"
      print "more speed possible with vagrant-winnfsd plugin\n\n"
      config.vm.synced_folder ".", "/serverdata", owner: "www-data", group:"www-data"
    else
      print "vagrant-winnfsd plugin found, using nfs\n"
      print "if this doesn´t work uninstall the plugin\n\n"
      config.vm.synced_folder ".", "/serverdata", type: "nfs"
    end
  else
    config.vm.synced_folder ".", "/serverdata", type: "nfs"
  end

  config.vm.provision "shell", inline: 'echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list'
  config.vm.provision "shell", inline: 'wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -'
  config.vm.provision "shell", inline: 'aptitude update'
  config.vm.provision "shell", inline: 'aptitude install php5-fpm appserver-dist -y'


  config.vm.provider "virtualbox" do |vm, override|
    vm.name = "appserver.dev"
    vm.customize ["modifyvm", :id, "--memory",          "2048"]
    vm.customize ["modifyvm", :id, "--cpuexecutioncap",   "80"]
  end
  config.vm.provider :vmware_fusion do |vm, override|
    override.vm.box     = "precise64_fusion"
    override.vm.box_url = "http://files.vagrantup.com/precise64_vmware.box"
    override.vm.network :private_network, ip: "192.168.35.56"
    # v.gui = false
    vm.vmx["memsize"] = "2048"

  end
end