# Ship

An open hardware monitoring platform, for the rest of us.

## Install

1. Install the framework:

        composer install

2. Configure:

        cp .env.example .env
        nano .env

   You'll most likely want to change `COMPONENT_DISKACTIVITY_DISKS` and `COMPONENT_NETTRAFFIC_INTERFACES`, and if you don't have a UPS, uncomment `COMPONENT_UPS=false`.

3. Run the installer:

        php artisan ship:install

4. Install the schedule cron job:

        echo '* * * * * www-data php /path/to/artisan schedule:run 2>&1 > /dev/null' > /etc/cron.d/ship3
        service cron restart

5. Create a virtual host

        <VirtualHost *:80>
            ServerName ship.example.com
            DocumentRoot /var/www/ship3
            
            # don't allow outside traffic
            <Location "/">
                <RequireAll>
                    Require ip 10.0.0.0/16
                </RequireAll>
            </Location>
        </VirtualHost>
 
        service apache2 reload

## Configuration

All components can be enabled or disabled by specifying `COMPONENT_{NAME}=true|false` in `.env`.

### Ship

  - `SHIP_TITLE`

    Name to display in the header and window title. By default, it is the machine's hostname.

  - `SHIP_DARK_MODE`

    Set to `true` to use the dark theme by default.

  - `SHIP_PERIOD`

    Default graph/data difference period to use. Must be in a format that [DateInterval](http://php.net/manual/en/dateinterval.format.php) can recognize.

  - `SHIP_AUTORELOAD`

    Specify whether or not Ship should reload automatically (once per minute).

  - `APP_LOCALE`

    The language you'd like to use. Ship currently supports English (`en`) and French (`fr`) translations.

#### Lumen framwork settings

Most settings supported by the Lumen framework can be overridden as well. (Not all of them are relevant to Ship, however.) Refer to each module's documentation for details on connecting to a different database, using a different language

### Every component

  - `COMPONENT_{NAME}`

    Set to `false` to disable the component.

  - `COMPONENT_{NAME}_ORDER`

    Set where the component should appear on the page. (0 is the top-left corner; components fill across.)

### Aptitude

  - `COMPONENT_APTITUDE_CACHE`

    How long (in minutes) to cache the package list for. (Regenerating the list every time can be slow.)

  - `COMPONENT_APTITUDE_PACKAGES`

    Whether or not to include the package list. This has security implications if enabled, as anyone who has access to Ship will be able to see what versions of upgradeable packages are installed.

### Disk Activity

  - `COMPONENT_DISKACTIVITY_DISKS`

    A comma-separated list of disks (as their device names, as they appear in `/dev` or the `df` command) to monitor and graph.

  - `COMPONENT_DISKACTIVITY_BLOCKSIZE`

    The block size of the disks that are being monitored. This may be one number to apply to all disks, or a comma-separated list (of the same length as the disk list) to apply certain block sizes to individual disks.

### Info

  - `COMPONENT_INFO_UPTIME_FORMAT`

    The format in which the uptime will be displayed, as a tokenized string:

    **Time tokens:**

    | Key  | Description         |
    |------|---------------------|
    | `@s` | Seconds piece       |
    | `@m` | Minutes piece       |
    | `@h` | Hours piece         |
    | `@d` | Days piece          |
    | `@M` | Total minutes       |
    | `@H` | Total hours         |
    | `@G` | Total hours + days  |
    | `@D` | Total days          |

    **Label tokens:**

    | Key  | Description         |
    |------|---------------------|
    | `_m` | Minute label        |
    | `_h` | Hour label          |
    | `_d` | Day label           |
    | `_M` | Total minutes label |
    | `_H` | Total hours label   |
    | `_D` | Total days label    |

    **Examples:**
    
    | Format string   | Output        |
    |-----------------|---------------|
    | `@d_d @h:@m:@s` | 25d 16:45:20  |
    | `@G_H @m_m`     | 616h 45m      |
    | `@H_H`          | 616.75h       |
    | `@D_D`          | 25.68d        |
    | `@m$@s@m_dello` | 45$2045dello  |

### Network Addresses

  - `COMPONENT_NETADDRESS_CACHE`

    How long, in minutes, to cache external IP addresses for, to not spam Rackspace's IP-checking server. (Ship uses [icanhazip.com](http://icanhazip.com) to detect external IP addresses.)

  - `COMPONENT_NETADDRESS_IPv4`, `COMPONENT_NETADDRESS_IPv6`

    Set to false to bypass checking for an external IPv4/IPv6 address.

### Network Traffic

  - `COMPONENT_NETTRAFFIC_INTERFACES`

    A comma-separated list of interfaces (as they appear in the `ifconfig` or `ip link` commands) for which to monitor and graph traffic.

### Processes

  - `COMPONENT_PROCESSES_COUNT`

    The number of processes to show in each category.

  - `COMPONENT_PROCESSES_EXECUTABLE`

    Path to the `ps` command, if it isn't already in `$PATH`.

  - `COMPONENT_PROCESSES_ARGS`

    Argument set to use for `ps`, since its implementation tends to vary across platforms. "`linux`" will use the Linux arguments, and "`freebsd`" will use the FreeBSD arguments. Ship will try to autodetect the operating system, so you probably shouldn't have to set this unless it is not working.

### UPS

  - `COMPONENT_UPS_EXECUTABLE`

    Path to the `apcaccess` command, if it isn't already in `$PATH`.

  - `COMPONENT_UPS_HOST`

    If the UPS device isn't connected to the current machine, you may specify a hostname or IP address to get UPS information from instead.

## License

**The MIT License (MIT)**

Copyright (c) 2016 Alex Lindeman

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
