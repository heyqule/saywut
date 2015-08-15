Version History
===============
0.8
--------------------
+ Various bug fix
+ now use \Saywut namespace

0.7
--------------------
+ Meta tag removal if value is empty
+ Force reindex now repopulate search table.
+ mass_import support (CLI only)
+ Enable keywords support
* Post Size query fix
* Change full text from natural language to boolean mode with + for each word

0.6
-------------------
Added keywords for posts
Uses Fulltext Search - title,contents,keywords column in posts
Various bug fixes

0.5
-----
Switch back to MySQL
Removed ACE code editor
Various changes and fixes

0.4
-----
Added Admin Section
Added Manual Posting support with ACE code editor
Added Weibo Lib
Various bug fixes and logic changes


0.3.3
-----
Use $Global for settings
Add defaults to CURL settings

0.3.2
-----
Twitter Bot update to use 1.1
Added twitteroauth to the library
Various fixes

0.3.1
-----
+ loadByQuery() in post collection. Uses %phase% to search contents.

0.3
-----
+ Twitter_Bot: Added Twitter Card simulation for URL.
+ Twitter_Bot: Tracks first expended_url in url entities
+ Twitter_Bot: Duplicated entry will by skipped by default
     Can be overwritten with overwrite config
+ Bot: Added Number of Changes to event tracking
+ Core: function to fetch meta tag from HTML
+ Post: function to load by provider content ID
* Various bug fixes


0.2.1
-----
+ Added Twitter image handler, tweet image url now stores in custom_data
+ Change logging to use event logger
+ Set runnable time to 1 minute ealier than targeted to prevent task slip to next minute
+ Fixed query string builder


0.2
---
+ Added cron event logging
+ Added internal job timer, depends on event log.
+ Rearranged file structure.
+ Incoming posts are REPLACED on id conflict at DB level, instead doing checks at PHP level.


0.1
---
Initial release, includes a twitter user timeline bot
