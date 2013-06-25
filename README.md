WTF is saywut bot?
==================
A collection of bots which archive posts from various sources, e.g twitter, to a central sqlite table. 

**DEMO**: <http://www.heyqule.com/#saywut>

Usage
-----
1. set up includes/config.php
2. run install.php
3. set up a cron job to run cron.php to get posts
4. use read.php to read posts as json.

## Reader.php Query Strings
+ offset=0 - page indicator

Add new bot
-----------
Bots are placed in bots directory

See Twitter_Bot as example on how to build one.


Registering new bot
-------------------
Bot config register which bot to run.

**class - required. identify the bot class**

**name - required. name of the bot**

interval - optional. the interval between each run.  Default is once a day

...  - add as many options as by your bot needs


**Example:**
$BOT_CONFIG[2] = array('class'=>'Twitter_Bot','name'=>'Twitter', 'interval'=>15...);



