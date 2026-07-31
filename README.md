<p align="center">
  <a href="https://lmshostingservices.com">
    <img src="https://raw.githubusercontent.com/lmshostingservices/lms-labs/main/attached_assets/lms-hosting-logo.png" alt="LMS Hosting Services" height="60">
  </a>
</p>

> **LMS Labs** is the Moodle plugin division of [LMS Hosting Services](https://lmshostingservices.com) — Australia's Moodle™ Certified Partner.

---

# Assignment Benchmarks (gradingform_benchmarks)

**Version:** 1.2.0  
**Requires:** Moodle 4.0+ (including Moodle 5.x)  
**PHP:** 8.0+ (including PHP 8.4)  
**License:** GPL v3+

## What Is This Plugin?

Assignment Benchmarks is an **advanced grading form** for Moodle assignments that provides a structured checklist-based approach to assessment. It is designed for **competency-based assessment** where students must demonstrate specific skills or knowledge against defined criteria.

### What "Benchmarks" Means in This Plugin

**Important:** This plugin uses "benchmark" to mean a **measurable grading criterion** (like a checklist item) — NOT a performance band, standards mastery level, or competency framework.

- Teachers define benchmark groups and items
- Students demonstrate achievement against each benchmark
- Assessors check off benchmarks as achieved/not achieved
- Points accumulate based on checked benchmarks

Think of it as a **structured grading checklist** with optional training.gov.au integration.

## Who Is This Plugin For?

- **Registered Training Organisations (RTOs)** in Australia using competency-based training
- **VET providers** who assess against unit elements and performance criteria
- **Any educator** who wants structured, transparent grading criteria
- **Not for:** Performance analytics, standards-based grading bands, or automated competency determination

## Features

### Core Features
- **Structured grading** - Organize criteria into logical groups
- **Points-based scoring** - Each benchmark can have weighted points
- **Achieved/Not Achieved** - Clear binary marking for each criterion
- **Grader remarks** - Optional comments per benchmark or group
- **Student visibility** - Show benchmarks before or after grading

### TGA Integration (Australian VET)
- **One-click import** from training.gov.au
- Import **Elements & Performance Criteria**
- Import **Performance Evidence** requirements
- Import **Knowledge Evidence** requirements
- **Auto-numbering** (1, 1.1, 1.2...) for professional formatting

### Bulk Builder
- **Paste from anywhere** - Excel, Word, text files
- Supports numbered outlines, CSV, and Markdown
- Preview before applying
- Instantly create complex checklists

### Technical
- **Moodle 4.0 - 5.x compatible**
- Full **backup/restore** support
- **AMD JavaScript** build chain (production-safe)
- **PHPUnit tests** included
- **17 languages** supported

## Installation

1. Download `gradingform_benchmarks_v1.2.0.zip`
2. Extract to `/wwwroot/grade/grading/form/benchmarks`
3. Visit Site Administration → Notifications to complete installation
4. Or use CLI: `php admin/cli/upgrade.php`

The plugin appears as "Assignment Benchmarks" in the assignment grading method selector.

## Grading Workflow

1. **Create an assignment** in your course
2. **Set grading method** to "Assignment Benchmarks" (under Advanced grading)
3. **Define benchmarks** - Add groups and items, or import from TGA
4. **Grade submissions** - Check off achieved benchmarks
5. **Grades flow to gradebook** automatically

## Differences from Other Grading Methods

| Feature | Rubric | Marking Guide | Benchmarks |
|---------|--------|---------------|------------|
| Structure | Criteria + levels | Free-form criteria | Groups + items |
| Grading | Select level per criterion | Score per criterion | Check/uncheck items |
| Use case | Qualitative feedback | Flexible marking | Competency checklists |
| TGA import | No | No | Yes |

## Known Limitations

- Does **not** automatically determine competency status
- Does **not** integrate with Moodle's competency framework
- Points-based (not pass/fail by default)
- TGA import requires network access to training.gov.au
- Bulk paste replaces existing benchmarks (not additive)

## Upgrade Notes

### Upgrading to v1.2.0
- **Plugin renamed** from `gradingform_checklist` to `gradingform_benchmarks`
- **New install path**: `/grade/grading/form/benchmarks/`
- If upgrading from checklist: Uninstall old plugin, install new one
- Grading data structure is unchanged

### Upgrading from earlier versions
- Always backup before upgrading
- No data migration required within the benchmarks plugin

## Capabilities

The plugin defines these capabilities:

| Capability | Description | Default Roles |
|------------|-------------|---------------|
| `gradingform/benchmarks:editdefinition` | Create/edit benchmark forms | Editing Teacher, Manager |
| `gradingform/benchmarks:grade` | Grade using benchmarks | Teacher, Editing Teacher, Manager |
| `gradingform/benchmarks:viewresults` | View grading results | Student, Teacher, Manager |


## ⭐ Why this plugin is unlike anything else available

**AI benchmarking against published vocational competency standards**

- Standard Moodle grading forms (rubrics, marking guides) require a teacher to pre-write every criterion. Assignment Benchmarks reads the competency unit descriptor — from TGA or pasted text — and generates the benchmark criteria automatically from the performance criteria and required skills listed in the unit.
- Integrates inside Moodle's native assignment grading screen. Teachers see a benchmarked criteria panel alongside the submission without leaving the standard grading workflow.
- Every criterion is tied to a specific performance criterion from the unit descriptor, so the feedback is evidence of attainment, not a teacher's opinion.

## Support

- **Portal:** [lms-labs.com](https://lms-labs.com)
- **Email:** support@lmshostingservices.com
- **Website:** [lmshostingservices.com](https://lmshostingservices.com)

LMS Labs is the plugin division of LMS Hosting Services, Australia's Moodle™ Certified Partner.

## Pricing

**$50 USD** — one-time purchase per site · lifetime updates · no subscription.

Download at [lms-labs.com/plugins](https://lms-labs.com/plugins).

## License

Copyright (c) 2025 AI Grader (lms-labs.com)

Based on the Open LMS Checklist Grading Form plugin.

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <http://www.gnu.org/licenses/>.
