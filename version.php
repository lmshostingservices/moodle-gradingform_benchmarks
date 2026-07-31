<?php
// This file is part of Moodle - http://moodle.org/
//
// Assignment Benchmarks
// Enhanced grading method with TGA integration for lms-labs.com
// Based on the standard gradingform_benchmarks plugin with bulk paste and training.gov.au import.

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'gradingform_benchmarks';

// Version above official plugin to prevent Moodle "updates".
$plugin->version   = 2026072300207;   // 2026-07-17, v1.2.34

// Minimum Moodle version required (4.0+).
$plugin->requires  = 2022041900;

// Moodle 4.0 - 5.x supported.
$plugin->supported = [400, 500];

$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.2.37'; // FIX-BM-ZEROGRADE-PROXY (v1.2.34): assign_proxy subclass promotes process_add_attempt() to public — fixes "Call to protected method assign::process_add_attempt() from scope gradingform_benchmarks\observer" on Moodle installs where that method is declared protected. No schema change.

