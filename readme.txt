=== RF Twitter Post ===
Contributors: layotte, fullthrottledevelopment
Donate link: http://fullthrottledevelopment.com/contact
Tags: twitter, tweet, autopost, autotweet, automatic, social networking, social media, posts, twitterpost, tinyurl, twitter friendly links, multiple authors, exclude post, category, categories
Requires at least: 2.6
Tested up to: 2.8
Stable tag: 1.3.0

A simple plugin that will post to twitter whenever you add a new post to your wordpress blog. 

== Description ==

Multiple Authors of your blog can have their own Twitter information setup under the User's section. So whenever they post it will send a tweet. It will also still send a tweet from the main Twitter account.
You can choose which categories are included or excluded.
You can now select individal posts not to be tweeted before you publish them.
The Admin can choose to tweet to all author accounts in the main Twitter Post options page.

This plugin allows you to tweet whenever you publish a new post.
You can customize the tweet message and include the title or url with tags %TITLE% or %URL% (respectively).
Twitter only allows up to 140 characters in a single tweet. Because of this restriction, the format + expanded tags cannot be more than 140 characters.
If you include the %URL% tag, it is given priority. If it cannot fit into the format because your text is to long then it will be excluded entriley.
If you include the %TITLE% tag and the post title causes the tweet to go over 140 characters, the plugin uses a substring of the post title, so it will fit in the tweet.

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