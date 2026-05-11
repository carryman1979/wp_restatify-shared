(function (global) {
    'use strict';

    var root = global || window;
    var hasFocusTracker = false;
    var lastFocusedField = null;

    function getTinyMceEditor(editorId) {
        if (!root.tinymce || typeof root.tinymce.get !== 'function') {
            return null;
        }
        return root.tinymce.get(editorId);
    }

    function setActiveTab(tab, activeClass) {
        if (!(tab instanceof HTMLElement)) {
            return;
        }
        tab.classList.add(activeClass);
    }

    function clearActiveTab(tab, activeClass) {
        if (!(tab instanceof HTMLElement)) {
            return;
        }
        tab.classList.remove(activeClass);
    }

    function initTabSystem(config) {
        var tabSelector = config.tabSelector;
        var panelSelector = config.panelSelector;
        var tabGroupAttr = config.tabGroupAttr;
        var panelGroupAttr = config.panelGroupAttr;
        var panelAttr = config.panelAttr;
        var activeClass = config.activeClass || 'is-active';
        var onSwitch = typeof config.onSwitch === 'function' ? config.onSwitch : null;

        if (!tabSelector || !panelSelector || !tabGroupAttr || !panelGroupAttr || !panelAttr) {
            return;
        }

        document.querySelectorAll(tabSelector).forEach(function (tabBtn) {
            tabBtn.addEventListener('click', function (event) {
                event.preventDefault();

                var group = tabBtn.getAttribute(tabGroupAttr) || '';
                var panel = tabBtn.getAttribute(panelAttr) || '';
                if (!group || !panel) {
                    return;
                }

                document.querySelectorAll(tabSelector + '[' + tabGroupAttr + '="' + group + '"]').forEach(function (btn) {
                    if (btn === tabBtn) {
                        setActiveTab(btn, activeClass);
                    } else {
                        clearActiveTab(btn, activeClass);
                    }
                });

                document.querySelectorAll(panelSelector + '[' + panelGroupAttr + '="' + group + '"]').forEach(function (panelEl) {
                    var isActive = panelEl.getAttribute(panelAttr) === panel;
                    panelEl.hidden = !isActive;
                    if (isActive) {
                        setActiveTab(panelEl, activeClass);
                    } else {
                        clearActiveTab(panelEl, activeClass);
                    }
                });

                if (onSwitch) {
                    onSwitch(group, panel, tabBtn);
                }
            });
        });
    }

    function bindHtmlCodeSync(config) {
        var codeSelector = config.codeSelector;
        var codeForAttr = config.codeForAttr;

        if (!codeSelector || !codeForAttr) {
            return;
        }

        document.querySelectorAll(codeSelector).forEach(function (codeField) {
            if (!(codeField instanceof HTMLTextAreaElement)) {
                return;
            }

            var editorId = codeField.getAttribute(codeForAttr) || '';
            if (!editorId) {
                return;
            }

            var htmlField = document.getElementById(editorId);
            if (htmlField instanceof HTMLTextAreaElement) {
                codeField.value = htmlField.value || '';
            }

            codeField.addEventListener('input', function () {
                var nextValue = codeField.value || '';
                var editorTextarea = document.getElementById(editorId);
                if (editorTextarea instanceof HTMLTextAreaElement) {
                    editorTextarea.value = nextValue;
                }

                var editor = getTinyMceEditor(editorId);
                if (editor && typeof editor.setContent === 'function') {
                    editor.setContent(nextValue);
                }
            });
        });
    }

    function syncCodeFromEditor(editorId, codeSelector, codeForAttr) {
        var codeField = document.querySelector(codeSelector + '[' + codeForAttr + '="' + editorId + '"]');
        if (!(codeField instanceof HTMLTextAreaElement)) {
            return;
        }

        var editor = getTinyMceEditor(editorId);
        if (editor && typeof editor.getContent === 'function') {
            codeField.value = editor.getContent() || '';
            return;
        }

        var htmlField = document.getElementById(editorId);
        if (htmlField instanceof HTMLTextAreaElement) {
            codeField.value = htmlField.value || '';
        }
    }

    function ensureFocusTracker() {
        if (hasFocusTracker) {
            return;
        }

        document.addEventListener('focusin', function (event) {
            var target = event.target;
            if (target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement) {
                lastFocusedField = target;
            }
        });

        hasFocusTracker = true;
    }

    function insertAtCursor(field, value) {
        if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) {
            return;
        }

        var start = typeof field.selectionStart === 'number' ? field.selectionStart : field.value.length;
        var end = typeof field.selectionEnd === 'number' ? field.selectionEnd : field.value.length;

        field.value = field.value.slice(0, start) + value + field.value.slice(end);

        var nextPos = start + value.length;
        field.focus();
        if (typeof field.setSelectionRange === 'function') {
            field.setSelectionRange(nextPos, nextPos);
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function insertIntoEditorById(editorId, value, onAfterEditorInsert) {
        if (!editorId) {
            return false;
        }

        var editor = getTinyMceEditor(editorId);
        if (editor && typeof editor.insertContent === 'function' && !editor.isHidden()) {
            editor.focus();
            editor.insertContent(value);
            if (typeof onAfterEditorInsert === 'function') {
                onAfterEditorInsert(editorId);
            }
            return true;
        }

        var targetEl = document.getElementById(editorId);
        if (targetEl instanceof HTMLInputElement || targetEl instanceof HTMLTextAreaElement) {
            insertAtCursor(targetEl, value);
            return true;
        }

        return false;
    }

    function initPlaceholderButtons(config) {
        var buttonSelector = config.buttonSelector;
        var placeholderAttr = config.placeholderAttr || 'data-placeholder';
        var targetAttr = config.targetAttr || 'data-target';
        var activeEditorIdResolver = typeof config.activeEditorIdResolver === 'function'
            ? config.activeEditorIdResolver
            : null;
        var onAfterEditorInsert = typeof config.onAfterEditorInsert === 'function'
            ? config.onAfterEditorInsert
            : null;

        if (!buttonSelector) {
            return;
        }

        ensureFocusTracker();

        document.querySelectorAll(buttonSelector).forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var value = button.getAttribute(placeholderAttr) || '';
                if (!value) {
                    return;
                }

                var targetId = button.getAttribute(targetAttr) || '';
                if (insertIntoEditorById(targetId, value, onAfterEditorInsert)) {
                    return;
                }

                if (activeEditorIdResolver) {
                    var activeEditorId = activeEditorIdResolver(button);
                    if (insertIntoEditorById(activeEditorId || '', value, onAfterEditorInsert)) {
                        return;
                    }
                }

                if (root.tinymce && root.tinymce.activeEditor && !root.tinymce.activeEditor.isHidden()) {
                    root.tinymce.activeEditor.focus();
                    root.tinymce.activeEditor.insertContent(value);
                    var activeId = root.tinymce.activeEditor.id || '';
                    if (activeId && typeof onAfterEditorInsert === 'function') {
                        onAfterEditorInsert(activeId);
                    }
                    return;
                }

                if (lastFocusedField instanceof HTMLInputElement || lastFocusedField instanceof HTMLTextAreaElement) {
                    insertAtCursor(lastFocusedField, value);
                    return;
                }

                var quicktagsField = document.querySelector('.wp-editor-area:focus');
                if (quicktagsField instanceof HTMLTextAreaElement) {
                    insertAtCursor(quicktagsField, value);
                }
            });
        });
    }

    root.RestatifySharedMailEditor = {
        initTabSystem: initTabSystem,
        bindHtmlCodeSync: bindHtmlCodeSync,
        syncCodeFromEditor: syncCodeFromEditor,
        initPlaceholderButtons: initPlaceholderButtons,
        getTinyMceEditor: getTinyMceEditor,
    };
})(typeof window !== 'undefined' ? window : this);
