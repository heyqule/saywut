Version History
===============

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
