esoTalk for Nextcloud
=====================

[esoTalk](http://esotalk.org) is a lightweight and very usable message board. This fork of 
esoTalk integrates it as an app into [Nextcloud](https://nextcloud.com). You can then access 
the forum as part of Nextcloud, with integrated user management.

You install the esoTalk app within Nextcloud by cloning this repository into a new directory 
`apps/board` within your Nextcloud installation. You can then enable the esoTalk app in the 
Nextcloud app selection dialog.

When you run the esoTalk app for the first time, you are guided through the initial 
configuration. Make sure to back esoTalk with the same database as Nextcloud, because the 
esoTalk code will access the Nextcloud user information to maintain an integrated user 
management.
