# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1] - 2026-01-30

### Fixed
- Sorting fatal error: pass string to `SqlOrderByFactory::create()` (order name with `'ASC'` fallback) for Admin Columns Pro 7 compatibility
- Export fatal error: use `AC\Formatter` (ExportFormatter) in `FormatterCollection` for export column values instead of Export service
- Column registration: register class name strings and dynamic subclasses; Column resolves `AdvancedColumnFactory` dependencies (FeatureSettingBuilderFactory, DefaultSettingsBuilder) from AC DI container
- ValueFormatter: `format()` signature and Value API (`get_id()`, `with_value()`) for `AC\Formatter` compatibility

## [2.0] - 2026-01-30

### Added
- Support for Admin Columns Pro 7
- `ColumnFactory` for dynamic column registration (one factory per profile field)
- `ValueFormatter` for list table display via `AC\FormatterCollection`

### Changed
- **Breaking:** Requires Admin Columns Pro 7.0+ (v6 no longer supported)
- **Breaking:** Requires PHP 7.4+
- Column registration: `add_action('acp/column_types')` replaced by `add_filter('ac/column/types/pro')`
- Column base class: `AC\Column` replaced by `ACP\Column\AdvancedColumnFactory`
- Display: `get_value($id)` replaced by `get_formatters(Config)` returning `AC\FormatterCollection`
- Features: `editing()` / `sorting()` / `export()` / `search()` replaced by `get_editing()` / `get_sorting()` / `get_export()` / `get_search()` with `AC\Setting\Config`
- List screen check uses `AC\TableScreen` and `get_key()` / `get_id()` for `wc_user_membership`

### Removed
- Version check for Admin Columns 6.3
- Direct `register_column_type(new Column(...))` in favor of factory instances

## [1.3] - 2024-12-19

### Added
- Dropdown filter with all available values for each profile field
- Automatic discovery and population of filter dropdown options from existing user meta values
- Value caching for improved filter performance

### Changed
- Search/filter functionality now uses dropdown select instead of plain text input
- Filter now shows only exact match (EQ) operator for better dropdown compatibility
- Improved handling of serialized array values in filter dropdown

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

