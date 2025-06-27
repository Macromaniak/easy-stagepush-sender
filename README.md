# Easy StagePush Sender

**Easy StagePush Sender** is a WordPress plugin that enables seamless one-way content publishing from a development or staging environment to a live production site. With a single click, you can transfer posts, pages, custom fields (ACF), taxonomy terms, SEO metadata (Yoast), and media using the WordPress REST API.

---

## ⚙️ How It Works

When editing a post, page, or custom post type, a **“Push to Live”** button appears in the sidebar. Clicking this button instantly sends the post and all associated data (including scheduled status and date, if applicable) to your production site—where it is received by the companion plugin: [Easy StagePush Receiver](https://github.com/Macromaniak/easy-stagepush-receiver).

No checkboxes, no auto-publishing: **You’re in control—push only when you want!**

---

## ✨ Features

- ✅ One-click content push from staging/dev to production
- ✅ Supports ACF (Flexible Content, Groups, Repeaters, Relationships)
- ✅ Transfers featured images, media files, SVGs, and attachments (no duplicates)
- ✅ Syncs taxonomy terms, including custom taxonomies
- ✅ Includes Yoast SEO meta (title, description, OG, Twitter)
- ✅ Handles page templates and parent/child structure
- ✅ Preserves post status (draft, scheduled, published) and scheduled date
- ✅ Configurable post types and easy settings
- ✅ Developer-friendly with clean REST architecture

---

## 🧩 Requirements

- WordPress 6.3+
- ACF Pro (if using ACF fields)
- [Easy StagePush Receiver](https://github.com/Macromaniak/easy-stagepush-receiver) must be installed on the production site

---

## 🚀 Installation & Usage

1. **Install and activate** this plugin on your **development/staging** WordPress site.
2. **Install and activate** [Easy StagePush Receiver](https://github.com/Macromaniak/easy-stagepush-receiver) on your **production** WordPress site.
3. Go to `Settings → StagePush` and configure:
   - Your production site URL
   - (Optional) Your dev/staging site URL for URL rewriting
   - Select which post types can be pushed
4. Edit a post, page, or CPT, and click **Push to Live** in the sidebar meta box.
5. The content—including ACF data, media, taxonomy, SEO, and post status—will be sent to your live site.

**Note:**  
Your production site must have the same post types, taxonomies, and ACF field groups registered. This plugin does not register or sync field definitions—only content and metadata.

---

## 📌 Important Notes

> This plugin assumes your production site is a **structural mirror** of your dev site.  
> The plugin does not create post types, taxonomies, or ACF field groups—only syncs content.

---

## 🧪 Development

Contributions are welcome! Fork, submit issues, or send pull requests.

- PHP
- WordPress REST API
- ACF Integration
- Works with Classic and Block Editor

---

## 📫 Support

Have a question or need help?

- Email: anandhu.natesh@gmail.com / anandhu.nadesh@gmail.com
- GitHub Issues: [Create an issue](https://github.com/Macromaniak/easy-stagepush-sender/issues)

---

## 📄 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

Enjoy seamless deployment from stage to prod! 🎯

