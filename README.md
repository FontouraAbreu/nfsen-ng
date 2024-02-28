# nfsen-ng

[![GitHub license](https://img.shields.io/github/license/mbolli/nfsen-ng.svg?style=flat-square)](https://github.com/mbolli/nfsen-ng/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/mbolli/nfsen-ng.svg?style=flat-square)](https://github.com/mbolli/nfsen-ng/issues)
[![Donate a beer](https://img.shields.io/badge/paypal-donate-yellow.svg?style=flat-square)](https://paypal.me/bolli)

nfsen-ng is an in-place replacement for the ageing nfsen.

![nfsen-ng dashboard view](https://github.com/mbolli/nfsen-ng/assets/722725/b3e6e8a5-185c-4347-9d43-d4aa9b09f35e)

## Used components

* Front end: [jQuery](https://jquery.com), [dygraphs](http://dygraphs.com), [FooTable](http://fooplugins.github.io/FooTable/), [ion.rangeSlider](http://ionden.com/a/plugins/ion.rangeSlider/en.html)
* Back end: [RRDtool](http://oss.oetiker.ch/rrdtool/), [nfdump tools](https://github.com/phaag/nfdump)

## TOC

* [nfsen-ng](#nfsen-ng)
  * [Installation](#installation)
  * [Configuration](#configuration)
  * [CLI](#cli)
  * [API](#api)
    * [/api/config](./API_ENDPOINTS.md#apiconfig)
    * [/api/graph](./API_ENDPOINTS.md#apigraph)
    * [/api/flows](./API_ENDPOINTS.md#apiflows)
    * [/api/stats](./API_ENDPOINTS.md#apistats)

## Installation

Detailed installation instructions are available in [INSTALL.md](./INSTALL.md). Pull requests for additional distributions are welcome.

Software packages required:

* nfdump
* rrdtool
* git
* composer
* apache2
* php >= 8.1

Apache modules required:

* mod_rewrite
* mod_deflate
* mod_headers
* mod_expires

PHP modules required:

* mbstring
* rrd

## Configuration

> *Note:* nfsen-ng expects the profiles-data folder structure to be `PROFILES_DATA_PATH/PROFILE/SOURCE/YYYY/MM/DD/nfcapd.YYYYMMDDHHII`, e.g. `/var/nfdump/profiles_data/live/source1/2018/12/01/nfcapd.201812010225`.

The default settings file is `backend/settings/settings.php.dist`. Copy it to `backend/settings/settings.php` and start modifying it. Example values are in *italic*:

* **general**
  * **ports:** (*array(80, 23, 22, ...)*) The ports to examine. *Note:* If you use RRD as datasource and want to import existing data, you might keep the number of ports to a minimum, or the import time will be measured in moon cycles...
    * **sources:** (*array('source1', ...)*) The sources to scan.
    * **db:** (*RRD*) The name of the datasource class (case-sensitive).
  * **frontend**
    * **reload_interval:** Interval in seconds between graph reloads.
  * **nfdump**
    * **binary:** (*/usr/bin/nfdump*) The location of your nfdump executable
    * **profiles-data:** (*/var/nfdump/profiles_data*) The location of your nfcapd files
    * **profile:** (*live*) The profile folder to use
    * **max-processes:** (*1*) The maximum number of concurrently running nfdump processes. *Note:* Statistics and aggregations can use lots of system resources, even to aggregate one week of data might take more than 15 minutes. Put this value to > 1 if you want nfsen-ng to be usable while running another query.
  * **db** If the used data source needs additional configuration, you can specify it here, e.g. host and port.
  * **log**
    * **priority:** (*LOG_INFO*) see other possible values at [http://php.net/manual/en/function.syslog.php]

## CLI

The command line interface is used to initially scan existing nfcapd.* files, or to administer the daemon.

Usage:

  `./cli.php [ options ] import`

or for the daemon

  `./cli.php start|stop|status`

* **Options:**
  * **-v**  Show verbose output
  * **-p**  Import ports data as well *Note:* Using RRD this will take quite a bit longer, depending on the number of your defined ports.
  * **-ps**  Import ports per source as well *Note:* Using RRD this will take quite a bit longer, depending on the number of your defined ports.
  * **-f**  Force overwriting database and start fresh

  * **Commands:**
    * **import** Import existing nfdump data to nfsen-ng. *Note:* If you have existing nfcapd files, better do this overnight.
    * **start** Start the daemon for continuous reading of new data
    * **stop** Stop the daemon
    * **status** Get the daemon's status

  * **Examples:**
    * `./cli.php -f import`
        Imports fresh data for sources

    * `./cli.php -f -p -ps import`
        Imports all data

    * `./cli.php start`
        Starts the daemon

## Logs

Nfsen-ng logs to syslog. You can find the logs in `/var/log/syslog` or `/var/log/messages` depending on your system. Some distribuitions might register it in `journalctl`. To access the logs, you can use `tail -f /var/log/syslog` or `journalctl -u nfsen-ng`

You can change the log priority in `backend/settings/settings.php`.

## API

The API is used by the frontend to retrieve data. The API endpoints are documented in [API_ENDPOINTS.md](./API_ENDPOINTS.md).
