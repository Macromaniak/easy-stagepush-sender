# Easy StagePush Sender

**Easy StagePush Sender** is a WordPress plugin that enables seamless one-way content publishing from a development or staging environment to a live production site. It transfers posts, pages, custom fields (ACF), taxonomy terms, SEO metadata (Yoast), and media using the WordPress REST API.

---

## ⚙️ How It Works

When a post of a selected type is published or updated and marked with a checkbox, the plugin sends the post and all associated data to the production site where it is received and saved using the companion plugin — [Easy StagePush Receiver](https://github.com/your-org/easy-stagepush-receiver).

---

## ✨ Features

- ✅ Push content from staging/dev to production
- ✅ Supports ACF (Flexible Content, Groups, Repeaters, Relationships)
- ✅ Featured images, media files, SVGs, and attachments
- ✅ Syncs taxonomy terms, including custom taxonomies
- ✅ Includes Yoast SEO meta (title, description, OG, Twitter)
- ✅ Handles page templates and parent/child structure
- ✅ Configurable post types and visibility options
- ✅ Developer-friendly with clean REST architecture

---

## 🧩 Requirements

- WordPress 5.0+
- ACF Pro (if using ACF fields)
- [Easy StagePush Receiver](https://github.com/your-org/easy-stagepush-receiver) must be installed on the production site

---

## 🚀 Installation

1. Install and activate this plugin on your **development/staging** WordPress site.
2. Install and activate **Easy StagePush Receiver** on your **production** WordPress site.
3. Go to `Settings → StagePush` and configure:
   - Your production site URL
   - Post types to support syncing
   - Whether to show the sync checkbox
4. Edit a post and check `Publish to Production`, then update or publish the post.

---

## 📌 Important Notes

> This plugin assumes your production site is a **structural mirror** of your dev site.  
> You must have the **same post types, taxonomies, and ACF field groups** registered on both sites.  
> The plugin does not create or register them — it only syncs the content.

---

## 🧪 Development

Feel free to fork this plugin, submit issues, or contribute improvements.

- PHP
- WordPress REST API
- ACF Integration
- Compatible with classic and block editor

---

## 📫 Support

Have a question or need help?

- Email: anandhu.natesh@gmail.com / anandhu.nadesh@gmail.com
- GitHub Issues: [Create an issue](https://github.com/Macromaniak/form-submission-manager/issues)

---

## 📄 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

Enjoy seamless deployment from stage to prod! 🎯
