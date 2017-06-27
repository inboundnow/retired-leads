=== WordPress Leads ===

Contributors: Hudson Atwell, David Wells, Giulio Dapreala, ahmedkaludi 
Donate link: mailto:hudson@inboundnow.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: leads, lead capture, lead tracking, lead collection, lead management, crm, crm tools
Requires at least: 3.8
Tested up to: 4.7.3
Stable Tag: 3.1.3

Track visitor activity, capture and manage incoming leads, and send collected emails to your email service provider.

== Description ==

>WordPress Leads works as a standalone plugin or hand in hand with [WordPress Landing Pages](http://wordpress.org/plugins/landing-pages/ "Learn more about WordPress Landing Pages") & [WordPress Calls to Action](http://wordpress.org/plugins/cta/ "Learn more about Calls to Action") to create a powerful & free lead generation system for your business.

Wordpress leads gives you powerful visitor tracking where Google Analytics leaves off ( aka tie website activity directly to a specific individual) It allows you to track every activity that a visitor takes on your site, before converting on a web form.

= Gather Valuable Intelligence on your Leads: =

* Learn where your leads are coming from geographically
* What pages they viewed on your site
* See past comments they have made on your blog
* Know what they have searched for on your site
* Learn where referral traffic is coming from
* See social media profiles and sites they own
* Gather demographic data
* See past/current job histories
* Learn their topical interests
* and much more.

This powerful lead data can help you sell smarter and more efficiently.

Wordpress Leads was originally built as an add-on for [WordPress Landing Pages](http://wordpress.org/plugins/landing-pages/ "Learn more about WordPress Landing Pages") but quickly grew into its own stand alone plugin to capture lead information on any page of your site.

Manage your leads directly from the admin interface, or send the lead data into a third party CRM of your choosing.

This plugin is built to be fully extendable by providing custom action and filter hooks, allowing for an almost endless number of powerful CRM application addons.

[Start Collecting Advanced Lead Intelligence in WordPress ](http://www.inboundnow.com/collecting-advanced-lead-intelligence-wordpress-free/ "Start Collecting Advanced Lead Intelligence in WordPress")


Wordpress Leads is Wordpress's first fully extendable CRM plugin.

= Highlights =

* Built in visual form builder that is dead simple to use.
* Automatically detect and collect visitor data from any submitted form.
* Track and see what pages your leads visited before converting on your site to gain valuable lead intelligence
* Integrates seamlessly with WordPress Landing Pages and WordPress Calls to Action Plugins
* Easily search, view, and modify lead (contact) information with bulk lead management tool
* Uses geolocation services to detect additional information on first time conversion.
* Sync your leads with a third party CRM like salesforce.com, Zoho or sugarCRM.
* [Connect with Zapier to send leads to over 300+ different CRM and Email marketing tools ](http://www.inboundnow.com/zapier/  "Connect with Zapier to send leads to over 300+ different CRM and Email marketing tools")
* Developers: Extend Leads with custom functionality via built in api
* Integrate with 39+ Email service providers for Email Autoresponder Campaigns
* Integrate with 36+ CRM providers for easy lead management

= About the Plugin =

This is a free plugin that was built to help people collect, store, and manage lead/contact information to better understand each lead that comes into their site. It's built with inbound marketing best practices in mind and integrates seamlessly with the free WordPress Landing Pages plugin.

= Developers & Designers =

We built Lead Management as a framework! You can use our extendable framework to bring custom solutions to your application.

[Follow Development on GitHub ](https://github.com/inboundnow/leads "Follow & Contribute to core development on GitHub")
 |
[Follow Development on Twitter ](https://twitter.com/inboundnow "Follow us for notifications")

== Installation ==

1. Upload `leads` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==

1. Leads Plugin is shipped with a series of dashboard widgets that provide insightful information about your inbound marketing campaign.
2. Pick and choose which lead lists to monitor performance on.
3. Inbound Pro Subscribers can view expanded data about lead page views.
4. Inbound Pro Subscribers can view expanded data about lead action events.
5. Leads are powered by WordPress custom post types. Here we see a general leads listing page.
6. Lead profiles are automatically created through Inbound Form submissions. They can also be manually created. Here we look at some of our mappable fields.
7. Inside our lead profile we collect various lead stats. Inbound Pro subscribers can view expanded statistics inside a popup.
8. Sources are recorded for a lead if they are available. Multiple sources can be collected.
9. In this screenshot we are still inside a lead profile. Set lead lists, tags, and view geolocation information if available.
10. Inbound Forms are our main tools for in-sourcing leads. We also have extensions that support Ninja Forms and Gravity Forms.
11. Here we see our administrative options, like what lead lists to add submissions to, what lead tags, and who should we notify via email?
12. When we setup our form fields we want to make sure our inputs are mapped to a lead profile.
13. Our Inbound Forms comes with limited styling options.
14. Leads plugin provides it’s own bulk processing interface for performing mass searches and actions on leads.
15. Here are a few of our bulk action options. Notice where we can export as CSV. Pretty cool!
16. After a successful lead submission the site administrator is sent a notification email.
17. Here’s a better look at our new lead notification email sent to administrators.

== Changelog ==

= 3.1.3 =
* Updating shared files.

= 3.1.2 =
* Delete automation rules on lead trash.
* Updating shared database tables
* Removing include and exclude 3rd party form options as we now support major 3rd party forms through extensions.
* Fixing issue with CTA list not populating inside of marketing button popup.

= 3.0.9 =
* Better Avada theme support

= 3.0.8 =
* Security improvements
* Updating readme screenshots
* Adding input for class name into Inbound Form styling options
* Adding sources to CSV export

= 3.0.5 =
* Removing geolocation box from core
* Adding 'source' field to lead retrieval calls inside Leads API
* Improving compatibility with WooCommerce 3.0

= 3.0.3 =
* FireFox support for datetime picker.
* Moved field mapping select input to a more visable location.
* Improved New Lead Notification email report

= 3.0.2 =
* [bugfix] Fixing issue with upgrade routine and funnel tracking.

= 3.0.1 =
* [new] Adding double optin to lead support.
* [refactor] General code improvements for speed and memory usage.
* [UI] Updated styling

= 2.8.1 =
* [fix] Restore ability to delete leads from lead listing page via bulk actions.
* [tweak] Maintenance work on Full Contact integration with Lead profile
* [tweak] Improved UI inside Lead Profile.
* [tweak] removing inbound_add_list events from action totals.
* [fix] Fixed broken page views in Lead Activity section. Now pulls from inbound_page_views table.
* [fix] Fixed broken Conversion Paths section. Now pulls from inbound_events table.
* [refactor] Now pulling lead sources from inbound_events table
* [enhancement] Better support for checklist and radio custom fields
* [enhancement] No lead tracking within admin

= 2.7.8 =
* [tweak] Adding lead status and lead tags to the Bulk Actions filter
* [fix] CSV exporting in Bulk Actions

= 2.7.7 =
* [fix] Fixing "Fatal error: Class ‘Leads_Settings’ not found" error.

= 2.7.3 =
* [improvement] Adding settings support for Inbound Pro
* [fix] fixing include file for Inbound Forms when wp-config.php is outside of it's normal location.

= 2.7.0 =
* Fixing broken 'Bulk Actions' page.
* Fixing CSV Export Issue.

= 2.6.8 =
* Updating shared files.

= 2.2.6 =
* Fixing issue with duplicate leads displaying in Bulk Actions section
* Better CSS for Lead profile

= 2.2.5 =
* Added inbound_page_views & now monitoring page view reports.
* Added better sanitation of _GET, _REQUEST, & POST variables.
* Better CSV exporting (supports batching)
* Prevent non admins from managing lead lists inside their profile.

= 2.2.0 =
* Updating Shared Folder. No real changes.

= 2.1.9 =
* UI Improvements

= 2.1.8 = 
* Improved event tracking.
* Improved responsiveness on smaller screens.
* Improved lead statuses.

= 2.1.7 = 
* Attempt to prevent fatals on batch updating. 

= 2.1.2 = 
* Preparing for Inbound Pro
* UI improvements

= 2.0.4.1 = 
* adding batch processing saftey for infinite loops.

= 2.0.4 = 
* lead lists not saving inside form editor (saves once but did not load saved lists on refresh)
* dynamic fields restored & reads from cookie now too if available
* introduction of inbound_events_table and hooking activity tab into new inbound_events_table.

= 2.0.1 = 
* migrating events into inbound_events table
* adding support for custom events, unsubscribe events

= 1.8.1 =
* Restoring geolocation services and offloading their processing to the lead profile open event instead of the new lead creation event.
* Improved impression/conversion reporting on Lead listing page.
* Restoring 'Set Email Response' button in Inbound Form management.
* Restoring Lead 'logs' tab.
* Refactoring admin enqueues into their respective classes.
* Refactoring all assets into /assets/ folder
* Refactoring select components into OOP.

= 1.7.1 =
* Attention Leads users, if you used the email templating features to customize email responses or customize core WordPress email templates please head here to restore your original setup, these features have been backed out of our Leads plugin. https://wordpress.org/plugins/leads-edit-core-email-templates/
* Temporarily disabling geolocation services

= 1.7.0 =
* Security Patch for firefox

= 1.6.8 =
* Security Patch

= 1.6.6 =
* Security Patch

= 1.6.5 =
* Fix for lead conversions not tracking on certain B variations

= 1.6.4 =
* Adding support for Inbound Attachments extension
* Bug fixes and general improvements.

= 1.6.3 =
* Fixing white screen of death issues with other plugin conflicts
* Improvements on NoConflict jQuery Class

= 1.6.2 =
* Debugging release issues with 1.8.1
* security update

= 1.6.1 =
* Fixes issue with using Form Name and First Name when no name field is present in Inbound Now Forms
* Required Fields no longer allow empty spaces as values.
* Restored way to disable field pre-population
* Improvement helps with checkbox required field validation
* For detailed list of changes see [here](https://github.com/inboundnow/leads/issues?q=label%3A1.6.1+is%3Aclosed)

= 1.5.9 =
* Better required field validation for checkboxes

= 1.5.8 =
* Even more security updates! Security for the win!

= 1.5.7 =
* Security Patch

= 1.5.6 =
* Fix double lead notification email

= 1.5.5 =
* Contact form 7 fix

= 1.5.4 =
* Added form field exclusions to ignore sensitive data

= 1.5.2 =
* See Changelog Here: https://github.com/inboundnow/leads/issues?q=is%3Aissue+is%3Aclosed+label%3Av1.5.2

= 1.5.1 =
* Improved form email typo detection
* Improved Template Styles
* Fixed content wysiwyg scroll freezing bug

= 1.5.0 =
* Added Lead API
* Added CSV exporting to Lead Management
* Moved language file out of shared folder
* Bug Fix: Email Systems
* Bug Fix: Lead notification email treats multiple selections correctly now.
* Add default email confirmation subject when empty.

= 1.4.6 =
* Bug Fix: Lead notification emails are fixed.

= 1.4.5 =
* Bug Fix: Renamed constant WPL_URLPATH to WPL_URLPATH to fix shared asset loading.

= 1.4.3 =
* Bug Fix: Remove marketing automation button for non-admin members logged in
* 100% support for the fr_FR

= 1.4.2 =
* Bug Fix: Check all required fields

= 1.4.1 =
* Bug Fix: Inbound form's email response fix for email body.
* Bug Fix: Admin bar marketing button showing for non admin logged in users.
* Added: Inbound forms now have a 'Country' dropdown.
* Added: These inbound form field elements now accept placeholders and/or html in labels: textarea, dropdown, checkbox.
* Added: Wrap Inbound form fields with pre-defined classes.
* Added: Create lead lists from within form creation tool
* Added: Filter form submissions through Akismet
* Improvement: Refactored serveral modules to be written in Class standard.

= 1.3.9 =
* Fix to insert marketing shortcode popup

= 1.3.8 =
* Overwrite core wordpress email template with customizable email templates.
* Lead notification email templates can now be customized.
* Improve analytics

= 1.3.7 =
* Temporary fix for shortcodes disappearing from wordpress 3.8 to 3.9
* Performance improvements on analytics and lead tracking

= 1.3.6 =
* Misc bug fixes

= 1.3.5 =
* fixed field mapping bug
* Added better compability for js conflicts
* Prepping for automation

= 1.3.4 =
* Improved form compatibilty with contact form 7, gravity forms, and ninja forms
* Now tracking blog comments. View the new comments tab in the lead activity profile!
* Now tracking searches made by leads. View the new search tab in the lead activity profile!
* Numerous bug files and code improvements

= 1.3.3 =
* Updated Bulk lead management tool
* Added Google Analytics Custom Event Tracking for form submissions
* Added Ability: automatically sort leads into lists on form completions
* Added Ability: Send lead notification emails to multiple people. Use comma separated values
* Added New Lead Tags option for additional ways to sort and categorize/tag leads
* Updated main docs.inboundnow.com site. Check it out!

= 1.3.2 =
* GPL update with js

= 1.3.1 =
* New Feature: Bulk Lead management
* Added tags to lead profiles for improved management/categorization
* Added new compatibility options to fix third party plugin conflicts!
* Added new debugging javascript debugging tools for users
* Fixed Email Sending Error on forms
* Improved support for master license keys

= 1.3.0 =
* Added New HTML Lead Email Template with clickable links for faster lead management
* Added Button Shortcodes!
* Added HTML field option to form tool
* Added Divider Option to Form tool
* Added multi column support to icon list shortcode
* Added Font Awesome Icons option to Inbound Form Submit buttons
* Added Social Sharing Shortcode

= 1.2.5 =
* Bug fix - missing trackingObj

= 1.2.4 =
* Added feature request form to all plugin admin pages. Submit your feature requests today! =)

= 1.2.3 =
* Bug fixes for form creation issues
* Bug fixes for safari page tracking not firing
* Added quick menu to WP admin bar for quicker marketing!

= 1.2.2 =
* Updated: Styles to WordPress 3.8
* Updated: Shortcode fixes


= 1.2.1 =
* Added: Time on time per page view
* Updated: Conversion paths into session history
* Updated: Form tool.

= 1.2.0 =

* Added: Added email confirmation support to Inbound Forms tool
* Added: New Shortcodes Fancy List and Column shortcodes
* Added: Field Mapping for other langauges for Inbound Form tool

= 1.1.1 =

* Added: Added InboundNow form creation and management system (beta)
* Added: Support for InboundNow cross plugin extensions
* Added: 'header' setting component to global settings.
* Added: bulk add lead to list.
* Improvement: Improved data management for global settings, metaboxes, and extensions.
* fix: fixed issue with CSV bulk lead exporting.

= 1.0.0.5 =
* Fix issue with lead's first conversion not inserting into database correctly.

= 1.0.0.2 =
* Improved list view layout
* Fixed some JS errors

= 1.0.0.1 =

Released