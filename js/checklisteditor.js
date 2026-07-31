M.gradingform_benchmarkseditor = M.gradingform_benchmarkseditor|| {'templates' : {}, 'name' : null, 'Y' : null};

/**
 * This function is called for each checklisteditor on page.
 */
M.gradingform_benchmarkseditor.init = function(Y, options) {
    M.gradingform_benchmarkseditor.name = options.name;
    M.gradingform_benchmarkseditor.Y = Y;
    M.gradingform_benchmarkseditor.templates[options.name] = {
        'group' : options.grouptemplate,
        'item'  : options.itemtemplate
    };

    M.gradingform_benchmarkseditor.disablealleditors();
    Y.on('click', M.gradingform_benchmarkseditor.clickanywhere, 'body', null);
    YUI().use('event-touch', function (Y) {
        Y.one('body').on('touchstart', M.gradingform_benchmarkseditor.clickanywhere);
        Y.one('body').on('touchend', M.gradingform_benchmarkseditor.clickanywhere);
    });

    // Keydown length validator for definition inputs
    Y.all('input[id$="-definition-input"]').on('keydown', M.gradingform_benchmarkseditor.lengthvalidator);

    //Event handler for submit buttons
    Y.one('#checklist-' + options.name).delegate('click', M.gradingform_benchmarkseditor.buttonclick, 'input[type=submit]');
    Y.one('#checklist-' + options.name).delegate('key', M.gradingform_benchmarkseditor.handlekey, 'press:13', 'input[type=text]');
};

// switches all input text elements to non-edit mode
M.gradingform_benchmarkseditor.disablealleditors = function() {
    var Y = M.gradingform_benchmarkseditor.Y;
    var name = M.gradingform_benchmarkseditor.name;

    Y.all('#checklist-' + name + ' .item').each( function(node) {M.gradingform_benchmarkseditor.editmode(node, false)} );
    Y.all('#checklist-' + name + ' .description').each( function(node) {M.gradingform_benchmarkseditor.editmode(node, false)} );
};

M.gradingform_benchmarkseditor.handlekey = function(e) {
    e.preventDefault();
    M.gradingform_benchmarkseditor.disablealleditors();
};

// function invoked on each click on the page. If item and/or group description is clicked
// it switches this element to edit mode. If checklist button is clicked it does nothing so the 'buttonclick'
// function is invoked
M.gradingform_benchmarkseditor.clickanywhere = function(e) {
    if (e.type == 'touchstart') {
        return;
    }
    var el = e.target;
    // if clicked on button - disablecurrenteditor, continue
    if (el.get('tagName') == 'INPUT' && el.get('type') == 'submit') {
        return;
    }
    // else if clicked on item and this item is not enabled - enable it
    // or if clicked on description and this description is not enabled - enable it
    var focustb = false;
    while (el && !(el.hasClass('item') || el.hasClass('description'))) {
        if (el.hasClass('score')) {
            focustb = true;
        }
        el = el.get('parentNode');
    }
    if (el) {
        if (el.one('input[type=text]').hasClass('hiddenelement')) {
            M.gradingform_benchmarkseditor.disablealleditors();
            M.gradingform_benchmarkseditor.editmode(el, true, focustb);
        }
        return;
    }
    // else disablecurrenteditor
    M.gradingform_benchmarkseditor.disablealleditors();
};

