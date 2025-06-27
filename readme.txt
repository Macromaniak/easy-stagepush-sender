=== Easy StagePush Sender ===
Contributors: anandhunadesh, phaseswpdev
Tags: content-sync, acf, media, dev-to-live, migration
Requires at least: 6.3
Tested up to: 6.8
Stable tag: 1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Push posts, pages, custom content, ACF fields, media, taxonomies & SEO from staging to production with one click.

=== Description ===

**Easy StagePush Sender** lets you safely and easily migrate content — including ACF fields, media files, featured images, taxonomy terms, and SEO metadata — directly from your staging/dev site to your production site. 

A “Push to Live” button appears in the editor sidebar for all supported post types. When you click the button, the plugin instantly transfers the post, including all custom fields and media references, to your live site using a secure REST API endpoint.

This plugin is designed to work with the [Easy StagePush Receiver](https://wordpress.org/plugins/easy-stagepush-receiver/) plugin, which must be installed on your production site to receive the content.

**Important:** Your production site should have the same post types, taxonomies, and ACF field groups as your staging/dev site. This plugin does not register or sync post type or field definitions—it only pushes content and metadata.

=== Features ===

* Manual “Push to Live” button for posts, pages, and custom post types (CPTs)
* One-click transfer of all post content, including ACF Flexible Content, Relationships, Repeaters, and Groups
* Seamless handling of featured images and other media (no duplication)
* Taxonomy and term synchronization (including custom taxonomies)
* Yoast SEO metadata transfer
* Respects page templates and parent/child hierarchies
* Supports scheduled posts, files, SVGs, and more
* Customizable settings panel for site URL and allowed post types

=== Usage ===

1. **Install and activate** Easy StagePush Sender on your development/staging WordPress site.
2. **Install and activate** Easy StagePush Receiver on your live/production WordPress site.
3. Go to **Settings → StagePush** on your dev/staging site:
   - Enter the production site URL.
   - (Optional) Enter the dev/staging site URL for automatic URL replacement.
   - Select the post types you want to enable for pushing.
4. Edit a post, page, or custom post type.
5. Click the **Push to Live** button in the editor’s sidebar meta box.
6. Your post’s content, ACF data, media references, taxonomy, and SEO metadata will be transferred to your live site instantly, preserving status (draft, scheduled, published, etc.).

**Note:** Make sure all ACF field groups, post types, and taxonomies exist on both sites for proper mapping.

=== Frequently Asked Questions ===

= Does this plugin sync content both ways? =

No. Content is pushed one way only: from your staging/dev site to your production site.

= Is the Receiver plugin required? =

Yes. Easy StagePush Receiver must be active on your production site to accept incoming posts.

= Does the “Push to Live” button work for scheduled posts and drafts? =

Yes! The current post status (draft, published, scheduled, etc.) and post date are included in the push, so scheduled posts will remain scheduled on production.

= Is authentication required? =

Currently, the REST endpoint is open for convenience. You can restrict access using authentication headers, IP whitelisting, or additional security measures as needed.

= Will this create duplicate media? =

No. The plugin references media files by URL, and the receiver checks for existing media by filename to avoid duplication.

=== Support ===

For support, questions, or feature requests, contact anandhu.natesh@gmail.com / anandhu.nadesh@gmail.com  
Contributions are welcome on [GitHub](https://github.com/Macromaniak/easy-stagepush-sender).

=== License ===

This plugin is licensed under GPLv2 or later. You are free to use, modify, and distribute this plugin under the terms of the license.
