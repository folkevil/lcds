![Project logo](https://raw.githubusercontent.com/jf-guillou/lcds/master/web/images/lcds_logo-200.png)

# Light Centralized Digital Signage

There are already dozens of digital signage managers. But no easy to use open-source CMS based projects.

Now there's this one. **Early beta** though. Expect a lot of updates on master branch, there is no STABLE release yet.

Based on the [Yii2 framework](http://www.yiiframework.com/).
See [https://github.com/jf-guillou/lcds/blob/master/composer.json](composer.json) for the complete list of extensions used in this project.

[![Build Status](https://travis-ci.org/jf-guillou/lcds.svg?branch=master)](https://travis-ci.org/jf-guillou/lcds) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jf-guillou/lcds/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jf-guillou/lcds/?branch=master) [![GitHub license](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://raw.githubusercontent.com/jf-guillou/lcds/master/LICENSE.md)

**Table of contents**
- [Requirements](#requirements)
- [Installation](#installation)
  - [Install app](#install-app)
  - [Database](#database)
  - [App configuration](#app-configuration)
  - [Web Server configuration](#web-server-configuration)
    - [Apache](#apache)
    - [Nginx](#nginx)
  - [Ready](#ready)
- [Upgrade](#upgrade)
- [Client](#client)
- [Raspberry Pi Client](#raspberry-pi-client)
  - [Client installation](#client-installation)
  - [Auto-Configuration](#auto-configuration)
  - [Manual configuration](#manual-configuration)



## REQUIREMENTS

- PHP >= 5.6
- php5-ldap / php7-ldap
- MySQL >= 5.5 OR MariaDB >= 10.0
- [Composer](https://getcomposer.org/)
> Lower PHP versions are unsupported but should work

### Optional

- youtube-dl : Used by HostedVideo sideloader -- Make sure to keep updated
```bash
sudo wget https://yt-dl.org/downloads/latest/youtube-dl -O /usr/local/bin/youtube-dl
sudo chmod a+rx /usr/local/bin/youtube-dl
```

## INSTALLATION

**Do not forget to change /path/to/install in the following guide to your liking.**

### Install app

```bash
composer self-update
composer global require "fxp/composer-asset-plugin:^1.2.0"
cd /path/to/install
git clone https://github.com/jf-guillou/lcds.git
cd lcds
composer install --no-dev
```

### Database

Edit the database configuration file `config/db.php` according to your settings.
> Make sure to modify the `dbname=`, `username` and `password`.

Then run the migrations to pre-fill the database :
```bash
./yii migrate --interactive=0
```

### App configuration

Edit the app configuration file `config/params.php` :

- `language` - Currently supported :
  - `en-US`
  - `fr-FR`
- `prettyUrl` - See [Enable pretty URLS](#enable-pretty-urls)
- `cookieValidationKey` - Should be automatically generated by composer, should not be modified
- `proxy` - Outgoing proxy if necessary, used by media downloaders
- `useKerberos` - If the web server is Kerberos enabled
- `kerberosPrincipalVar` - The HTTP environment variable containing the user principal for Kerberos
- `useLdap` - Use LDAP for authentication backend
- `ldapOptions` - Common options for LDAP, default values should help you understand their meaning
- `cookieDuration` - Cookie duration in seconds, 0 will disable cookies
- `agenda` - Agenda image renderer configuration
  - `calendarTimezone` - Timezone input from calendar feed
  - `displayTeachers` - Display teachers name if space is not an issue
- `weather` - Weather renderer configuration / See https://darksky.net/dev for more details
  - `language` - Summary text language
  - `units` - Temperature units
  - `apikey` - API key used to fetch weather status
  - `withSummary` - Display text summary alongside icon


### Web Server configuration

The [Yii2 framework](http://www.yiiframework.com/) document root is in the /web folder

#### Apache
```
DocumentRoot "/path/to/install/lcds/web"
```

##### Enable pretty urls
Add to Apache configuration
```
<Directory "/path/to/install/lcds/web">
    # use mod_rewrite for pretty URL support
    RewriteEngine on
    # If a directory or a file exists, use the request directly
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # Otherwise forward the request to index.php
    RewriteRule . index.php
    # ...other settings...
</Directory>
```

#### Nginx
```
root /path/to/install/lcds/web;
```

##### Enable pretty urls
Add to Nginx configuration
```
location / {
    # Redirect everything that isn't a real file to index.php
    try_files $uri $uri/ /index.php$is_args$args;
}
```

### Ready

The app should be ready to use.
Do not hesitate to report bugs by posting an [issue at Github](https://github.com/jf-guillou/lcds/issues)


## UPGRADE

```bash
composer self-update
composer global update
cd /path/to/install/lcds
git pull
composer install --no-dev
./yii migrate --interactive=0
```

## CLIENT

The client configuration mostly depends on the hardware you are going to use.

Basically, just set the homepage of the browser to your web server address with '/frontend' suffix

`https://lcds-server/frontend`

## Raspberry Pi Client

The whole project was based on the idea that a proper digital signage could be handled with Raspberry Pi only.

The server part can also be handled by a Raspberry Pi, there isn't much performance required for this CMS.

The client part requires a little more configuration due to the wonky video hardware acceleration on these type of chips.

### Client installation

I recommend using the [minibian](https://minibianpi.wordpress.com/) OS. Trimmed of unused libraries and still fully compatible with Raspbian apps.

As of today, the latest version is [Minibian 2016-03-12](https://minibianpi.wordpress.com/2016/03/12/minibian-2016-03-12-is-out/), [direct download](https://sourceforge.net/projects/minibian/files/2016-03-12-jessie-minibian.tar.gz/download)

- Burn this image on a 4Gb or more µSD card using the [appropriate tool](https://minibianpi.wordpress.com/setup/)
- Connect RPi to a screen and network (DHCP)
- Login by SSH using root / raspberry
- Do not forget to change root password with
  `passwd`
- Extend the partition to fill SD card
  - Automatically : `apt update && apt install -y raspi-config && raspi-config nonint do_expand_rootfs && reboot`
  - Manually : https://minibianpi.wordpress.com/how-to/resize-sd/

### Auto-Configuration

Configuration of the Raspberry Pi can be mostly automated, beside some prompts for specific details :

`wget "https://raw.githubusercontent.com/jf-guillou/lcds/master/web/tools/raspberrypi.sh" -O - | bash -s -`

This will install everything and configure most options at the beginning. This whole installation can take an hour.

Please also note that by default, the screen will shutdown at 6pm and reboot at 7am every weekday.
This can be modified using [crontab -e](https://help.ubuntu.com/community/CronHowto).

### Manual Configuration

**In case you don't trust an automatic installer**

Below are the complete explanations for the commands used in the auto-install script :

- Packages installation
```bash
apt update
apt upgrade -y
apt install -y apt-utils raspi-config
apt install -y keyboard-configuration console-data
apt install -y rpi-update nano sudo lightdm spectrwm xwit xserver-xorg python python-tk lxterminal
```

- Configure OS
```bash
raspi-config nonint do_memory_split 128
raspi-config nonint do_change_timezone
raspi-config nonint do_overscan 1
```

- Change root password
```bash
passwd
```

- Create autostart user & set its password
```bash
DISP_USER=pi
useradd -m -s /bin/bash -G sudo -G video $DISP_USER
passwd $DISP_USER
```

- Install browser
```bash
wget -qO - "http://bintray.com/user/downloadSubjectPublicKey?username=bintray" | sudo apt-key add -
echo "deb http://dl.bintray.com/kusti8/chromium-rpi jessie main" | sudo tee -a /etc/apt/sources.list
apt update
apt install omxplayer kweb youtube-dl
```
The kweb installer may prompt for suggested packages, you should always refuse them (N).

- Configure display
```bash
# Light DM autologin on user
sed -i s/#autologin-user=/autologin-user=$DISP_USER/ /etc/lightdm/lightdm.conf

# Spectrwm autostart script
echo "
disable_border        = 1
bar_enabled           = 0
autorun               = ws[1]:/home/$DISP_USER/autorun.sh
" > /home/$DISP_USER/.spectrwm.conf
chown $DISP_USER: /home/$DISP_USER/.spectrwm.conf
```

- Setup scripts
```bash
# Configuration
LCDS=$(whiptail --inputbox "Please input your webserver address (ie: 'https://lcds-webserver')" 0 0 --nocancel 3>&1 1>&2 2>&3)
CONFIG=$(whiptail --title "Configuration" --separate-output --checklist "Select configuration options" 0 0 0 \
  "WIFI" "Install wifi modules" OFF \
  "SQUID" "Use internal Squid caching proxy" ON 3>&1 1>&2 2>&3)
WIFI=0
SQUID=0
for c in $CONFIG ; do
  case $c in
    "WIFI") WIFI=1 ;;
    "SQUID") SQUID=1 ;;
    *) ;;
  esac
done

echo '#!/bin/bash
# Logs storage
LOGS="./logs"

# Enable Squid
SQUID=$SQUID # 1 or 0

# Enable Wifi
WIFI=$WIFI # 1 or 0

# Frontend
LCDS="$LCDS"
' > /home/$DISP_USER/config.sh
chown $DISP_USER: /home/$DISP_USER/config.sh
chmod u+x /home/$DISP_USER/config.sh

# Load configuration
. /home/$DISP_USER/config.sh

# Scripts
sudo -u $DISP_USER wget https://raw.githubusercontent.com/jf-guillou/lcds/master/web/tools/autorun.sh -O /home/$DISP_USER/autorun.sh
chmod u+x /home/$DISP_USER/autorun.sh

sudo -u $DISP_USER mkdir /home/$DISP_USER/bin

sudo -u $DISP_USER wget https://raw.githubusercontent.com/jf-guillou/lcds/master/web/tools/connectivity.sh -O /home/$DISP_USER/bin/connectivity.sh
chmod u+x /home/$DISP_USER/bin/connectivity.sh
```

- Configure browser in kiosk mode
```bash
echo "-JEKR+-zbhrqfpoklgtjneduwxyavcsmi#?!.," > /home/$DISP_USER/.kweb.conf

chown $DISP_USER: /home/$DISP_USER/.kweb.conf
```

- Configure media player
```bash
echo "
omxplayer_in_terminal_for_video = False
omxplayer_in_terminal_for_audio = False
useAudioplayer = False
useVideoplayer = False
" >> /usr/local/bin/kwebhelper_settings.py

sudo $DISP_USER wget https://raw.githubusercontent.com/jf-guillou/lcds/master/web/tools/omxplayer -O /home/$DISP_USER/bin/omxplayer
chmod u+x /home/$DISP_USER/bin/omxplayer
```

- Configure local proxy
```bash
if [ $SQUID -eq 1 ] ; then
apt install -y squid3

echo "http_port 127.0.0.1:3128

acl localhost src 127.0.0.1

http_access allow localhost
http_access deny all

cache_dir aufs /var/spool/squid3 1024 16 256
maximum_object_size 256 MB

cache_store_log /var/log/squid3/store.log
read_ahead_gap 1 MB

refresh_pattern -i (\.mp4|\.jpg|\.jpeg) 43200 100% 129600 reload-into-ims

strip_query_terms off
range_offset_limit none
" >> /etc/squid3/squid.local.conf
echo "include /etc/squid3/squid.local.conf" >> /etc/squid3/squid.conf
fi
```

- Configure Wifi
```bash
if [ $WIFI -eq 1 ] ; then
apt install -y firmware-brcm80211 pi-bluetooth wpasupplicant

SSID=$(whiptail --inputbox "Please input your wifi SSID" 0 0 --nocancel 3>&1 1>&2 2>&3)
PSK=$(whiptail --passwordbox "Please input your wifi password" 0 0 --nocancel 3>&1 1>&2 2>&3)

echo "ctrl_interface=/run/wpa_supplicant
update_config=1

" > /etc/wpa_supplicant/wpa_supplicant-wlan0.conf
wpa_password "$SSID" "$PSK" >> /etc/wpa_supplicant/wpa_supplicant-wlan0.conf
echo "
auto wlan0
allow-hotplug wlan0
iface wlan0 inet manual" >> /etc/network/interfaces
fi
```

- Configure network
```bash
sed -i s/iface eth0 inet dhcp/iface eth0 inet manual/ /etc/network/interfaces
```

- Configure auto shutdown
```bash
echo "0 18 * * 1-5 touch /tmp/turnoff_display >> /home/pi/autorun.log 2>&1
0 7 * * 1-5 /usr/bin/sudo /sbin/reboot >> /home/pi/autorun.log 2>&1
" >> /var/spool/cron/crontabs/root
```
This will make the screen black after 6pm and reboot the pi at 7am.
The reboot is not mandatory, but helps a lot with the general wonkyness of the RPi.

- Firmware update
```bash
rpi-update && reboot
```

- Ready
If 
The browser should start, register with lcds server and display the authorization screen.
