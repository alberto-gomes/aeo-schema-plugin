# 🚀 Advanced AEO & SEO Schema Generator

A powerful, lightweight WordPress plugin designed to automate and scale advanced structured data (JSON-LD) implementation for modern Answer Engine Optimization (AEO) and SEO. Built for marketing agencies and SEO professionals, this tool transitions manual schema processes into a user-friendly, repeatable framework directly inside WordPress.

---

## ✨ Key Features

*   **Google Search Gallery Compliant:** Generates strict JSON-LD markup that perfectly maps to Google's required and recommended properties.
*   **Intuitive UI via ACF:** Conditionally displays fields based on the selected schema type. Distinctly separates **Required** fields from **Optional** ones, making it foolproof for account managers and content creators.
*   **Author-Level E-E-A-T Propagation:** Map `ProfilePage` schema to a WordPress User Profile once, and the plugin will automatically inject it across every blog post authored by that user.
*   **Intelligent Fallback Logic:** 
    1. Checks for Page/Post specific overrides.
    2. Falls back to Author-level schema (if applicable).
    3. Defaults to Global Site-Wide schema settings.
*   **No Graph Conflicts:** Hooks smoothly into `wp_head` at priority `20`, ensuring it plays nicely with existing SEO plugins (like Yoast or RankMath) without causing duplicate code errors.

---

## 📋 Supported Schema Markups

The plugin currently features built-in, ready-to-use templates for high-impact, advanced schemas:

*   👤 **ProfilePage:** Explicitly ties an author or executive to their credentials and social profiles, cementing them as recognized entities in the Knowledge Graph.
*   📊 **Dataset:** Essential for B2B marketers publishing original research or surveys. Makes data discoverable in Google Dataset Search.
*   🎬 **VideoObject:** Defines key video metrics and timestamps, allowing AI to jump users directly to specific answers within a video.
*   💻 **SoftwareApplication:** Feeds search engines operating system requirements, pricing, and software categories right on the SERP for SaaS products.

---

## 🛠 Requirements

*   **WordPress:** v5.0 or higher.
*   **Advanced Custom Fields (ACF):** Required. Works with both the Free and Pro versions.

---

## 🚀 Installation

1. Ensure the **Advanced Custom Fields (ACF)** plugin is installed and activated on your WordPress site.
2. Download or copy the plugin code.
3. In your WordPress installation, navigate to `wp-content/plugins/`.
4. Create a new directory named `advanced-aeo-schema-generator`.
5. Create a file inside that directory named `advanced-aeo-schema-generator.php` and paste the provided code into it.
6. Go to the **Plugins** menu in your WordPress admin dashboard.
7. Locate **Advanced AEO & SEO Schema Generator** and click **Activate**.

> **Note:** If ACF is not active, the plugin will display an admin notice and safely deactivate itself to prevent site errors.

---

## 📖 How to Use

The plugin automatically registers all necessary fields and UI elements. No manual ACF configuration is required.

### 1. Page or Post Specific Schema
*   Navigate to any Page or Post edit screen.
*   Scroll down to the **Advanced Structured Data** meta box.
*   Select your desired schema from the dropdown and fill in the required fields.

### 2. Author-Level Schema (E-E-A-T)
*   Navigate to **Users > Profile** (or edit any specific user).
*   Scroll to the bottom to find the schema configuration.
*   Select **Profile Page** and enter the author's credentials. This will now automatically apply to all posts written by this author unless overridden at the post level.

### 3. Global Site-Wide Schema
*   Look for the **Global Schema** tab in your left-hand WordPress admin menu.
*   Set up a schema here to act as the ultimate fallback for pages that do not have specific page-level or author-level schema defined.

---

## 📄 License

This project is licensed under the **GPL2** License.
