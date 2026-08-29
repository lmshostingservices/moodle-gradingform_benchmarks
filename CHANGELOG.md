## [v1.2.39] - 2026-08-29

### Changed
- RELEASE RECOVERY: Republished the reviewed authoritative source under a new immutable tag because the historical tag contained a different source tree. No functional changes.

# Changelog - Assignment Benchmarks

All notable changes to this plugin will be documented in this file.

## [1.2.31] - 2026-04-23

### Fixed
- **AMD DEFINE FIX**: Converted `amd/build/grades/grader/gradingpanel.js` and `gradingpanel.min.js` from ES module syntax (`import`/`export` statements) to RequireJS `define()` format. ES module `import` statements in any AMD build file cause RequireJS to throw a SyntaxError when loading `core/first`, generating "No define call for core/first" and silently aborting the entire AMD module chain — hiding Moodle's primary and secondary navigation menus site-wide. No PHP, DB schema, or functional changes. version.php → 2026042300031.

## [1.2.30] - 2026-04-22

### Fixed
- **AMD ENCODING FIX**: Scrubbed all non-ASCII characters from AMD JS files. No PHP/DB changes. version.php → 2026042200030.

## [1.2.16] - 2026-03-20

### Fixed
- BUG-DB-NAME-LENGTH: All three DB tables renamed so that `mdl_` + table name is under 28 characters total. `gradingbench_grp` (20), `gradingbench_items` (22), `gradingbench_fills` (22). Upgrade step handles all prior install states (original, v1.2.15, v1.2.16a).

## [1.2.8] - 2026-01-07

### Changed
- **New trash icon for delete buttons** - Replaced crude "XX" text with proper SVG trash icons
- **Improved delete button styling** - Added border, hover states with red highlight, hidden text overflow

## [1.2.7] - 2025-12-24

### Fixed
- **Removed duplicate pts/points text** - Score display no longer shows redundant "pts" and "points" labels
- **Added more padding between criteria** - Improved visual spacing for better readability

## [1.2.6] - 2025-12-24

### Changed
- **Score display redesign** - New bordered box design for point values
- **Points label styling** - Cleaner typography with primary color theming
- **Primary color integration** - Score elements now respect site's primary color

## [1.2.5] - 2025-12-23

### Fixed
- **Fixed all file path references** - Changed remaining `/checklist/` paths to `/benchmarks/` throughout the codebase

## [1.2.4] - 2025-12-23

### Fixed
- **Fixed install.xml PATH attribute** - Changed from `grade/grading/form/checklist/db` to `grade/grading/form/benchmarks/db` to match actual install directory

## [1.2.3] - 2025-12-23

### Fixed
- **Renamed constant `CHECKLIST` to `BENCHMARKS`** - Prevents conflict with old gradingform_checklist plugin. You can now have both plugins installed side-by-side without constant redefinition errors.

## [1.2.2] - 2025-12-23

### Fixed
- **Removed `.kow-plugin.php`** - Leftover moodle.org metadata file that could cause "headers already sent" errors on some server configurations

### Verified
- All PHP files confirmed BOM-free (no UTF-8 byte order marks)
- No closing `?>` tags in any PHP files (Moodle best practice)
- Clean ZIP packaging with correct folder structure

## [1.2.1] - 2025-12-23

### Added
- **Explicit capabilities** in `db/access.php`:
  - `gradingform/benchmarks:editdefinition` - Create/edit benchmark forms
  - `gradingform/benchmarks:grade` - Grade using benchmarks  
  - `gradingform/benchmarks:viewresults` - View grading results
- **Enhanced language strings** with clearer terminology:
  - "Checked" → "Achieved"
  - "Unchecked" → "Not achieved"
  - "Add group" → "Add benchmark group"
  - Plugin help text explaining what benchmarks means
- **Capability language strings** for admin UI
- **Moodle 5.x support declaration** via `$plugin->supported = [400, 500]`
- **Comprehensive README.md** with:
  - Clear explanation of what "benchmarks" means in this context
  - Intended use cases and workflow
  - Differences from rubrics and marking guides
  - Known limitations
  - Upgrade notes
  - Capability documentation
- **CHANGELOG.md** for version tracking and upgrade notes

### Changed
- Maturity confirmed as MATURITY_STABLE (production-ready)

## [1.2.0] - 2025-12-23

### Changed
- **RENAMED**: Plugin component from `gradingform_checklist` to `gradingform_benchmarks`
- **NEW INSTALL PATH**: `/grade/grading/form/benchmarks/` (was `/grade/grading/form/checklist/`)
- All PHP class names, backup classes, and language files updated
- 17 language files renamed and updated

### Added
- **Explicit capabilities** in `db/access.php`:
  - `gradingform/benchmarks:editdefinition` - Create/edit benchmark forms
  - `gradingform/benchmarks:grade` - Grade using benchmarks  
  - `gradingform/benchmarks:viewresults` - View grading results
- **Enhanced language strings** with clearer terminology (achieved/not achieved)
- **Plugin help text** explaining benchmark concept
- **Comprehensive README** with use cases, workflow, limitations

### Upgrade Notes
- If upgrading from `gradingform_checklist`: Uninstall old plugin first
- Install new plugin to `/grade/grading/form/benchmarks/`
- Grading data structure unchanged - no data migration needed

## [1.1.0] - 2025-12-22

### Added
- **TGA Import** from training.gov.au
  - Fetch Elements & Performance Criteria
  - Fetch Performance Evidence
  - Fetch Knowledge Evidence
  - Auto-numbering (1, 1.1, 1.2...)
- Web service endpoint for TGA unit lookup

## [1.0.0] - 2025-12-20

### Added
- Initial release based on Open LMS Checklist
- **Bulk Paste Builder** - Create checklists from pasted text
- Support for numbered outlines, CSV, and Markdown formats
- Premium SaaS styling with Inter font
- Mobile-first responsive design (768px/480px breakpoints)
- Full-width layouts for better content visibility
- CSS variables for easy theming
- Moodle 4.0 - 5.x compatibility
- PHP 8.0 - 8.4 support
- Full backup/restore support
- 17 language translations
