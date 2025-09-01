# Agentic Social Sharing

A WordPress plugin to help publishers post their content to social platforms in a semi-automated manner satisfying algorithm requirements. Posting content doesn't happen via API, and links are added as a comment to the post. Every manual interaction is designed in a way that makes its handling automatically by agentic browsers possible.

## 🎯 Overview

Agentic Social Sharing bridges the gap between full automation and manual social media posting. It's specifically designed to work within the constraints of modern social media algorithms that favor authentic, manual-style posting over API-based automation.

### Key Features

- **🤖 AI Agent Ready**: Designed for compatibility with AI agentic browsers like Perplexity's Comet
- **📱 Algorithm-Friendly**: Posts links as comments instead of in the main post body for better engagement
- **🔄 Semi-Automated Workflow**: Balance automation with manual control
- **📊 Share History Tracking**: Monitor all your social media shares from one dashboard
- **🎨 Per-Post Customization**: Customize messages for each individual post
- **⏰ Smart Delays**: Configurable delays to avoid appearing spammy
- **📝 Multiple Post Types**: Support for posts, pages, and custom post types

## 🚀 Installation

### From WordPress Admin

1. Download the plugin as a ZIP file
2. Navigate to **Plugins → Add New** in your WordPress admin
3. Click **Upload Plugin** and select the ZIP file
4. Click **Install Now** and then **Activate**

### Manual Installation

```bash
# Navigate to your WordPress plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Clone this repository
git clone https://github.com/yourusername/agentic-social.git

# Activate the plugin through WordPress admin
```

### Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## ⚙️ Configuration

1. After activation, navigate to **Agentic Social** in your WordPress admin menu
2. Configure your general settings:
   - Enable/disable LinkedIn sharing
   - Set up auto-sharing preferences
   - Configure sharing delays
   - Select post types to share
3. Configure platform-specific settings under **Agentic Social → LinkedIn**

## 📖 Usage

### Manual Sharing

1. Edit any post or page
2. Look for the **Agentic Social Sharing** meta box in the sidebar
3. Check "Share this post on social media"
4. Optionally add a custom message
5. Click **Share Now** or let it share automatically based on your settings

### AI Agent Mode

Enable AI Agent Mode in settings to optimize the interface for automated browsers:

1. Go to **Agentic Social → Settings**
2. Enable **AI Agent Mode**
3. The plugin will display AI-friendly indicators and structured data

### Link as Comment Strategy

The plugin implements a best-practice approach for social media algorithms:

- Main post contains engaging content without links
- Links are automatically added as the first comment
- This approach typically results in 2-3x better reach

## 🏗️ Architecture

```text
agentic-social/
├── admin/                  # Admin-specific functionality
│   ├── css/               # Admin styles
│   ├── js/                # Admin JavaScript
│   └── partials/          # Admin view templates
├── includes/              # Core plugin classes
│   ├── class-agentic-social.php           # Main plugin class
│   ├── class-agentic-social-loader.php    # Hook loader
│   ├── class-agentic-social-activator.php # Activation logic
│   └── class-agentic-social-deactivator.php # Deactivation logic
├── languages/             # Translation files
├── public/                # Public-facing functionality
│   ├── css/              # Public styles
│   └── js/               # Public JavaScript
├── agentic-social.php    # Main plugin file
├── uninstall.php         # Cleanup on uninstall
└── readme.txt            # WordPress.org readme
```

## 🔧 Development

### Setup Development Environment

```bash
# Clone the repository
git clone https://github.com/yourusername/agentic-social.git
cd agentic-social

# Install development dependencies (if using build tools)
npm install

# Start development
npm run dev
```

### Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/):

- PHP: WordPress PHP Coding Standards
- JavaScript: WordPress JavaScript Coding Standards
- CSS: WordPress CSS Coding Standards

### Hooks and Filters

The plugin provides several hooks for developers:

```php
// Modify share data before posting
add_filter('agentic_social_share_data', 'your_function', 10, 2);

// After successful share
add_action('agentic_social_after_share', 'your_function', 10, 3);

// Customize AI agent behavior
add_filter('agentic_social_ai_agent_config', 'your_function');
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.0.2

- Replaced sidebar meta box with full-screen post-publish overlay
- Added iframe support for LinkedIn integration
- Enhanced AI agent automation with progress tracking
- Improved user experience with keyboard shortcuts
- Added tab-based LinkedIn loading options

### Version 1.0.1

- Initial release
- LinkedIn integration foundation
- AI Agent Mode
- Share history tracking
- Post-level customization
- Smart delay system

## 🔮 Roadmap

- [ ] Twitter/X integration
- [ ] Instagram support (via browser automation)
- [ ] Bulk scheduling
- [ ] Analytics dashboard
- [ ] Content optimization suggestions
- [ ] Multiple account support
- [ ] Webhook notifications
- [ ] REST API endpoints

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

Paolo Belcastro**

- Website: [paolobelcastro.com](https://paolobelcastro.com)
- GitHub: [@p3ob7o](https://github.com/p3ob7o)

## 💬 Support

For support, feature requests, or bug reports:

1. Check the [FAQ](#-frequently-asked-questions) section below
2. Search existing [GitHub Issues](https://github.com/p3ob7o/agentic-social/issues)
3. Create a new issue with detailed information
4. Visit [paolobelcastro.com/agentic-social](https://paolobelcastro.com/agentic-social)

## ❓ Frequently Asked Questions

### Why not use official social media APIs?

Modern social media algorithms favor authentic, manual-style posting. This plugin provides a semi-automated approach that maintains the appearance and engagement benefits of manual posting while still offering automation capabilities through AI agents.

### How does AI Agent Mode work?

AI Agent Mode optimizes the plugin's interface for interaction with AI agentic browsers. It adds structured data, clear action indicators, and predictable UI patterns that make it easy for AI agents to navigate and interact with the plugin.

### Is this plugin compliant with social media terms of service?

Yes. The plugin doesn't violate terms of service because it doesn't use unauthorized APIs or perform actions that users couldn't do manually. When used with AI agents, it simulates manual user behavior.

### Can I use this with multiple social media accounts?

Currently, the plugin supports one account per platform. Multiple account support is on our roadmap.

## 🙏 Acknowledgments

- WordPress Plugin Boilerplate for the initial structure
- The WordPress community for continuous support
- Contributors and testers who help improve this plugin

---

**Note**: This plugin is under active development. Features and documentation may change. Always test in a staging environment before deploying to production.
