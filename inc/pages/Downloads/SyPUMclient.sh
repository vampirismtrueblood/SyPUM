#!/bin/bash

######################
#Author: Peter Malaty
#Date: 25-Dec-2016
#SyPUM client V3.8
######################

if [ ! -f /tmp/sypumclient.lock ]; then
SyPUMClientVer="3.8"
HOSTNAME=`hostname`
SyPUMServer="sypum"
SyPUMIP=`getent ahosts $SyPUMServer |grep RAW | awk '{ORS=""} {print $1 ; exit}'`
IPADDR=$(ip ro get $SyPUMIP | head -1 | awk '{print $NF}' | xargs echo -n)
echo "$IPADDR"
UBUVER=`cat /etc/os-release | grep VERSION_ID | grep -Po '[0-9]+\.?[0-9]+'`
CURL=`which curl`
CAT=`which cat`
WGET=`which wget`
APTGET=`which apt-get`
CHMOD=`which chmod`
DPKG=`which dpkg`
RM=`which rm`
TOUCH=`which touch`
CHMOD=`which chmod`
REBOOTREQ="NO"
RELEASE=""
$TOUCH /tmp/sypumclient.lock

#Check if we're banned
$CURL -k https://$SyPUMServer/api4.php/isbanned/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" | grep "YES"

if [[ $? == 0 ]]; then
echo "We're banned to report anything"
else
echo "We're good to proceed"


if [ -z $APTGET ]; then RELEASE="rhel"; else RELEASE="ubuntu"; fi
#Remove spacewalk crap if any
#$DPKG -l | grep rhin

#if [ $? == 0 ]; then
#$DPKG -r apt-transport-spacewalk python-rhn rhn-client-tools rhnsd
#fi
if [ -f /etc/apt/sources.list.d/spacewalk.list ]; then
$RM -f /etc/apt/sources.list.d/spacewalk.list
fi

if [ -z $WGET ]; then
$APTGET install wget -y
WGET=`which wget`
fi

if [ -z $CURL ]; then
	$APTGET install curl -y
	CURL=`which curl`
fi


if [ ! -d /usr/lib/update-notifier ]; then
	mkdir -p /usr/lib/update-notifier
fi

$APTGET update


if [ ! -f /usr/lib/update-notifier/apt_check.py.mod ]; then
$WGET https://$SyPUMServer/modded_apt_check/"$UBUVER"/apt_check.py.mod --no-check-certificate -O /usr/lib/update-notifier/apt_check.py.mod
$CHMOD +x /usr/lib/update-notifier/apt_check.py.mod
fi

if [ ! -f /usr/lib/update-notifier/apt_check.py.mod2 ]; then
$WGET https://$SyPUMServer/modded_apt_check/"$UBUVER"/apt_check.py.mod2 --no-check-certificate -O /usr/lib/update-notifier/apt_check.py.mod2
$CHMOD +x /usr/lib/update-notifier/apt_check.py.mod2
fi

#We also Need to check for client update 
LatestClientVer=`curl -k https://$SyPUMServer/api4.php/clientver`

if [ $LatestClientVer == $SyPUMClientVer ]; then
	echo "Client is uptodate"
else
	$RM -f /root/SyPUMclient.sh /root/SyPUMclient
	$WGET --no-check-certificate https://$SyPUMServer/SyPUMclient -O /root/SyPUMclient.sh
	$CHMOD +x /root/SyPUMclient.sh
fi


#Check if our cronjob exists

if [ ! -f /etc/cron.d/sypumclientcron ]; then
	echo "It would appear that you're running the client for the very first time or a version later than 3.6 ... so I'm taking care of few things here ..."
	$WGET --no-check-certificate https://$SyPUMServer/SyPUMcron -O /etc/cron.d/sypumclientcron
fi



#We also need to know, if reboot is required
if [ -f /var/run/reboot-required ]; then
	REBOOTREQ="YES"
else
	REBOOTREQ="NO"
fi

#Update this hosts rebootreq status
$CURL -k https://$SyPUMServer/api4.php/rebootreq/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER"/$REBOOTREQ

#Any Custom Scripts must Go here until says End of Custom Scripts

#End of Custom Scripts




#Check for orders from master
$CURL -k https://$SyPUMServer/api4.php/getupdates/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" | grep "yes" 

if [[ $? == 0 ]]; then
echo "I will now self Update"
#apt-get install emacs -y > /tmp/bb 2>&1
apt-get update > /tmp/$HOSTNAME 2>&1
$CURL -k -i -X POST https://$SyPUMServer/api4.php/logfile/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" -H "Content-Type: text/xml" --data-binary "@/tmp/$HOSTNAME" > /dev/null
$CURL -k https://$SyPUMServer/api4.php/resetupdatestatus/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" > /dev/null
else
echo "No updates pushed"
fi

/usr/lib/update-notifier/apt_check.py.mod --human-readable > /tmp/allsecuritypacks
#Get New Security Packages
$CAT /tmp/allsecuritypacks | awk -F'apt_pkg.Version object: Pkg:' '{print $2}' | awk -F' ' '{print $1}' | tr "'" " " > /tmp/new
#Get Security Package Versions
$CAT /tmp/allsecuritypacks | awk -F' Ver:' '{print $2}' | awk -F' ' '{print $1}' | tr "'" " " > /tmp/new3

/usr/lib/update-notifier/apt_check.py.mod2 > /tmp/allpackaswithvers
$CAT /tmp/allpackaswithvers | awk -F'apt_pkg.Version object: Pkg:' '{print $2}' | awk -F' ' '{print $1}' | tr "'" " " > /tmp/allpackaswithversname
$CAT /tmp/allpackaswithvers | awk -F' Ver:' '{print $2}' | awk -F' ' '{print $1}' | tr "'" " " > /tmp/allpackaswithversverss