// switch the group description or item to edit mode or switch back
M.gradingform_benchmarkseditor.editmode = function(el, editmode, focustb) {
    var ta = el.one('input[type=text]');
    if (!editmode && ta.hasClass('hiddenelement')) return;
    if (editmode && !ta.hasClass('hiddenelement')) return;
    var pseudotablink = '<input type="text" size="1" class="pseudotablink"/>',
        taplain = ta.get('parentNode').one('.plainvalue'),
        tbplain = null,
        tb = el.one('.score input[type=text]');
    // add 'plainvalue' next to textbox for description/definition and next to input text field for score (if applicable)
    if (!taplain) {
        ta.get('parentNode').append('<div class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></div>');
        taplain = ta.get('parentNode').one('.plainvalue');
        taplain.one('.pseudotablink').on('focus', M.gradingform_benchmarkseditor.clickanywhere);
        if (tb) {
            tb.get('parentNode').append('<span class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></span>');
            tbplain = tb.get('parentNode').one('.plainvalue');
            tbplain.one('.pseudotablink').on('focus', M.gradingform_benchmarkseditor.clickanywhere);
        }
    }
    if (tb && !tbplain) tbplain = tb.get('parentNode').one('.plainvalue');
    if (!editmode) {
        // if we need to hide the input fields, copy their contents to plainvalue(s). If description/definition
        // is empty, display the default text ('Click to edit ...') and add/remove 'empty' CSS class to element
        var value = ta.get('value');
        if (value.length) {
            taplain.removeClass('empty');
        } else {
            value = (el.hasClass('item')) ? M.str.gradingform_benchmarks.itemempty : M.str.gradingform_benchmarks.groupempty;
            taplain.addClass('empty');
        }
        taplain.one('.textvalue').set('innerHTML', value);
        if (tb) {
            tbplain.one('.textvalue').set('innerHTML', tb.get('value'));
        }
        // hide/display textarea, textbox and plaintexts
        taplain.removeClass('hiddenelement');
        ta.addClass('hiddenelement');
        if (tb) {
            tbplain.removeClass('hiddenelement');
            tb.addClass('hiddenelement');
        }
    } else {
        // if we need to show the input fields, set the width/height for textarea so it fills the cell
//        try {
//            var width = parseFloat(ta.get('parentNode').getComputedStyle('width')),
//                height
//            if (el.hasClass('item')) height = parseFloat(el.getComputedStyle('height')) - parseFloat(el.one('.score').getComputedStyle('height'))
//            else height = parseFloat(ta.get('parentNode').getComputedStyle('height'))
//            ta.setStyle('width', Math.max(width,50)+'px')
//            ta.setStyle('height', Math.max(height,20)+'px')
//        }
//        catch (err) {
//            // this browser do not support 'computedStyle', leave the default size of the textbox
//        }
        // hide/display textarea, textbox and plaintexts
        taplain.addClass('hiddenelement');
        ta.removeClass('hiddenelement');
        if (tb) {
            tbplain.addClass('hiddenelement');
            tb.removeClass('hiddenelement');
        }
    }
    // focus the proper input field in edit mode
    if (editmode) {
        if (tb && focustb)  {
            tb.focus();
        } else {
            ta.focus();
        }
    }
};

