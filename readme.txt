=== NOAA Weather ===
Contributors: Tim Berneman
Donate link: http://www.berneman.com/noaa-weather
Tags: NOAA,weather,forecast,wordpress,plugin,widget,US,United States,local
Requires at least: 3.0.0
Tested up to: 3.1
Stable tag: trunk

Get NOAA weather information in the sidebar for your locale. Note that NOAA reports weather for US States, Commonwealths, & Territories only.

== Description ==

The NOAA Weather widget will show the current weather and weather icons for any locale in the United States (including the commonwealths & territories) that NOAA reports on.  It will automatically add the necessary information into the WordPress cron to update every 30 minutes.

Please remember to come back and Rate this plugin as well as report the Compatability of this plugin.  If you have any questions, problems or suggestions please don't hesitate to email me at tim@berneman.com and I will respond quickly.

To find your code go to this link http://www.weather.gov/xml/current_obs/ and find your state or location in the dropdown list and click the "Find" button. On the next screen find your 'Observation Location' and the code you need is in parenthesis after the location name.

Depending on your theme you may need to tweak the CSS file.

You can have multiple instances of the widget on the same page.

This widget periodically downloads an XML file from NOAA into the widget folder so it will need the appropriate permissions.

== Pro Version ==

Depending on how well received this widget is and requests for enhancements, I may make a paid for "Pro" version.  I would probably ask for a nominal fee (around $10-$20 USD) for this enhanced version.  The "Pro" version most likely would include the current conditions as well as a 3 or 5 day forecast and weather alerts.  Depending on the usefulness, I may make shortcodes to include the weather in posts or other places.  Please send me an email at tim@berneman.com if you would be interested in a pro version. Your suggestions would also be most welcome!


== Installation ==

###Upgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

###Uploading The Plugin###

Extract all files from the ZIP file, **making sure to keep the file/folder structure intact**, and then upload it to '/wp-content/plugins/'.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Plugin Activation###

Go to the admin area of your WordPress install and click on the "Plugins" menu. Click on "Activate" for the "NOAA-Weather" plugin.

###Plugin Usage###

Use as a widget wherever widgets are allowed in your theme.


== Upgrade Notice ==
Upgrade using the Wordpress Admin or overwrite your files/folder with the new files/folder.


== Frequently Asked Questions ==

= Why am I getting "Weather Unavailable or invalid NOAA code." in my widget? =
First, make sure you are using the latest version of the widget. If you are still getting this message, most likely you've entered an invalid code or you have just installed the widget. If you've just installed the widget, make sure you have entered in a code and that it is valid.


== Screenshots ==

1. Widget setup page.
2. Widget display with default theme.
3. Widget display in another theme formatted a little nicer.


== ChangeLog ==

= 1.0.2 =
The widget should pass markup validation now.
The NOAA code is trimmed of any extraneous spaces and is uppercased.
The weather is now retrieved immediately when you add/change the NOAA code. There is no need to deactivate/activate the plugin to get the weather to update.
If there is no value for Windchill, Dewpoint is displayed instead.

= 1.0.1 =
Added code to create "twicehourly" cron schedule.

= 1.0.0 =
This is the first version released to the public.

= 0.9.1 =
Some minor tweaks to the css file.

= 0.9.0 =
First beta release to select test group.
