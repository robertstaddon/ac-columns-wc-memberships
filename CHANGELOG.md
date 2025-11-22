# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2] - 2024-12-19

### Fixed
- Fixed profile field slug extraction from column type when columns are loaded from saved configurations
- Fixed inline editing not saving values by ensuring profile field slug is correctly passed to supporting classes
- Fixed meta key construction to include profile field slug in all cases

### Changed
- Updated all supporting class instantiation methods (Editing, Export, Search, Sorting) to use `get_profile_field_slug()` method
- Improved user ID retrieval consistency across all classes using `get_post_field()`
- Columns now properly categorized under "woocommerce" group instead of "custom"

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