#We update DB via API
$CURL -k https://$SyPUMServer/api4.php/flushallpackages/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER"

$RM -f /tmp/securityupdates_all
while read -r -u3 pack; read -r -u4 newvers; do
#  printf '%s;%s\n' "$pack" "$newvers"
#REGLHEADERS='([a-z]+)-([a-z]+)-([0-9\.]+)-([0-9]+)-generic'
LINUXHEADERSGENERIC='linux-headers-([0-9\.]+)-([0-9]+)-generic'
if [[ "$pack" =~ $LINUXHEADERSGENERIC ]]; then
OLDVer=`dpkg -l | grep linux-headers | sort -r | egrep 'linux-headers-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk -F" " '{print $3}' | xargs -L1`
SHORTDESC=`dpkg -l | grep linux-headers | sort -r | egrep 'linux-headers-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' | sed 's/%/percent/g' | sed 's/\// /g'  |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1`
fi
LINUXHEADERS='linux-headers-([0-9\.]+)-([0-9]+)'
if [[ "$pack" =~ $LINUXHEADERS ]]; then
OLDVer=`dpkg -l | grep linux-headers | sort -r | egrep 'linux-headers-([0-9\.]+)-([0-9]+)' | head -1  | awk -F" " '{print $3}' | xargs -L1`
SHORTDESC=`dpkg -l | grep linux-headers | sort -r | egrep 'linux-headers-([0-9\.]+)-([0-9]+)' | head -1  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' | sed 's/%/percent/g' | sed 's/\// /g'  |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1`
fi
LINUXIMAGEGENERIC='linux-image-([0-9\.]+)-([0-9]+)-generic'
if [[ "$pack" =~ $LINUXIMAGEGENERIC ]]; then
OLDVer=`dpkg -l | grep linux-image | sort -r | egrep 'linux-image-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk -F" " '{print $3}' | xargs -L1`
SHORTDESC=`dpkg -l | grep linux-image | sort -r | egrep 'linux-image-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' | sed 's/%/percent/g' | sed 's/\// /g'  |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1`
fi
LINUXIMAGEEXTRAGENERIC='linux-image-extra-([0-9\.]+)-([0-9]+)-generic'
if [[ "$pack" =~ $LINUXIMAGEEXTRAGENERIC ]]; then
OLDVer=`dpkg -l | grep linux-image | sort -r | egrep 'linux-image-extra-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk -F" " '{print $3}' | xargs -L1`
SHORTDESC=`dpkg -l | grep linux-image | sort -r | egrep 'linux-image-extra-([0-9\.]+)-([0-9]+)-generic' | head -1  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' | sed 's/%/percent/g' | sed 's/\// /g'  |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1`
fi

if [[ ! "$pack" =~ $LINUXIMAGEEXTRAGENERIC && ! "$pack" =~ $LINUXIMAGEGENERIC && ! "$pack" =~ $LINUXHEADERS && ! "$pack" =~ $LINUXHEADERSGENERIC ]]; then
OLDVer=`dpkg -l "$pack" | grep "$pack" | awk -F" " '{print $3}' | xargs -L1`
SHORTDESC=`dpkg -l "$pack" | grep "$pack"  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' | sed 's/%/percent/g' | sed 's/\// /g'  |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1`
fi

if [ -z $OLDVer ]; then
OLDVer="unknown"
fi

if [[ ! -z $pack ]]; then
echo "$pack@@@@$OLDVer@@@@$newvers@@@@$SHORTDESC" >> /tmp/securityupdates_all
fi
done 3</tmp/new 4</tmp/new3

$CURL -k -i -X POST https://$SyPUMServer/api4.php/allsecupdates/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" -H "Content-Type: text/xml" --data-binary "@/tmp/securityupdates_all"

$RM -f /tmp/allupdates

while read -r -u3 pack; read -r -u4 newvers; do
#  printf '%s;%s\n' "$pack" "$newvers"
OLDVer=`dpkg -l "$pack" | grep "$pack" | awk -F" " '{print $3}' | xargs -L1 -0`
if [ -z $OLDVer ]; then
OLDVer="unknown"
fi

SHORTDESC=`dpkg -l "$pack" | grep "$pack"  | awk '{$1=$2=$3=$4=""; print $0}' | sed -e 's/^[ \t]*//' |  sed 's/%/percent/g' | sed 's/\// /g' |sed s/"'"/"\""/g | sed 's/\[//g' | sed 's/\]//g' | xargs -L1 -0`
if [[ ! -z $pack ]]; then
echo "$pack@@@@$OLDVer@@@@$newvers@@@@$SHORTDESC" >> /tmp/allupdates
fi
done 3</tmp/allpackaswithversname 4</tmp/allpackaswithversverss

$CURL -k -i -X POST https://$SyPUMServer/api4.php/allupdates/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" -H "Content-Type: text/xml" --data-binary "@/tmp/allupdates" 

#Find out if we're already part of the system
$CURL -k https://$SyPUMServer/api4.php/amisubscribed/$HOSTNAME/$IPADDR/$RELEASE/"$UBUVER" | grep "$HOSTNAME"

#echo $?
if [ $? == 1 ]; then
echo "we aint subscribed ... then we subscribe"
$CURL -k https://$SyPUMServer/api4.php/subscribeme/"$HOSTNAME"/"$IPADDR"/$RELEASE/"$UBUVER"
else
echo "we subscribed yo .. So we check in"
$CURL -k https://$SyPUMServer/api4.php/checkin/"$HOSTNAME"/"$IPADDR"/$RELEASE/"$UBUVER"
fi

fi #ends isbanned

$RM -f /tmp/sypumclient.lock
fi

