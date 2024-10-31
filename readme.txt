=== Recruitment Manager - Jobs Listing and Recruitment Plugin ===
Contributors: codewand
Tags: recruitment, jobs, career, recruitment plugin, career plugin, jobs plugin
Requires at least: 4.7
Tested up to: 5.9
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://patreon.com/codewand

WP Recruitment Manager - Jobs plugin to create ease in your recruitment process

== Description ==

**Recruitment Manager is an advance recruitment and applicant tracking plugin for WordPress designed specially to create your site’s job or career pages. It’s unique features allows you to have a convenience in the recruitment process thus selecting the perfect applicant for your job needs. It’s provided with an easy to use admin panel with all integrated settings you might need for the hiring process.**

Several different shortcodes gives you the flexibility to create job pages as per your needs. Customizing the layout is pretty easy with the built in css editor directly in the admin interface without having to touch code. Also, you can customize email notifications as per your needs.

**[View Demo](https://cwrm.code-wand.com/)**

**[Visit Website](http://code-wand.com/)**

= Key Features =

* Simple to setup just like any other wordpress plugin.
* Jobs management via admin.
* Applications management via admin and filter via jobs.
* Unlimited Filters for jobs (department, location, type etc).
* Category specific short codes for job list.
* Category specific short codes for only job titles.
* Sidebar filters with search.
* Email applicants directly from the applications screen.
* Customize email notifications.
* Translation ready (POT file included) for all phrases.
* Roles & Permissions enabled.
* Google captcha enabled.
* Built in export feature.
* Automatic job expiry.
* Enable / Disable job applications feature.
* CSS Overrides setting for controlling layout.

= More from the Smart Version (Pro) =

**Resume Builder & Applicants Database**

Most of the times, just having the job apply form is not enough and you need more information from applicants which sometimes you don't even know yourself. So take advantage of resume builder which is researched as per the industry standards and collect all the necessary information which you might need to have in applicants. Not just that, once an applicant enter this information you can search it for the later job needs you might need. The resume builder covers the form fields under the categories of General information, Job Experiences, Academic Qualifications, Career & Life Achievements, Languages and References of an applicant.

**Interviews Creation, Assignment & Conducts with notes for Applicant Tracking**

Since interviews are a must and integral part of any recruitment process. This smart and unique feature enables you to first create interview questionaires, assign to applications and then do the marking against each application. By doing this, the marks and quality of the applicants can be easily tracked with the data being converted from qualitative to quantitative. Along with that you can have noted against each application with which you can search and filter applications.

**Auth (Login & Registration)**

The pro version comes with front end login and registration features for applicants so that they can have a track of their job applications and maintain their resume directly from the front end. The auth features does not expose wordpress admin login system and works directly from the fron end.

**More features coming soon**

**[Smart Version (Pro)](https://codecanyon.net/item/wp-smart-recruit-jobs-plugin-for-wordpress/31448233)**

== Installation ==
1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the `Plugins` screen in WordPress

== Screenshots ==
1. Jobs list - With Filters On Sidebar
2. Jobs list - With Filters On Top
3. Job Detail with captcha enabled apply form
4. Admin jobs list
5. Admin applications list
6. Admin Filters
7. Admin Settings

== Frequently Asked Questions ==
= How to install this plugin? =
You can simply download the plugin via the wordpress plugin site or you can go to your wordpress admin -> Plugins -> Add New Plugin and then search for the plugin "WP Recruit Manager".
= How to add jobs? =
WP Recruit manager is gutenberg enabled and just like any other post on your wordpress site you can create job posts. Go to "Recruitment Manager -> New Job". Give a title to your job post, add description, select filter values and then hit update.
= How to create filters? =
This is one unique feature of WP Recruit Manager. Go to "Recruitment Manager -> Job Fields -> Add Field". Just enter any filter of your choice like 'Department', 'Location', 'Salary' etc. Then select the display settings for both front and admin interface and click save.
= How to assign values filters? =
Once filters of your choice are added. They'll appear in job create/edit page on the right settings bar under the jobs tab. You can either select from existing values or create new one under each filter. Also, from the jobs tab, you can add starting and ending salary with job expiry.
= How to display filter in a sidebar? =
This unique feature of 'WP Recruit Manager' allows you to display filters in a sidebar. In any sidebar of your theme, add text widget and place this shortcode "[cwrm-job-filters]". Make sure you use this shortcode on a page where you have also used the job list shortcode.
= How to display jobs from any particular category e.g. "Marketing"? =
Go to "Recruitment Manager -> Job Fields" and copy any shortcode like "[cwrm-job-list Department="Marketing"]" and insert it to a page. This way, you'll only see jobs under the filter 'Department' with values 'Marketing'.
= How to see job applications? =
Go to "Recruitment Manager -> Job Applications" to see all the applicants against each job. You can export list of applications from this screen as well as filter applications against each job.
= How to export job applications? =
On the "Job Applications" screen, go to the actions menu and select 'Export Applications'. Then press the apply button. This action will download csv on your machine.
= How to enable google captcha? =
All the necessary code to enable google recaptcha is integrated into the plugin. To enable google recaptcha go to "https://google.com/recaptcha", add your site domain name and get Site Key and Secret key. Then go to "Recruitment Manager -> Settings -> General Options" and add the credentials there.


== Upgrade Notice ==

= 1.0 =
Complete plugin with all features.