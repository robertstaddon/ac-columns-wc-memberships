# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1] - 2024-12-19

### Added
- Dynamic column creation for WooCommerce Memberships Profile Fields
- Automatic discovery of profile fields by querying user meta keys
- Support for displaying profile field values from post author's user meta
- Full Admin Columns Pro integration with editing, export, search, and sorting capabilities
- Columns automatically registered for `wc_user_membership` post type

### Changed
- Transformed single-column template into dynamic multi-column system
- Updated all column classes to work with user meta instead of post meta
- Modified search and sorting queries to join `wp_usermeta` via `wp_posts.post_author`

## [1.0] - Initial Release

### Added
- Initial column template for Admin Columns Pro

