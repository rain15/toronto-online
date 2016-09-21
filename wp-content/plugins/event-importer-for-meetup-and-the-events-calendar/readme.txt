=== Event Importer for Meetup and The Events Calendar ===
Contributors:      dabernathy89
Donate link:       https://paypal.me/DanielAbernathy
Tags:              meetup, calendar, events
Requires at least: 4.4
Tested up to:      4.6
Stable tag:        0.3.1
License:           GPLv2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Automatically import events from Meetup.com into The Events Calendar.

To get started, make sure that you have The Events Calendar plugin (by Modern Tribe) installed. This plugin will not do anything without it!

This plugin relies on WP Cron to run the import on a schedule, so it will not work if you have disabled WP Cron (sometimes this is done in wp-config.php).

After you have both plugins installed, you need to configure a couple of settings. First navigate to Events -> Import in your Dashboard. From there you can input your [Meetup.com API Key](https://secure.meetup.com/meetup_api/key/) and choose the post status that should be used for imports.

Next, click on the "Meetup.com" tab. From there you can fill in the URL for a Meetup group, select the Event Categories you want it to be mapped to, and hit "Add Recurring Import". This will schedule a script to run twice a day to import the upcoming events from that group into The Events Calendar. You can add as many groups as you'd like.

*This is an early release of this plugin, so it's a little barebones. Help make it better by contributing yourself - see below!*

= Contributing =

You can contribute to development of the plugin on [Github](https://github.com/dabernathy89/meetup-importer-for-the-events-calendar/). Please don't use the repo for technical support. Pull requests will be welcomed for bug fixes and other improvements. Let me know before you start developing a new feature so that we can discuss it.

This plugin is built using the `generator-plugin-wp` Yo generator built by WebDevStudios.

= Disclaimer =

I am not affiliated with Meetup, Inc or Modern Tribe (developers of The Events Calendar). This plugin is not endorsed, sponsored, or certified by Meetup, Inc or Modern Tribe. I really hope they like it though.

== Screenshots ==

1. On the main import settings screen you can set your Meetup.com API Key and the default status of imported events.
2. On the Meetup.com import tab, you can set up recurring imports. Enter the URL of the meetup group and choose Event Categories that its events will be a part of.

== Installation ==

= Manual Installation =

1. Upload the entire `/event-importer-for-meetup-and-the-events-calendar` directory to the `/wp-content/plugins/` directory.
2. Activate Event Importer for Meetup and The Events Calendar through the 'Plugins' menu in WordPress.

== Changelog ==

= 0.3.1 =
* Bug fix: multiple venues were not importing correctly

= 0.3.0 =
* Add option to display an event's Meetup.com link

= 0.2.3 =
* Add screenshots to readme.

= 0.2.2 =
* Map event categories on import, update readme, code cleanup

= 0.2.1 =
* Update plugin name and readme

= 0.2.0 =
* Initial public release
