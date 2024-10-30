=== LH Response Handler ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-response-handler/
Tags: redirect, 410, status code, 404, multisite
Requires at least: 4.0
Tested up to: 6.0
Stable tag: trunk

Intercepts wordpress 404s and allows you to handle the response with a redirect or much more!

== Description ==

LH Responses Handler is a flexible wordpress waqy of handling urls in your single site or multiste that are currently handled by a 404. You can choose to redirect these responses or indeed handle them with a range of status codes (with more to be added later).


**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-response-handler/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-response-handler/).**

== Installation ==

1. Upload the `lh-response-handler` folder to the `/wp-content/plugins/` directory
2. Activate or network activate the plugin through the 'Plugins' menu in WordPress
3. If the plugin is activated normally a menu item Respones will appear on your tools menu of your site, if it is network activated this menu item will only appear on the main site. 


== Frequently Asked Questions ==

= Why is this plugin better than the alternatives? =

Unlike the alterantives this plugin does not add any tables to your database. It also handles multiple tatus codes (not all plugins have this feature) and can work at the network level if you are using multisite.

= Does the plugin require a multisite installation? =

No it works on both a single install and multisite

= Does this plugin behave differently when networked activated? =

Yes when network activated the you can only set redirects on the main site of the multisite, those redirects you set however are applied to all sites within the multisite. 

= What is something does not work?  =

LH Response Handler, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfirteen, twentysixteen etc.

If the problem persists pleasse leave a post in the support forum: [https://wordpress.org/support/plugin/lh-response-handler/](https://wordpress.org/support/plugin/lh-response-handler/) . I look there regularly and resolve most queries.

= Are there any other requirements?  =

This plugin relies on the [https://developer.wordpress.org/reference/hooks/wp_body_open/](wp_body_open) hook. 98% of themes include it and it is now a standard. If yours does not, create a child theme, or get a better theme.

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)


== Changelog ==

**1.00 May 04, 2019** 
* Initial release

**1.00 October 10, 2020** 
* Added 410 gone support