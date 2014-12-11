esoTalk for ownCloud
====================

[esoTalk](http://esotalk.org) is a lightweight and very usable message board. This fork of 
esoTalk integrates it as an app into [ownCloud](http://owncloud.org). You can then access 
the forum as part of ownCloud, with integrated user management.

You install the esoTalk app within ownCloud by cloning this repository into a new directory 
`apps/board` within your ownCloud installation. You can then enable the esoTalk app in the 
ownCloud app selection dialog.

When you run the esoTalk app for the first time, you are guided through the initial 
configuration. Make sure to back esoTalk with the same database as ownCloud, because the 
esoTalk code will access the ownCloud user information to maintain an integrated user 
management.
