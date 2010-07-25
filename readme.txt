=== RF Twitter Post ===
Contributors: layotte, fullthrottledevelopment
Donate link: http://fullthrottledevelopment.com/donate
Tags: twitter, tweet, autopost, autotweet, automatic, social networking, social media, posts, twitter post, tinyurl, twitter friendly links, multiple authors, exclude post, category, categories, retweet
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.5.7

A simple plugin that will post to twitter whenever you add a new post to your wordpress blog. 

== Description ==

= ATTENTION TWITTER POST USERS =
Recently Twitter announced announced that they will be shutting off the "basic authentication" API used by many WordPress plugins — including Twitter Post. We have been working diligently to create a new service that uses Twitters recommended API. This new service will be launched a few days before Twitter shuts down their API. We will be charging 33 cents a month to use the service. We also plan on extending it to Facebook, Digg, Buzz, etc. We hope to have the Facebook connect setup before launch!

With Twitter Post every author of your blog can have their own Twitter information stored under the User's section. Whenever they post to your blog it will automatically tweet a message to the admin twitter accoutn as well as their own twitter account. The admin can also choose to send a tweet to all authors twitter accounts whenever anyone publishes a post.

With Twitter Post you can...
choose which categories are included or excluded
exclude individal posts from being tweeted before you publish them
retweet a published post*
choose to tweet to all authors
customize the tweet format, including the post title and post URL (using the custom tags %TITLE% and %URL%, respectively)**

Currently Twitter Post supports two URL shortening services. TinyURL is the default shortener, Twitter Post will attempt to get permalink of your post shortened by TinyURL. If it is unable to, it will use the regular site URL. The other shortener you can use is a WordPress plugin called [Twitter Friendly Links](http://wordpress.org/extend/plugins/twitter-friendly-links/). If Twitter Friendly Links is installed and activated on your website then Twitter Post will use it as the default shortener.

* Twitter no longer allows the ability to tweet the same exact message more than once. This is an attempt to reduce SPAM in their system. I am not trying to encourage SPAM with the ReTweet feature, but I felt like it was an important feature to include. Because of the limitation imposed by Twitter, I had to add a random element to each ReTweet. Currently a random digit between 10 and 99 will be appended to a ReTweet. Also, you will only see the ReTweet option for published posts.

** Twitter allows a maximum of 140 characters per tweet. If your custom format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).
[Support](http://fullthrottledevelopment.com/contact)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `rf-twitterpost` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Update Twitter Post Options with your twitter username, password, and the tweet format.
4. Next time you publish a new post it will update twitter.

== Frequently Asked Questions ==

= Where can I find help or make suggestions? =

http://fullthrottledevelopment.com/twitter-post

== Release History ==
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