=== RF Twitter Post ===
Contributors: layotte
Tags: publish, automatic, facebook, twitter, linkedin, friendfeed, leenkme, leenk.me, social network, social media, social media tools
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.5.9

A simple plugin that will post to twitter whenever you add a new post to your wordpress blog. 

== Description ==

= ATTENTION TWITTER POST USERS =
On August 16th, 2010, Twitter killed their basic authentication API which was used by Twitter Post. In anticipation of this, we created a new service called [leenk.me](http://leenk.me/). If you want to continue publishing to your Twitter account whenever you publish a new post in WordPress, we recommend using the [leenk.me WordPress plugin](http://leenk.me/download/leenkme.zip). RF Twitter Post will no longer work or be supported.

== Installation ==

On August 16th, 2010, Twitter killed their basic authentication API which was used by Twitter Post. In anticipation of this, we created a new service called [leenk.me](http://leenk.me/). If you want to continue publishing to your Twitter account whenever you publish a new post in WordPress, we recommend using the [leenk.me WordPress plugin](http://leenk.me/download/leenkme.zip). RF Twitter Post will no longer work or be supported.

== Frequently Asked Questions ==

On August 16th, 2010, Twitter killed their basic authentication API which was used by Twitter Post. In anticipation of this, we created a new service called [leenk.me](http://leenk.me/). If you want to continue publishing to your Twitter account whenever you publish a new post in WordPress, we recommend using the [leenk.me WordPress plugin](http://leenk.me/download/leenkme.zip). RF Twitter Post will no longer work or be supported.

== Changelog ==
= 1.5.9 =
* Final Update to Twitter Post. Twitter Post will no longer work after Twitter kills their basic authentication API on August 16th. To continue posting to Twitter, install the [leenk.me WordPress plugin](http://wordpress.org/extend/plugins/leenkme/).

= 1.5.8 =
* Fixed some deprecated functions, reduced some code bloat, updated some error checking.

= 1.5.7 =
* Fixed some PHP Warnings caused by Object to String conversions.

= 1.5.6 =
* Fixed a few bugs discovered while creating new Twitter oAuth service.

= 1.5.5 =
* Fixed exclude post issue.

= 1.5.4 =
* Removed link to survey.

= 1.5.3 =
* Added link to survey about [what we should do when Twitter breaks Twitter Post] (http://fullthrottledevelopment.com/what-should-we-do-when-twitter-breaks-twitter-post)

= 1.5.2 =
* Fixed bug introduced in WordPress 3.0 with publishing pages

= 1.5.1 =
* Fixed case sensitivity issue in tweet format
* Added ability to ReTweet a published post
* Removed and cleaned up some code
* Cleaned up some validation techniques for test tweet feature

= 1.5.0 =
* Added ability to send a test tweet to Twitter (to verify everything is working); this bumps the support up to start at WP2.8 but will allow me to add a "re"-tweet feature in a later version.
* Made some efficiency fixes
* Made some styling changes to match current WordPress styling
* Setup partial error reporting (as part of the test tweet) which I will extend into a debugging feature in a later version

= 1.4.0 =
* Discovered WP_Http class (since WP2.7) which makes life much easier for everyone, but this bumps the support up to start at WP2.7
* Removed cURL requirement, switched to WP_Http API
* Removed Twitter API Classes, switched to WP_Http API
* Changes in cURL requirement required modification of init() function

= 1.3.5 =
* Moved URL shortening functionality for improved efficiency

= 1.3.4 =
* Moved exclusion check for efficiency
* Fixed bug in scheduled posts, if a secondary account schedules a post and logs out, it would not have tweeted the message

= 1.3.3 =
* Had a typo when checking the PHP Version for PHP5 functionality

= 1.3.2 =
* Fixed bug that prevented TwitterPost from tweeting when setting a custom tweet on a Post page

= 1.3.1 =
* Fixed bug with category exclusion logic... accidentally brought it back in with version 1.3.0

= 1.3.0 =
* Cleaned up and remove 139 lines of code
* Fixed second bug with category exclusion logic

= 1.2.2 =
* Fixed bug with category exclusion logic
* Added fullthrottledevelopment profile as contributor

= 1.2.1 =
* Fixed PHP cURL Requirement Error Message
* Added PHP cURL Requirement skip if Twitter Friendly Links is already installed.
* Added ability for WP Admin to set Twitter Post to tweet from all Author accounts whenever a post is published.

= 1.2.0 =
* Changed default tweet from "Blogged %TITLE%: - %URL%" to "Blogged %TITLE%: %URL%".
* Added check to make sure PHP Curl is installed.
* Fixed bug that caused Twitter Post to tweet when adding new page.
* Added ability to specify which categories to include/exclude in tweet.
* Added ability to exclude a post before publishing it.
* Added support for using the Twitter Friendly Links URL instead of TinyURL if activated
* Added support for multiple author twitter accounts and default twitter account.

= 1.1.1 =
* Fixed support URLs.

= 1.1.0 =
* Fixed Default Tweet typo ("blogged" instead of "bloggged").
* No longer publishes to twitter when you update/edit an old post.
* Added feature to customize tweet per post using Custom Fields.
* Changed project URL to http://fullthrottledevelopment.com/ - my new business venture.

= 1.0.0 =
* Initial Release

== Screenshots ==

1. None at this time