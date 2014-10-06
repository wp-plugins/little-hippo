=== Little Hippo ===
Contributors: erbuc
Donate link: http://www.littlehippo.co/donate/
Tags: little hippo, seo tools, image tagger
Requires at least: 3.5.1
Tested up to: 4.0
Stable tag: 0.4.5
License: GPLv2 or later

Little Hippo is a multifunction plugin for agencies and site owners that speeds up the process
of onsite optimisation.

== Description ==

Little Hippo is a Multifunction plugin for agencies and site owners that speeds up the process 
of onsite optimisation. By bringing the page Meta, image Meta into a single area you can fly 
through the mark-up process.

We are still in Beta and adding new functions towards a 1.0.0 release soon.

We have included a quick look dashboard that allows you to see at a glance issues with your 
onsite issues, without having to run a scan at all, it gives you an instant idea on how much 
work you have ahead of you.

We have also added the some of the most important settings you need for a site into one place, 
with a single click to make all outgoing links no follow, an easy to use tabbed default title 
and naming convention, and we have beefed up the google analytics input, giving you the 
ability to edit the bounce rate time out in seconds.

We have many more features coming, in new iterations, like page and site speed edits, cdn 
management, Redirect management, robots and HT access management, webmaster tools notifications 
and much more.

Little hippo will be the first and last SEO plugin that you will need to install on any site!

## Features:
+ View all meta issues on site via a dashboard on site
+ Bulk SEO title and Meta data completion
+ Set defaults for meta tiles and description on all pages
+ Bulk Image alt & text completion.
+ Quick copy meta information form post titles to image meta
+ Overview of your optimisation issues in a dashboard
+ Add Google Analytics via the unique UA code
+ Edit the Google bounce rate timeout for better bounce numbers
+ Set all out-bound links to no-follow
+ Manage Facebook OG tags and defaults
+ Bulk empty all trash on site

More information can be found on the plug-in website at http://www.littlehippo.co

== Installation ==

This section describes how to install the plugin and get it working.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'plugin-name'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `little-hippo.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `little-hippo.zip`
2. Extract the `little-hippo` directory to your computer
3. Upload the `little-hippo` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= Why do the number of Issues not Add up on the Dashboard? =
The issues are tallied on a per issue type basis. For example, Little HIppo will examine the 
meta title value. If it is missing, the result will end up counting the missing title as 1 for 
"title is too short", and 1 for "missing". Once the meta title is added, and it falls within 
the limits, both issues will subtract 1 from their total. The total issues only counts this 
as a single issue however. This is so each reporting element can stand on it's own when you 
are evaluating your web site for SEO readiness.

= Does this work with the newest version of wordpress? =
We ensure that this is built to the newest build of WP (4.0) and will continue to update it

= Does Little Hippo work with other SEO Plug-ins? =
Yes, we have written Little Hippo so it will work with either All-in-one SEO or (Yoast) 
WordPress SEO. This way, you do not need to re-enter any of your data or settings if you are 
currently using either of these plug-ins. 
In an upcoming release, we will offer the ability to copy your data from your existing 
plug-in into Little Hippo.

= Will this work with all themes? =
We have built it to work with the WordPress core, not the theme so you will have hassle 
free integration. However, some themes provide their own SEO components. These may conflict 
with any values input in Little Hippo.

= Why do the titles not show on all the images when I run a test on the site? =
WordPress Core removed the default alt and title Meta a few versions ago, and while we provide 
the option to add the titles to every image, WordPress doesn’t natively show that data. If you 
know php and HTML we recommend you update your theme to show this data. 
For some hints and recommendations, please look the user guide ** Coming Soon **

= The home page meta default isn’t showing! =
If you have Yeost or All in one SEO, the settings for home page will be controlled form there, 
in V2 and 3 we plan to move all the important functions into Little Hippo so you will not even 
need to use these other plugins.

= Why are Meta Title / Meta Desc fields highlighted as red?
If you have posts/pages that have not had a Meta Title or a Meta Desc defined, Little Hippo 
will suggest values for you. Little Hippo creates a default title from the post/page title 
and a description from the first 155 characters of the content. If these values have not been 
aved, Little Hippo will highlight the input field as read, indicating that you must save these 
values to accept them before they will be displayed on your website for search engines.

= Can we hire you to do our SEO? =
Yes of course, please contact us and we can discuss how we can help!

== Screenshots ==

![Little Hippo Dashboard](assets/screenshot-1.png "Screenshot of Little Hippo Dashboard")

== Changelog ==

= 0.4.4 Beta =
* Minor bug updates
* Trash clean-up section in settings
* Facebook OG Tagging automated based on SEO meta values

= 0.4.0 Beta =
* The first version of Little Hippo released to the public

== Upgrade Notice ==
Nothing to notify at this time