// handler for clicking on submit buttons within checklisteditor element. Adds/deletes/rearranges groups and/or items on client side
M.gradingform_benchmarkseditor.buttonclick = function(e, confirmed) {
    var Y = M.gradingform_benchmarkseditor.Y;
    var name = M.gradingform_benchmarkseditor.name;
    if (e.target.get('type') != 'submit') {
        return;
    }
    M.gradingform_benchmarkseditor.disablealleditors();
    var chunks = e.target.get('id').split('-'),
        action = chunks[chunks.length-1];
    if (chunks[0] != name || chunks[1] != 'groups') {
        return;
    }
    var elements_str;
    if (chunks.length > 4 || action == 'additem') {
        elements_str = '#checklist-' + name + ' #' + name + '-groups-' + chunks[2] + '-items .item';
    } else {
        elements_str = '#checklist-' + name + ' .group';
    }
    // prepare the id of the next inserted item or group
    if (action == 'addgroup' || action == 'additem') {
        var newid = M.gradingform_benchmarkseditor.calculatenewid('#checklist-' + name + ' .group');
        var newlevid = M.gradingform_benchmarkseditor.calculatenewid('#checklist-' + name + ' .item');
    }
    var dialog_options = {
        'scope' : this,
        'callbackargs' : [e, true],
        'callback' : M.gradingform_benchmarkseditor.buttonclick
    };
    if (chunks.length == 3 && action == 'addgroup') {
        // ADD NEW GROUP
        var newscore= 1, levidx = 0;
        var parentel = Y.one('#' + name + '-groups');

        var itemsstr = '';
        for (levidx = 0; levidx < 3; levidx++) {
            itemsstr += M.gradingform_benchmarkseditor.templates[name]['item'].replace(/\{ITEM-id\}/g, 'NEWID' + (newlevid + levidx)).replace(/\{ITEM-score\}/g, newscore);
        }
        var newgroup = M.gradingform_benchmarkseditor.templates[name]['group'].replace(/\{ITEMS\}/, itemsstr);
        parentel.append(newgroup.replace(/\{GROUP-id\}/g, 'NEWID' + newid).replace(/\{.+?\}/g, ''));
        M.gradingform_benchmarkseditor.assignclasses('#checklist-' + name + ' #' + name + '-groups-NEWID' + newid + '-items .item');
        M.gradingform_benchmarkseditor.disablealleditors();
        M.gradingform_benchmarkseditor.assignclasses(elements_str);
        M.gradingform_benchmarkseditor.editmode(Y.one('#checklist-' + name + ' #' + name + '-groups-NEWID' + newid + '-description'),true);
    } else if (chunks.length == 5 && action == 'additem') {
        // ADD NEW ITEM
        var newscore = 1;
        var parent = Y.one('#' + name + '-groups-' + chunks[2] + '-items');
        var newitem = M.gradingform_benchmarkseditor.templates[name]['item'].
            replace(/\{GROUP-id\}/g, chunks[2]).replace(/\{ITEM-id\}/g, 'NEWID' + newlevid).replace(/\{ITEM-score\}/g, newscore).replace(/\{.+?\}/g, '');
        parent.append(newitem);
        M.gradingform_benchmarkseditor.disablealleditors();
        M.gradingform_benchmarkseditor.assignclasses(elements_str);
        M.gradingform_benchmarkseditor.editmode(parent.all('.item').item(parent.all('.item').size()-1), true);
    } else if (chunks.length == 4 && action == 'moveup') {
        // MOVE GROUP UP
        var el = Y.one('#' + name + '-groups-' + chunks[2]);
        var previous = el.previous();
        if (previous) {
            el.get('parentNode').insertBefore(el, previous);
        }
        M.gradingform_benchmarkseditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'movedown') {
        // MOVE GROUP DOWN
        var el = Y.one('#' + name + '-groups-' + chunks[2]);
        if (el.next()) el.get('parentNode').insertBefore(el.next(), el);
        M.gradingform_benchmarkseditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'delete') {
        // DELETE GROUP
        if (confirmed) {
            Y.one('#' + name + '-groups-' + chunks[2]).remove();
            M.gradingform_benchmarkseditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_benchmarks.confirmdeletegroup;
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else if (chunks.length == 6 && action == 'delete') {
        // DELETE ITEM
        if (confirmed) {
            Y.one('#' + name + '-groups-' + chunks[2] + '-' + chunks[3] + '-' + chunks[4]).remove();
            M.gradingform_benchmarkseditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_benchmarks.confirmdeleteitem;
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else {
        // unknown action
        return;
    }
    e.preventDefault();
};

// properly set classes (first/last/odd/even), item width and/or group sortorder for elements Y.all(elements_str)
M.gradingform_benchmarkseditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_benchmarkseditor.Y.all(elements_str);
    for (var i=0;i<elements.size();i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''));
        elements.item(i).all('input[type=hidden]').each(
            function(node) {if (node.get('name').match(/sortorder/)) node.set('value', i)}
        );
    }
};

// returns unique id for the next added element, it should not be equal to any of Y.all(elements_str) ids
M.gradingform_benchmarkseditor.calculatenewid = function (elements_str) {
    var newid = 1;
    M.gradingform_benchmarkseditor.Y.all(elements_str).each( function(node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        if (id.match(/^NEWID(\d+)$/)) newid = Math.max(newid, parseInt(id.substring(5)) + 1);
    } );
    return newid
};

M.gradingform_benchmarkseditor.lengthvalidator = function (e) {
    // Ignore control keys and direction keys
    if (e.keyCode < 32 || (e.keyCode >= 33 && e.keyCode <= 40)) {
        return;
    }

    var max = parseInt(e.target.getAttribute('maxlength'));
    if (e.target.get('value').length >= max) {
        e.preventDefault();
        window.alert(M.str.gradingform_benchmarks.maxlengthalert.replace('{$a}', max));
    }
};


/**
 * Bulk builder: parse user text using a selected format and build groups/items.
 * Formats:
 *   parts     -  "Part A: Group Title" headers + "1." numbered criteria as items (default)
 *   outline   -  "1. Group" headers + "1.1" sub-items as items
 *   markdown  -  "# Group" headers + "- item" bullet items
 */
/**
 * Shared helper: inject groups/items into the checklist editor using direct
 * YUI template manipulation, identical to what buttonclick() does internally.
 * Avoids calling .getDOMNode().click() on submit buttons, which triggers an
 * actual form submission on Moodle 4.x (causing a page reload) and therefore
 * broke the bulk-paste and TGA-import features on all pre-Moodle-5 sites.
 *
 * @param {Object} Y     YUI instance
 * @param {string} name  Checklist element name (same as init options.name)
 * @param {Array}  groups Array of {label, items[]} objects
 */
M.gradingform_benchmarkseditor.applyGroups = function(Y, name, groups) {
    if (!groups || !groups.length) { return; }
    var editor = M.gradingform_benchmarkseditor;
    var groupsContainer = Y.one('#' + name + '-groups');
    if (!groupsContainer) { return; }

    // Full override: remove all existing groups before inserting new ones.
    groupsContainer.all('.group').remove(true);

    Y.Array.each(groups, function(g) {
        groupsContainer = Y.one('#' + name + '-groups');
        if (!groupsContainer) { return; }

        // Re-calculate unique IDs each iteration to pick up previous additions.
        var newGroupId = editor.calculatenewid('#checklist-' + name + ' .group');

        // Build group HTML with no default items ({ITEMS} replaced with empty string).
        var newGroupHtml = editor.templates[name]['group']
            .replace(/{ITEMS}/, '')
            .replace(/{GROUP-id}/g, 'NEWID' + newGroupId)
            .replace(/{.+?}/g, '');

        groupsContainer.append(newGroupHtml);
        editor.assignclasses('#checklist-' + name + ' .group');

        // Set group description input value.
        if (g.label) {
            var newGroupEl = Y.one('#' + name + '-groups-NEWID' + newGroupId);
            if (newGroupEl) {
                var ginput = newGroupEl.one('input[type=text]');
                if (ginput) { ginput.set('value', g.label); }
            }
        }

        // Add items directly using the item template.
        if (g.items && g.items.length) {
            var itemsSel = '#' + name + '-groups-NEWID' + newGroupId + '-items';
            var itemsContainer = Y.one(itemsSel);
            if (!itemsContainer) { return; }

            Y.Array.each(g.items, function(it) {
                var newItemId = editor.calculatenewid('#checklist-' + name + ' .item');
                var newItemHtml = editor.templates[name]['item']
                    .replace(/{GROUP-id}/g, 'NEWID' + newGroupId)
                    .replace(/{ITEM-id}/g, 'NEWID' + newItemId)
                    .replace(/{ITEM-score}/g, 1)
                    .replace(/{.+?}/g, '');

                itemsContainer.append(newItemHtml);

                // Set item definition input value.
                var allItems = itemsContainer.all('.item');
                var newItem = allItems.item(allItems.size() - 1);
                if (newItem) {
                    var idef = newItem.one('.definition input[type=text]');
                    if (idef) { idef.set('value', it); }
                }

                editor.assignclasses(itemsSel + ' .item');
            });
        }
    });

    // Finalise: copy all input values into display spans and switch to read mode.
    editor.disablealleditors();
};

M.gradingform_benchmarkseditor.init_bulkbuilder = function(Y, name) {
    var container = Y.one('#' + name + '-groups');
    if (!container) {
        return;
    }
    var bulkinput = Y.one('#' + name + '-bulkbuilder-input');
    if (!bulkinput) {
        return;
    }
    var previewarea = Y.one('#' + name + '-bulkbuilder-preview');

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    function getFormat() {
        var radios = Y.all('input[name="' + name + '-bulkformat"]:checked');
        if (radios && radios.size() > 0) {
            return radios.item(0).get('value');
        }
        return 'parts';
    }

    // ---------------------------------------------------------------
    // Parse: Parts style  (Part A/B/C: Title  +  1. numbered items)
    // ---------------------------------------------------------------
    function parseParts(text) {
        var lines = text.split(/\r?\n/);
        var entries = [];
        var sectionHeader = /^(Part|Section|Unit|Module|Stage|Phase|Area|Group)\s+[A-Za-z0-9]+[\s\S]*$/i;
        var numberedItem  = /^[0-9]+\.\s+(.+)/;
        var subNumbered   = /^[0-9]+\.[0-9]+\s+(.+)/;
        Y.Array.each(lines, function(line) {
            var trimmed = Y.Lang.trim(line);
            if (!trimmed) {return;}
            // Sub-numbered (1.1)  ->  always item
            if (subNumbered.test(trimmed)) {
                entries.push({type: 'item', label: trimmed});
                return;
            }
            // Simple numbered (1.)  ->  item
            if (numberedItem.test(trimmed)) {
                entries.push({type: 'item', label: trimmed});
                return;
            }
            // Section keyword header (Part/Section/Unit/etc.)  ->  group
            if (sectionHeader.test(trimmed)) {
                entries.push({type: 'group', label: trimmed, items: []});
                return;
            }
            // Everything else (plain text criterion)  ->  item, not a group.
            // This is the correct behaviour for plain-list pastes where the
            // user just pastes one criterion per line without any Part headers.
            entries.push({type: 'item', label: trimmed});
        });
        return groupEntries(entries);
    }

    // ---------------------------------------------------------------
    // Parse: Numbered outline  (1. Group  +  1.1 sub-items)
    // ---------------------------------------------------------------
    function parseOutline(text) {
        var lines = text.split(/\r?\n/);
        var entries = [];
        Y.Array.each(lines, function(line) {
            var trimmed = Y.Lang.trim(line);
            if (!trimmed) {return;}

            // Sub-numbered (1.1)  ->  item
            if (/^[0-9]+\.[0-9]+\s+(.+)/.test(trimmed)) {
                entries.push({type: 'item', label: trimmed});
                return;
            }
            // Simple numbered (1.)  ->  group header
            if (/^[0-9]+\.\s+(.+)/.test(trimmed)) {
                entries.push({type: 'group', label: trimmed, items: []});
                return;
            }
            // Markdown group (# / ## / ###)  ->  group header
            var mdGroup = trimmed.match(/^#{1,3}\s+(.+)/);
            if (mdGroup) {
                entries.push({type: 'group', label: mdGroup[1], items: []});
                return;
            }
            // v1.2.25 FIX-BM-COMMA-BULLET: Markdown bullet check moved BEFORE CSV detection.
            // The previous approach (v1.2.24) guarded the CSV path with "!/^[-*]\s/" but that
            // negative-regex exclusion could be bypassed by Unicode dashes (en/em dash) or any
            // edge case where the bullet character was not a plain ASCII hyphen or asterisk.
            // Moving the bullet match first means a dash/asterisk line ALWAYS returns as an item
            // before the CSV logic is reached  -  commas anywhere in the text are never split.
            var mdItem = trimmed.match(/^[-*]\s+(.+)/);
            if (mdItem) {
                entries.push({type: 'item', label: mdItem[1]});
                return;
            }
            // CSV: Group, Item 1, Item 2...
            // Only runs for lines that did not match any numbered, heading, or bullet pattern above.
            // v1.2.25: Split only on commas NOT inside parentheses so text like
            // "Group name (option A, option B), Item 1" still parses as a single group label
            // followed by real CSV items  -  the parenthetical content is never split.
            if (trimmed.indexOf(',') !== -1) {
                var csvParts = [];
                var curr = '';
                var depth = 0;
                for (var ci = 0; ci < trimmed.length; ci++) {
                    var ch = trimmed.charAt(ci);
                    if (ch === '(') { depth++; curr += ch; }
                    else if (ch === ')') { depth = depth > 0 ? depth - 1 : 0; curr += ch; }
                    else if (ch === ',' && depth === 0) { csvParts.push(Y.Lang.trim(curr)); curr = ''; }
                    else { curr += ch; }
                }
                csvParts.push(Y.Lang.trim(curr));
                // Only treat as CSV when there is at least one non-empty item after the label.
                var hasItems = false;
                for (var pi = 1; pi < csvParts.length; pi++) {
                    if (csvParts[pi]) { hasItems = true; break; }
                }
                if (csvParts[0] && hasItems) {
                    var group = {type: 'group', label: csvParts[0], items: []};
                    for (var vi = 1; vi < csvParts.length; vi++) {
                        if (csvParts[vi]) { group.items.push(csvParts[vi]); }
                    }
                    entries.push(group);
                    return;
                }
            }
            // Fallback  ->  group
            entries.push({type: 'group', label: trimmed, items: []});
        });
        return groupEntries(entries);
    }

    // ---------------------------------------------------------------
    // Parse: Markdown style  (# Group  +  - bullet items)
    // ---------------------------------------------------------------
    function parseMarkdown(text) {
        var lines = text.split(/\r?\n/);
        var entries = [];
        Y.Array.each(lines, function(line) {
            var trimmed = Y.Lang.trim(line);
            if (!trimmed) {return;}
            // Markdown heading  ->  group
            var mdGroup = trimmed.match(/^#{1,3}\s+(.+)/);
            if (mdGroup) {
                entries.push({type: 'group', label: mdGroup[1], items: []});
                return;
            }
            // Bullet (- or *)  ->  item
            var mdItem = trimmed.match(/^[-*]\s+(.+)/);
            if (mdItem) {
                entries.push({type: 'item', label: mdItem[1]});
                return;
            }
            // Numbered (1. or 1.1)  ->  item
            if (/^[0-9]+\./.test(trimmed)) {
                entries.push({type: 'item', label: trimmed});
                return;
            }
            // Fallback  ->  group
            entries.push({type: 'group', label: trimmed, items: []});
        });
        return groupEntries(entries);
    }

    // ---------------------------------------------------------------
    // Shared: attach loose items to last group
    // ---------------------------------------------------------------
    function groupEntries(entries) {
        var grouped = [];
        var currentGroup = null;
        Y.Array.each(entries, function(e) {
            if (e.type === 'group') {
                currentGroup = {label: e.label, items: Y.Array(e.items || [])};
                grouped.push(currentGroup);
            } else if (e.type === 'item') {
                if (!currentGroup) {
                    currentGroup = {label: '', items: []};
                    grouped.push(currentGroup);
                }
                currentGroup.items.push(e.label);
            }
        });
        return grouped;
    }

    function parseLines(text) {
        var fmt = getFormat();
        if (fmt === 'outline') { return parseOutline(text); }
        if (fmt === 'markdown') { return parseMarkdown(text); }
        return parseParts(text); // default
    }

    // ---------------------------------------------------------------
    // ChatGPT prompt templates for each format
    // ---------------------------------------------------------------
    var chatgptPrompts = {
        parts: [
            'TASK: Reformat my existing assessment criteria into the layout below.',
            'IMPORTANT: Do NOT generate new criteria, add new items, create simulations,',
            'or expand the content in any way. Only reformat and reorganise the criteria',
            'I paste at the bottom of this prompt  -  using my exact words.',
            '',
            'FORMAT RULES  -  follow exactly:',
            '  - Group headers use:  Part A: [Title],  Part B: [Title]  (choose appropriate titles from my content)',
            '  - Each criterion is numbered:  1.  2.  3.  (number, full stop, space, then the criterion text)',
            '  - Do NOT use sub-numbers like 1.1 or 1.2',
            '  - Leave one blank line between the group header and the first criterion',
            '  - Leave one blank line between groups',
            '  - Do not add any extra headings, bullet points, or formatting',
            '  - Do not add introductory text or closing remarks  -  output the formatted list only',
            '',
            'FORMAT EXAMPLE (structure only  -  do not use this content):',
            'Part A: [Title from my content]',
            '',
            '1. [my criterion text, unchanged]',
            '2. [my criterion text, unchanged]',
            '',
            'Part B: [Title from my content]',
            '',
            '1. [my criterion text, unchanged]',
            '2. [my criterion text, unchanged]',
            '',
            'Paste as code so numbered bullets can be copied as well.',
            '',
            '--- PASTE YOUR EXISTING CRITERIA BELOW THIS LINE ---',
            ''
        ].join('\n'),

        outline: [
            'TASK: Reformat my existing assessment criteria into a numbered outline format.',
            'IMPORTANT: Do NOT generate new criteria, add new items, or expand the content',
            'in any way. Only reformat and reorganise the criteria I paste at the bottom of',
            'this prompt  -  using my exact words.',
            '',
            'FORMAT RULES  -  follow exactly:',
            '  - Group headers use a single number:  1.  2.  3.  (choose groupings from my content)',
            '  - Each item under a group uses a sub-number:  1.1  1.2  1.3',
            '  - Leave one blank line between groups',
            '  - Do not add bullet points, hashes, or any other formatting',
            '  - Do not add introductory text or closing remarks  -  output the formatted list only',
            '',
            'FORMAT EXAMPLE (structure only  -  do not use this content):',
            '1. [Group title from my content]',
            '1.1 [my criterion text, unchanged]',
            '1.2 [my criterion text, unchanged]',
            '',
            '2. [Group title from my content]',
            '2.1 [my criterion text, unchanged]',
            '2.2 [my criterion text, unchanged]',
            '',
            'Paste as code so numbered bullets can be copied as well.',
            '',
            '--- PASTE YOUR EXISTING CRITERIA BELOW THIS LINE ---',
            ''
        ].join('\n'),

        markdown: [
            'TASK: Reformat my existing assessment criteria into Markdown-style formatting.',
            'IMPORTANT: Do NOT generate new criteria, add new items, or expand the content',
            'in any way. Only reformat and reorganise the criteria I paste at the bottom of',
            'this prompt  -  using my exact words.',
            '',
            'FORMAT RULES  -  follow exactly:',
            '  - Group headers start with a single hash:  # [Title from my content]',
            '  - Each item under a group starts with a dash:  - [criterion text]',
            '  - Leave one blank line between groups',
            '  - Do not use numbers, sub-numbers, or any other formatting',
            '  - Do not add introductory text or closing remarks  -  output the formatted list only',
            '',
            'FORMAT EXAMPLE (structure only  -  do not use this content):',
            '# [Group title from my content]',
            '- [my criterion text, unchanged]',
            '- [my criterion text, unchanged]',
            '',
            '# [Group title from my content]',
            '- [my criterion text, unchanged]',
            '- [my criterion text, unchanged]',
            '',
            'Paste as code so numbered bullets can be copied as well.',
            '',
            '--- PASTE YOUR EXISTING CRITERIA BELOW THIS LINE ---',
            ''
        ].join('\n')
    };

    function downloadChatGPTPrompt() {
        var fmt = getFormat();
        var content = chatgptPrompts[fmt] || chatgptPrompts['parts'];
        var fname = 'benchmark_chatgpt_prompt_' + fmt + '.txt';
        try {
            var blob = new Blob([content], {type: 'text/plain;charset=utf-8'});
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = fname;
            document.body.appendChild(a);
            a.click();
            setTimeout(function() {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 200);
        } catch (ex) {
            window.open('data:text/plain;charset=utf-8,' + encodeURIComponent(content), '_blank');
        }
    }

    // ---------------------------------------------------------------
    // Preview
    // ---------------------------------------------------------------
    function renderPreview(groups) {
        if (!previewarea) {return;}
        if (!groups || !groups.length) {
            previewarea.setHTML('');
            return;
        }
        var html = '<div class="bulkbuilder-preview-inner">';
        Y.Array.each(groups, function(g) {
            html += '<div class="bulkbuilder-preview-group">';
            html += '<div class="bulkbuilder-preview-grouptitle">' + Y.Escape.html(g.label || '(No group title)') + '</div>';
            if (g.items && g.items.length) {
                html += '<ol class="bulkbuilder-preview-items">';
                Y.Array.each(g.items, function(it) {
                    html += '<li>' + Y.Escape.html(it) + '</li>';
                });
                html += '</ol>';
            }
            html += '</div>';
        });
        html += '</div>';
        previewarea.setHTML(html);
    }

    // ---------------------------------------------------------------
    // Apply to checklist
    // ---------------------------------------------------------------
    function applyToChecklist(groups) {
        // Delegate to shared helper  -  avoids .getDOMNode().click() on submit buttons,
        // which caused form submission (page reload) on Moodle 4.x.
        M.gradingform_benchmarkseditor.applyGroups(Y, name, groups);
    }

    // ---------------------------------------------------------------
    // Wire up events
    // ---------------------------------------------------------------
    var currentGroups = null;

    // Re-parse on typing
    if (bulkinput) {
        bulkinput.on('input', function() {
            currentGroups = parseLines(bulkinput.get('value'));
            renderPreview(currentGroups);
        });
    }

    // Re-parse when format radio changes
    Y.all('input[name="' + name + '-bulkformat"]').on('change', function() {
        currentGroups = parseLines(bulkinput.get('value'));
        renderPreview(currentGroups);
    });

    var previewbtn = Y.one('.bulkbuilder-preview[data-elementname="' + name + '"]');
    if (previewbtn) {
        previewbtn.on('click', function(e) {
            e.preventDefault();
            currentGroups = parseLines(bulkinput.get('value'));
            renderPreview(currentGroups);
        });
    }

    var applybtn = Y.one('.bulkbuilder-apply[data-elementname="' + name + '"]');
    if (applybtn) {
        applybtn.on('click', function(e) {
            e.preventDefault();
            if (!currentGroups || !currentGroups.length) {
                currentGroups = parseLines(bulkinput.get('value'));
            }
            applyToChecklist(currentGroups);
        });
    }

    var downloadbtn = Y.one('.bulkbuilder-download-prompt[data-elementname="' + name + '"]');
    if (downloadbtn) {
        downloadbtn.on('click', function(e) {
            e.preventDefault();
            downloadChatGPTPrompt();
        });
    }
};

/**
 * TGA Import: fetch unit from training.gov.au and insert groups/items.
 */
M.gradingform_benchmarkseditor.init_tgaimport = function(Y, name) {
    var fetchbtn = Y.one('#' + name + '-tga-fetch');
    var insertbtn = Y.one('#' + name + '-tga-insert');
    var unitcodeinput = Y.one('#' + name + '-tga-unitcode');
    var statusdiv = Y.one('#' + name + '-tga-status');
    
    if (!fetchbtn || !unitcodeinput) {
        return;
    }
    
    var fetchedUnit = null;
    
    function applyTGAToChecklist(groups) {
        // Delegate to shared helper  -  avoids .getDOMNode().click() on submit buttons,
        // which caused form submission (page reload) on Moodle 4.x.
        M.gradingform_benchmarkseditor.applyGroups(Y, name, groups);
    }
    
    fetchbtn.on('click', function(e) {
        e.preventDefault();
        var code = unitcodeinput.get('value').trim().toUpperCase();
        if (!code) {
            statusdiv.setHTML('<span style="color: var(--gcl-error);">Please enter a unit code</span>');
            return;
        }
        
        fetchbtn.set('disabled', true);
        fetchbtn.setHTML(M.str.gradingform_benchmarks.tgafetching || 'Fetching...');
        statusdiv.setHTML('<span style="color: var(--gcl-text-secondary);">Fetching unit data...</span>');
        
        require(['core/ajax'], function(Ajax) {
            Ajax.call([{
                methodname: 'gradingform_benchmarks_get_unit',
                args: { code: code }
            }])[0].done(function(result) {
                fetchbtn.set('disabled', false);
                fetchbtn.setHTML(M.str.gradingform_benchmarks.tgafetch || 'Fetch unit');
                
                if (result.success) {
                    fetchedUnit = result;
                    var msg = (M.str.gradingform_benchmarks.tgaunitfound || 'Found: {$a}').replace('{$a}', result.title || result.code);
                    statusdiv.setHTML('<span style="color: var(--gcl-success);">' + Y.Escape.html(msg) + '</span>');
                    if (insertbtn) {
                        insertbtn.set('disabled', false);
                    }
                } else {
                    fetchedUnit = null;
                    statusdiv.setHTML('<span style="color: var(--gcl-error);">' + Y.Escape.html(result.error || 'Unit not found') + '</span>');
                    if (insertbtn) {
                        insertbtn.set('disabled', true);
                    }
                }
            }).fail(function(err) {
                fetchbtn.set('disabled', false);
                fetchbtn.setHTML(M.str.gradingform_benchmarks.tgafetch || 'Fetch unit');
                fetchedUnit = null;
                statusdiv.setHTML('<span style="color: var(--gcl-error);">Error fetching unit: ' + Y.Escape.html(err.message || 'Unknown error') + '</span>');
                if (insertbtn) {
                    insertbtn.set('disabled', true);
                }
            });
        });
    });
    
    if (insertbtn) {
        insertbtn.on('click', function(e) {
            e.preventDefault();
            if (!fetchedUnit) {
                statusdiv.setHTML('<span style="color: var(--gcl-error);">Please fetch a unit first</span>');
                return;
            }
            
            var includeElements = Y.one('#' + name + '-tga-elements');
            var includePE = Y.one('#' + name + '-tga-pe');
            var includeKE = Y.one('#' + name + '-tga-ke');
            var autoNumber = Y.one('#' + name + '-tga-autonumber');
            
            var wantElements = includeElements && includeElements.get('checked');
            var wantPE = includePE && includePE.get('checked');
            var wantKE = includeKE && includeKE.get('checked');
            var wantAutoNumber = autoNumber && autoNumber.get('checked');
            
            if (!wantElements && !wantPE && !wantKE) {
                statusdiv.setHTML('<span style="color: var(--gcl-error);">' + (M.str.gradingform_benchmarks.tganoselection || 'Please select at least one option') + '</span>');
                return;
            }
            
            var groups = [];
            var groupNum = 1;
            
            if (wantElements && fetchedUnit.elements && fetchedUnit.elements.length) {
                Y.Array.each(fetchedUnit.elements, function(el) {
                    var groupLabel = wantAutoNumber ? (groupNum + '. ' + el.name) : el.name;
                    var items = [];
                    var itemNum = 1;
                    if (el.performancecriteria && el.performancecriteria.length) {
                        Y.Array.each(el.performancecriteria, function(pc) {
                            var itemLabel = wantAutoNumber ? (groupNum + '.' + itemNum + ' ' + pc.text) : pc.text;
                            items.push(itemLabel);
                            itemNum++;
                        });
                    }
                    groups.push({ label: groupLabel, items: items });
                    groupNum++;
                });
            }
            
            if (wantPE && fetchedUnit.performanceevidence && fetchedUnit.performanceevidence.length) {
                var peLabel = wantAutoNumber ? (groupNum + '. ' + (M.str.gradingform_benchmarks.tgapegroup || 'Performance Evidence')) : (M.str.gradingform_benchmarks.tgapegroup || 'Performance Evidence');
                var peItems = [];
                var peNum = 1;
                Y.Array.each(fetchedUnit.performanceevidence, function(item) {
                    var itemLabel = wantAutoNumber ? (groupNum + '.' + peNum + ' ' + item.text) : item.text;
                    peItems.push(itemLabel);
                    peNum++;
                });
                groups.push({ label: peLabel, items: peItems });
                groupNum++;
            }
            
            if (wantKE && fetchedUnit.knowledgeevidence && fetchedUnit.knowledgeevidence.length) {
                var keLabel = wantAutoNumber ? (groupNum + '. ' + (M.str.gradingform_benchmarks.tgakegroup || 'Knowledge Evidence')) : (M.str.gradingform_benchmarks.tgakegroup || 'Knowledge Evidence');
                var keItems = [];
                var keNum = 1;
                Y.Array.each(fetchedUnit.knowledgeevidence, function(item) {
                    var itemLabel = wantAutoNumber ? (groupNum + '.' + keNum + ' ' + item.text) : item.text;
                    keItems.push(itemLabel);
                    keNum++;
                });
                groups.push({ label: keLabel, items: keItems });
            }
            
            if (groups.length) {
                applyTGAToChecklist(groups);
                statusdiv.setHTML('<span style="color: var(--gcl-success);">Inserted ' + groups.length + ' groups from ' + fetchedUnit.code + '</span>');
            }
        });
    }
};

// Wrap original init to also call bulkbuilder and tgaimport.
(function(origInit) {
    M.gradingform_benchmarkseditor.init = function(Y, options) {
        origInit(Y, options);
        if (options && options.name) {
            M.gradingform_benchmarkseditor.init_bulkbuilder(Y, options.name);
            M.gradingform_benchmarkseditor.init_tgaimport(Y, options.name);
        }
    };
})(M.gradingform_benchmarkseditor.init);

