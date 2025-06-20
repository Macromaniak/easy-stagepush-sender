=== Easy StagePush Sender ===
Contributors: anandhunadesh, phaseswpdev
Tags: staging, publishing, content-sync, acf, media, dev-to-live
Requires at least: 3.0.1
Tested up to: 6.8.1
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

A simple plugin to manage automatic publishing of posts, pages, and custom content from a staging/dev WordPress site to a production site.

=== Description ===

Easy StagePush Sender is a powerful WordPress plugin that allows you to push content — including ACF fields, media files, featured images, taxonomy terms, and SEO metadata — directly from your development or staging site to your live site with a single click.

The plugin adds a meta checkbox to selected post types. When checked, and the post is published or updated, the post is automatically pushed to the production environment via REST API.

This plugin is intended to be used in tandem with the [Easy StagePush Receiver](https://wordpress.org/plugins/easy-stagepush-receiver/) plugin, which must be installed on the live site to receive the pushed content.

**Important:** This plugin assumes your production site is a structural mirror of your development site. That means all custom post types, taxonomies, and ACF field groups used in the dev site must also exist and be registered on the production site. This plugin does not create post types, taxonomies, or field groups — it only pushes content and metadata.

=== Features ===

* Push posts, pages, and custom post types (CPTs) from dev to production
* Supports ACF Flexible Content Fields, Relationships, Repeaters, Groups
* Includes featured images and other media without duplication
* Syncs taxonomy terms (including custom taxonomies)
* Includes Yoast SEO meta fields
* Respects page templates and parent/child hierarchies
* Supports file and SVG field types
* Customizable settings panel to control sync options and endpoints

=== Usage ===

1. Install and activate **Easy StagePush Sender** on your development/staging WordPress site.
2. Install and activate **Easy StagePush Receiver** on your live/production WordPress site.
3. Configure sender settings in Settings → StagePush.
   - Enter the production site URL.
   - Select which post types should support syncing.
   - Enable or disable the meta checkbox visibility.
4. Edit a post or page of a supported type, check "Publish to Production", and click "Update" or "Publish".
5. The content, ACF data, media, and metadata will be automatically pushed to your live site.

**Note:** Ensure that all ACF field groups, post types, and taxonomies used on the staging site are also present on the production site for proper content mapping and display.

=== Frequently Asked Questions ===

= Does this plugin sync content both ways? =

No. Syncing is one-way — from the dev/staging site to the live/production site.

= Is the Receiver plugin required? =

Yes. Easy StagePush Receiver must be installed and active on the destination site to receive and import content.

= What about security and authentication? =

The current version uses an open REST endpoint for convenience. You may restrict access using authentication headers or IP whitelisting in future updates.

= Will it create duplicate media files? =

No. The plugin checks for existing media by filename and avoids re-uploading files.

=== Support ===

If you have any questions, issues, or feature requests, please feel free to reach out at anandhu.natesh@gmail.com / anandhu.nadesh@gmail.com  
If you want to contribute, please create an issue or submit a pull request on [Github](https://github.com/Macromaniak/easy-stagepush-sender).

=== License ===

This plugin is licensed under the GPLv2 or later. You are free to use, modify, and distribute this plugin under the terms of the license.
