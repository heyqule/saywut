Version History
===============

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
