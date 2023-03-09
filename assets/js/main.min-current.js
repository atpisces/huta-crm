/* v2.9.1 */
$(window).on("load", function() {
    init_btn_with_tooltips()
}), $.fn.dataTable.ext.errMode = "throw", $.fn.dataTableExt.oStdClasses.sWrapper = "dataTables_wrapper form-inline dt-bootstrap table-loading", "1" == app.options.enable_google_picker && ($.fn.googleDrivePicker.defaults.clientId = app.options.google_client_id, $.fn.googleDrivePicker.defaults.developerKey = app.options.google_api), Dropzone.options.newsFeedDropzone = !1, Dropzone.options.salesUpload = !1, "Notification" in window && "1" == app.options.desktop_notifications && Notification.requestPermission();
var original_top_search_val, table_leads, table_activity_log, table_estimates, table_invoices, table_tasks, menu_href_selector, tab_active = get_url_param("tab"),
    tab_group = get_url_param("group"),
    side_bar = $("#side-menu"),
    content_wrapper = $("#wrapper"),
    setup_menu = $("#setup-menu-wrapper"),
    calendar_selector = $("#calendar"),
    notifications_wrapper = $("#header").find("li.notifications-wrapper"),
    doc_initial_title = document.title,
    newsfeed_posts_page = 0,
    track_load_post_likes = 0,
    track_load_comment_likes = 0,
    post_likes_total_pages = 0,
    comment_likes_total_pages = 0,
    select_picker_validated_event = !1,
    postid = 0,
    lastAddedItemKey = null,
    setup_menu_item = $("#setup-menu-item");

function _make_task_checklist_items_deletable() {
    if ("1" == app.options.has_permission_tasks_checklist_items_delete) {
        var i = $("body").find(".checklist-templates-wrapper ul.dropdown-menu li").not(":first-child"),
            e = $("body").find(".checklist-templates-wrapper select option").not(":first-child");
        $.each(e, function(e, t) {
            var a = $(t);
            0 === $(i[e]).find(".checklist-item-template-remove").length && $(i[e]).find("a > span.text").after('<small class="checklist-item-template-remove" onclick="remove_checklist_item_template(' + a.attr("value") + '); event.stopPropagation();"><i class="fa fa-remove"></i></small>')
        })
    }
}

function _init_tasks_billable_select(e, t) {
    var a = $("#task_select");
    if (0 < a.length) {
        var i;
        a.find("option").filter(function() {
            return this.value || 0 < $.trim(this.value).length || 0 < $.trim(this.text).length
        }).remove(), $.each(e, function(e, t) {
            i = " ", !0 === t.started_timers ? i += 'disabled class="text-danger important" data-subtext="' + app.lang.invoice_task_billable_timers_found + '"' : !1 === t.started_timers && "project" != t.rel_type && (i += 'data-subtext="' + t.rel_name + '"'), a.append('<option value="' + t.id + '"' + i + ">" + t.name + "</option>")
        });
        var n = $(".input-group-addon-bill-tasks-help");
        n.find(".popover-invoker").popover("destroy"), n.empty();
        var s = "";
        s = empty(t) ? app.lang.invoice_task_item_project_tasks_not_included : app.lang.showing_billable_tasks_from_project + " " + $("#project_id option:selected").text().trim(), n.html('<span class="pointer popover-invoker" data-container=".form-group-select-task_select" data-trigger="click" data-placement="top" data-toggle="popover" data-content="' + s + '"><i class="fa fa-question-circle"></i></span>'), delay(function() {
            (n.attr("info-shown-count") < 3 || void 0 === n.attr("info-shown-count")) && $(".projects-wrapper").is(":visible") && 0 < e.length && (n.attr("info-shown-count", void 0 === n.attr("info-shown-count") ? 1 : parseInt(n.attr("info-shown-count")) + 1), n.find(".popover-invoker").click())
        }, 3500)
    }
    a.selectpicker("refresh")
}

function mainWrapperHeightFix() {
    var e = side_bar.height(),
        t = $("#wrapper").find(".content").height();
    setup_menu.css("min-height", $(document).outerHeight(!0) - 126 + "px"), content_wrapper.css("min-height", $(document).outerHeight(!0) - 63 + "px"), t < e && content_wrapper.css("min-height", e + "px"), t < e && e < $(window).height() && content_wrapper.css("min-height", $(window).height() - 63 + "px"), e < t && t < $(window).height() && content_wrapper.css("min-height", $(window).height() - 63 + "px"), is_mobile() && "true" == isRTL && side_bar.css("min-height", $(document).outerHeight(!0) - 63 + "px")
}

function set_body_small() {
    $(this).width() < 769 ? $("body").addClass("page-small") : $("body").removeClass("page-small show-sidebar")
}

function switch_field(e) {
    var t;
    !(t = 0) === $(e).prop("checked") && (t = 1), requestGet($(e).data("switch-url") + "/" + $(e).data("id") + "/" + t)
}

function _validate_form(e, t, a, i) {
    appValidateForm(e, t, a, i)
}

function delete_option(t, e) {
    confirm_delete() && requestGetJSON("settings/delete_option/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || $(t).parents(".option").remove()
    })
}

function init_rel_tasks_table(e, t, a) {
    void 0 === a && (a = ".table-rel-tasks");
    var i = $("body").find(a);
    if (0 !== i.length) {
        var n, s = {},
            o = [0];
        n = $("body").find("._hidden_inputs._filters._tasks_filters input"), $.each(n, function() {
            s[$(this).attr("name")] = '[name="' + $(this).attr("name") + '"]'
        });
        var l = admin_url + "tasks/init_relation_tasks/" + e + "/" + t;
        "project" == i.attr("data-new-rel-type") && (l += "?bulk_actions=true"), initDataTable(i, l, o, o, s, [i.find("th.duedate").index(), "asc"])
    }
}

function initDataTableInline(e) {
    appDataTableInline(e, {
        supportsButtons: !0,
        supportsLoading: !0,
        autoWidth: !1
    })
}

function initDataTable(e, t, a, i, n, s) {
    var o = "string" == typeof e ? $("body").find("table" + e) : e;
    if (0 === o.length) return !1;
    n = "undefined" == n || void 0 === n ? [] : n, void 0 === s ? s = [
        [0, "asc"]
    ] : 1 === s.length && (s = [s]);
    var l = o.attr("data-default-order");
    if (!empty(l)) {
        var d = JSON.parse(l),
            r = [];
        for (var c in d) 0 < o.find("thead th:eq(" + d[c][0] + ")").length && r.push(d[c]);
        0 < r.length && (s = r)
    }
    var p = [10, 25, 50, 100],
        _ = [10, 25, 50, 100];
    app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit), -1 == $.inArray(app.options.tables_pagination_limit, p) && (p.push(app.options.tables_pagination_limit), _.push(app.options.tables_pagination_limit)), p.sort(function(e, t) {
        return e - t
    }), _.sort(function(e, t) {
        return e - t
    }), p.push(-1), _.push(app.lang.dt_length_menu_all);
    var m = {
            language: app.lang.datatables,
            processing: !0,
            retrieve: !0,
            serverSide: !0,
            paginate: !0,
            searchDelay: 750,
            bDeferRender: !0,
            autoWidth: !1,
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>",
            pageLength: app.options.tables_pagination_limit,
            lengthMenu: [p, _],
            columnDefs: [{
                searchable: !1,
                targets: a
            }, {
                sortable: !1,
                targets: i
            }],
            fnDrawCallback: function(e) {
                _table_jump_to_page(this, e), 0 === e.aoData.length ? $(e.nTableWrapper).addClass("app_dt_empty") : $(e.nTableWrapper).removeClass("app_dt_empty")
            },
            fnCreatedRow: function(e, t, a) {
                $(e).attr("data-title", t.Data_Title), $(e).attr("data-toggle", t.Data_Toggle)
            },
            initComplete: function(e, t) {
                var a = this,
                    i = $(".btn-dt-reload");
                i.attr("data-toggle", "tooltip"), i.attr("title", app.lang.dt_button_reload);
                var n = $(".dt-column-visibility");
                n.attr("data-toggle", "tooltip"), n.attr("title", app.lang.dt_button_column_visibility), a.wrap('<div class="table-responsive"></div>');
                var s = a.find(".dataTables_empty");
                s.length && s.attr("colspan", a.find("thead th").length), is_mobile() && $(window).width() < 400 && 0 < a.find('tbody td:first-child input[type="checkbox"]').length && (a.DataTable().column(0).visible(!1, !1).columns.adjust(), $("a[data-target*='bulk_actions']").addClass("hide")), a.parents(".table-loading").removeClass("table-loading"), a.removeClass("dt-table-loading");
                var o = a.find("thead th:last-child"),
                    l = a.find("thead th:first-child");
                o.text().trim() == app.lang.options && o.addClass("not-export"), 0 < l.find('input[type="checkbox"]').length && l.addClass("not-export"), mainWrapperHeightFix()
            },
            order: s,
            ajax: {
                url: t,
                type: "POST",
                data: function(e) {
                    for (var t in "undefined" != typeof csrfData && (e[csrfData.token_name] = csrfData.hash), n) e[t] = $(n[t]).val();
                    o.attr("data-last-order-identifier") && (e.last_order_identifier = o.attr("data-last-order-identifier"))
                }
            },
            buttons: get_datatable_buttons(o)
        },
        u = (o = o.dataTable(m)).DataTable(),
        f = o.find("th.not_visible"),
        h = [];
    if ($.each(f, function() {
            h.push(this.cellIndex)
        }), setTimeout(function() {
            for (var e in h) u.columns(h[e]).visible(!1, !1).columns.adjust()
        }, 10), o.hasClass("customizable-table")) {
        var v = o.find("th.toggleable"),
            b = $("#hidden-columns-" + o.attr("id"));
        try {
            b = JSON.parse(b.text())
        } catch (e) {
            b = []
        }
        $.each(v, function() {
            var e = $(this).attr("id"); - 1 < $.inArray(e, b) && u.column("#" + e).visible(!1)
        })
    }
    return o.is(":hidden") && o.find(".dataTables_empty").attr("colspan", o.find("thead th").length), o.on("preXhr.dt", function(e, t, a) {
        t.jqXHR && t.jqXHR.abort()
    }), u
}

function task_single_update_tags() {
    var e = $("#taskTags");
    $.post(admin_url + "tasks/update_tags", {
        tags: e.tagit("assignedTags"),
        task_id: e.attr("data-taskid")
    })
}

function task_attachments_toggle() {
    var e = $("#task-modal");
    e.find(".task_attachments_wrapper .task-attachments-more").toggleClass("hide"), e.find(".task_attachments_wrapper .task-attachments-less").toggleClass("hide")
}

function update_todo_items() {
    var e = $(".unfinished-todos li:not(.no-todos)"),
        t = $(".finished-todos li:not(.no-todos)"),
        a = 1;
    $.each(e, function() {
        $(this).find('input[name="todo_order"]').val(a), $(this).find('input[name="finished"]').val(0), a++
    }), 0 === e.length ? ($(".nav-total-todos").addClass("hide"), $(".unfinished-todos li.no-todos").removeClass("hide")) : 0 < e.length && ($(".unfinished-todos li.no-todos").hasClass("hide") || $(".unfinished-todos li.no-todos").addClass("hide"), $(".nav-total-todos").removeClass("hide").html(e.length)), x = 1, $.each(t, function() {
        $(this).find('input[name="todo_order"]').val(x), $(this).find('input[name="finished"]').val(1), $(this).find('input[type="checkbox"]').prop("checked", !0), a++, x++
    }), 0 === t.length ? $(".finished-todos li.no-todos").removeClass("hide") : 0 < t.length && ($(".finished-todos li.no-todos").hasClass("hide") || $(".finished-todos li.no-todos").addClass("hide"));
    var i = [];
    $.each(e, function() {
        var e = $(this).find(".todo-description");
        e.hasClass("line-throught") && e.removeClass("line-throught"), $(this).find('input[type="checkbox"]').prop("checked", !1), i.push([$(this).find('input[name="todo_id"]').val(), $(this).find('input[name="todo_order"]').val(), $(this).find('input[name="finished"]').val()])
    }), $.each(t, function() {
        var e = $(this).find(".todo-description");
        e.hasClass("line-throught") || e.addClass("line-throught"), i.push([$(this).find('input[name="todo_id"]').val(), $(this).find('input[name="todo_order"]').val(), $(this).find('input[name="finished"]').val()])
    }), data = {}, data.data = i, $.post(admin_url + "todo/update_todo_items_order", data)
}

function delete_todo_item(t, e) {
    requestGetJSON("todo/delete_todo_item/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || ($(t).parents("li").remove(), update_todo_items())
    })
}

function edit_todo_item(e) {
    requestGetJSON("todo/get_by_id/" + e).done(function(e) {
        var t = $("#__todo");
        t.find('input[name="todoid"]').val(e.todoid), t.find('textarea[name="description"]').val(e.description), t.modal("show")
    })
}

function init_datepicker(e, t) {
    appDatepicker({
        element_date: e,
        element_time: t
    })
}

function init_color_pickers() {
    appColorPicker()
}

function init_selectpicker() {
    appSelectPicker()
}

function init_lightbox() {
    appLightbox()
}

function init_progress_bars() {
    appProgressBar()
}

function init_tags_inputs() {
    appTagsInput()
}

function dt_custom_view(e, t, a, i) {
    var n = void 0 === a ? "custom_view" : a;
    if (void 0 !== i) {
        var s = $("._filter_data li.active").not(".clear-all-prevent");
        s.removeClass("active"), $.each(s, function() {
            var e = $(this).find("a").attr("data-cview");
            $('._filters input[name="' + e + '"]').val("")
        })
    }
    do_filter_active(n) != n && (e = ""), $('input[name="' + n + '"]').val(e), $(t).DataTable().ajax.reload()
}

function do_filter_active(e, t) {
    if ("" === e || void 0 === e) return $("._filters input").val(""), $("._filter_data li.active").removeClass("active"), $('[data-cview="all"]').parents("li").addClass("active"), "";
    $('[data-cview="all"]').parents("li").removeClass("active");
    var a = $('[data-cview="' + e + '"]');
    void 0 !== t && (a = $(t + ' [data-cview="' + e + '"]'));
    var i = a.parents("li");
    if (i.hasClass("filter-group")) {
        var n = i.data("filter-group");
        $('[data-filter-group="' + n + '"]').not(i).removeClass("active"), $.each($('[data-filter-group="' + n + '"]').not(i), function() {
            $('input[name="' + $(this).find("a").attr("data-cview") + '"]').val("")
        })
    }
    if (i.not(".dropdown-submenu").hasClass("active")) {
        i.not(".dropdown-submenu").removeClass("active");
        var s = a.parents("li.dropdown-submenu");
        0 < s.length && 0 === s.find("li.active").length && s.removeClass("active"), e = ""
    } else i.addClass("active");
    return e
}

function init_roles_permissions(e, t) {
    if (e = void 0 === e ? $('select[name="role"]').val() : e, !(0 < $('.member > input[name="isedit"]').length && void 0 !== e && void 0 === t) && !0 !== $('input[name="administrator"]').prop("checked") && "" !== e) {
        var a = $("table.roles").find("tr");
        requestGetJSON("staff/role_changed/" + e).done(function(e) {
            a.find(".capability").not('[data-not-applicable="true"]').prop("checked", !1).trigger("change"), $.each(a, function() {
                var i = $(this);
                $.each(e, function(a, e) {
                    i.data("name") == a && $.each(e, function(e, t) {
                        i.find('input[id="' + a + "_" + t + '"]').prop("checked", !0), "view" == t ? i.find("[data-can-view]").change() : "view_own" == t && i.find("[data-can-view-own]").change()
                    })
                })
            })
        })
    }
}

function toggle_small_view(e, t) {
    $("body").toggleClass("small-table");
    var a = $("#small-table");
    if (0 !== a.length) {
        var i = !1;
        a.hasClass("col-md-5") ? (a.removeClass("col-md-5").addClass("col-md-12"), i = !0, $(".toggle-small-view").find("i").removeClass("fa fa-angle-double-right").addClass("fa fa-angle-double-left")) : (a.addClass("col-md-5").removeClass("col-md-12"), $(".toggle-small-view").find("i").removeClass("fa fa-angle-double-left").addClass("fa fa-angle-double-right"));
        var n = $(e).DataTable();
        n.columns(hidden_columns).visible(i, !1), n.columns.adjust(), $(t).toggleClass("hide"), $(window).trigger("resize")
    }
}

function logout() {
    if (0 < $(".started-timers-top").find("li.timer").length) return system_popup({
        message: " ",
        content: $("#timers-logout-template-warning").html()
    }).find(".popup-message").addClass("hide"), !1;
    window.location.href = admin_url + "authentication/logout"
}

function elFinderBrowser(t, e, a, i) {
    return tinymce.activeEditor.windowManager.open({
        file: admin_url + "misc/tinymce_file_browser",
        title: app.lang.media_files,
        width: 900,
        height: 450,
        resizable: "yes"
    }, {
        setUrl: function(e) {
            i.document.getElementById(t).value = e
        }
    }), !1
}

function init_editor(e, t) {
    e = void 0 === e ? ".tinymce" : e;
    var a = $(e);
    if (0 !== a.length) {
        $.each(a, function() {
            $(this).hasClass("tinymce-manual") && $(this).removeClass("tinymce")
        });
        var i = {
            branding: !1,
            selector: e,
            browser_spellcheck: !0,
            height: 400,
            theme: "modern",
            skin: "perfex",
            language: app.tinymce_lang,
            relative_urls: !1,
            inline_styles: !0,
            verify_html: !1,
            cleanup: !1,
            autoresize_bottom_margin: 25,
            valid_elements: "+*[*]",
            valid_children: "+body[style], +style[type]",
            apply_source_formatting: !1,
            remove_script_host: !1,
            removed_menuitems: "newdocument restoredraft",
            forced_root_block: !1,
            autosave_restore_when_empty: !1,
            fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
            setup: function(e) {
                e.on("init", function() {
                    this.getDoc().body.style.fontSize = "12pt"
                })
            },
            table_default_styles: {
                width: "100%"
            },
            plugins: ["advlist autoresize autosave lists link image print hr codesample", "visualblocks code fullscreen", "media save table contextmenu", "paste textcolor colorpicker"],
            toolbar1: "fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft",
            file_browser_callback: elFinderBrowser,
            contextmenu: "link image inserttable | cell row column deletetable | paste copy"
        };
        if ("true" == isRTL && (i.directionality = "rtl"), "true" == isRTL && (i.plugins[0] += " directionality"), void 0 !== t)
            for (var n in t) "append_plugins" != n ? i[n] = t[n] : i.plugins.push(t[n]);
        var s = tinymce.init(i);
        return $(document).trigger("app.editor.initialized"), s
    }
}

function _formatMenuIconInput(e) {
    if (void 0 !== e) {
        var t = $(e.target);
        t.val().match(/^fa /) || t.val("fa " + t.val())
    }
}

function init_btn_with_tooltips() {
    if (is_mobile()) {
        if (null != navigator.userAgent.match(/iPad/i)) return !1;
        var e = $("._buttons").find(".btn-with-tooltip");
        $.each(e, function() {
            var e = $(this).attr("title");
            void 0 === e && (e = $(this).attr("data-title")), void 0 !== e && ($(this).append(" " + e), $(this).removeClass("btn-with-tooltip"))
        });
        var t = $("._buttons").find(".btn-with-tooltip-group");
        $.each(t, function() {
            var e = $(this).attr("title");
            void 0 === e && (e = $(this).attr("data-title")), void 0 !== e && ($(this).find(".btn").eq(0).append(" " + e), $(this).removeClass("btn-with-tooltip-group"))
        })
    }
}

function do_hash_helper(e) {
    if (void 0 !== history.pushState) {
        var t = {
            Url: window.location.href
        };
        history.pushState(t, "", t.Url), window.location.hash = e
    }
}

function init_form_reminder(e) {
    var t = e ? $("#form-reminder-" + e) : $('[id^="form-reminder-"]');
    $.each(t, function(e, t) {
        $(t).appFormValidator({
            rules: {
                date: "required",
                staff: "required",
                description: "required"
            },
            submitHandler: reminderFormHandler
        })
    })
}

function new_task_reminder(e) {
    var t = $("#newTaskReminderToggle");
    !t.is(":visible") || t.is(":visible") && null != t.attr("data-edit") ? (t.slideDown(400, function() {
        fix_task_modal_left_col_height()
    }), $("#taskReminderFormSubmit").html(app.lang.create_reminder), t.find("form").attr("action", admin_url + "tasks/add_reminder/" + e), t.find("#description").val(""), t.find("#date").val(""), t.find("#staff").selectpicker("val", t.find("#staff").attr("data-current-staff")), t.find("#notify_by_email").prop("checked", !1), null != t.attr("data-edit") && t.removeAttr("data-edit"), t.isInViewport() || $("#task-modal").animate({
        scrollTop: t.offset().top + "px"
    }, "fast")) : t.slideUp()
}

function edit_reminder(i, e) {
    requestGetJSON("misc/get_reminder/" + i).done(function(e) {
        var t = $(".reminder-modal-" + e.rel_type + "-" + e.rel_id),
            a = admin_url + "misc/edit_reminder/" + i;
        0 === t.length && $("body").hasClass("all-reminders") ? ((t = $(".reminder-modal--")).find('input[name="rel_type"]').val(e.rel_type), t.find('input[name="rel_id"]').val(e.rel_id)) : $("#task-modal").is(":visible") && ((t = $("#newTaskReminderToggle")).attr("data-edit") && t.attr("data-edit") == i ? (t.slideUp(), t.removeAttr("data-edit")) : (t.slideDown(400, function() {
            fix_task_modal_left_col_height()
        }), t.attr("data-edit", i), t.isInViewport() || $("#task-modal").animate({
            scrollTop: t.offset().top + "px"
        }, "fast")), a = admin_url + "tasks/edit_reminder/" + i, $("#taskReminderFormSubmit").html(app.lang.save)), t.find("form").attr("action", a), t.find("form").attr("data-edit", !0), t.find("#description").val(e.description), t.find("#date").val(e.date), t.find("#staff").selectpicker("val", e.staff), t.find("#notify_by_email").prop("checked", 1 == e.notify_by_email), t.hasClass("modal") && t.modal("show")
    })
}

function reminderFormHandler(t) {
    var e = (t = $(t)).serialize();
    return $.post(t.attr("action"), e).done(function(e) {
        "" !== (e = JSON.parse(e)).message && alert_float(e.alert_type, e.message), t.trigger("reinitialize.areYouSure"), $("#task-modal").is(":visible") && _task_append_html(e.taskHtml), reload_reminders_tables()
    }), $("body").hasClass("all-reminders") ? $(".reminder-modal--").modal("hide") : $(".reminder-modal-" + t.find('[name="rel_type"]').val() + "-" + t.find('[name="rel_id"]').val()).modal("hide"), !1
}

function reload_reminders_tables() {
    $.each([".table-reminders", ".table-reminders-leads", ".table-my-reminders"], function(e, t) {
        $.fn.DataTable.isDataTable(t) && $("body").find(t).DataTable().ajax.reload()
    })
}

function toggle_edit_note(e) {
    $("body").find('[data-note-edit-textarea="' + e + '"]').toggleClass("hide"), $("body").find('[data-note-description="' + e + '"]').toggleClass("hide")
}

function edit_note(t) {
    var a = $("body").find('[data-note-edit-textarea="' + t + '"] textarea').val();
    "" !== a && ($.post(admin_url + "misc/edit_note/" + t, {
        description: a
    }).done(function(e) {
        !0 !== (e = JSON.parse(e)).success && "true" != e.success || (alert_float("success", e.message), $("body").find('[data-note-description="' + t + '"]').html(nl2br(a)))
    }), toggle_edit_note(t))
}

function toggle_file_visibility(e, t, a) {
    requestGet("misc/toggle_file_visibility/" + e).done(function(e) {
        1 == e ? $(a).find("i").removeClass("fa fa-toggle-off").addClass("fa fa-toggle-on") : $(a).find("i").removeClass("fa fa-toggle-on").addClass("fa fa-toggle-off")
    })
}

function fix_kanban_height(e, t) {
    $("body").find("div.dt-loader").remove();
    var a = $(".kan-ban-content-wrapper");
    a.css("max-height", window.innerHeight - e + "px"), $(".kan-ban-content").css("min-height", window.innerHeight - e + "px");
    var i = parseInt(a.length);
    $(".container-fluid").css("min-width", i * t + "px")
}

function kanban_load_more(t, a, e, i, n) {
    var s, o = [],
        l = $('input[name="search"]').val(),
        d = $(a).attr("data-page"),
        r = $('[data-col-status-id="' + t + '"]').data("total-pages");
    if (d <= r) {
        var c = $('input[name="sort_type"]'),
            p = $('input[name="sort"]').val();
        0 != c.length && "" !== c.val() && (o.sort_by = c.val(), o.sort = p), void 0 !== l && "" !== l && (o.search = l), $.each($("#kanban-params input"), function() {
            "" !== (s = "checkbox" == $(this).attr("type") ? !0 === $(this).prop("checked") ? $(this).val() : "" : $(this).val()) && (o[$(this).attr("name")] = s)
        }), o.status = t, o.page = d, o.page++, requestGet(buildUrl(admin_url + e, o)).done(function(e) {
            d++, $('[data-load-status="' + t + '"]').before(e), $(a).attr("data-page", d), fix_kanban_height(i, n)
        }).fail(function(e) {
            alert_float("danger", e.responseText)
        }), r - 1 <= d && $(a).addClass("disabled")
    }
}

function check_kanban_empty_col(a) {
    var e = $("[data-col-status-id]");
    $.each(e, function(e, t) {
        0 == $(t).find(a).length ? ($(t).find(".kanban-empty").removeClass("hide"), $(t).find(".kanban-load-more").addClass("hide")) : $(t).find(".kanban-empty").addClass("hide")
    })
}

function init_kanban(e, a, t, i, n, s) {
    if (0 !== $("#kan-ban").length) {
        var o, l = [];
        $.each($("#kanban-params input"), function() {
            "" !== (o = "checkbox" == $(this).attr("type") ? !0 === $(this).prop("checked") ? $(this).val() : "" : $(this).val()) && (l[$(this).attr("name")] = o)
        });
        var d = $('input[name="search"]').val();
        void 0 !== d && "" !== d && (l.search = d);
        var r = $('input[name="sort_type"]'),
            c = $('input[name="sort"]').val();
        0 != r.length && "" !== r.val() && (l.sort_by = r.val(), l.sort = c), l.kanban = !0, e = admin_url + e, e = buildUrl(e, l), delay(function() {
            $("body").append('<div class="dt-loader"></div>'), $("#kan-ban").load(e, function() {
                fix_kanban_height(i, n);
                void 0 !== s && s(), $(".status").sortable({
                    connectWith: t,
                    helper: "clone",
                    appendTo: "#kan-ban",
                    placeholder: "ui-state-highlight-card",
                    revert: "invalid",
                    scrollingSensitivity: 50,
                    scrollingSpeed: 70,
                    sort: function(e, t) {
                        var a = t.placeholder[0].parentNode;
                        a = $(a).parents(".kan-ban-content-wrapper")[0];
                        var i = $(a).offset();
                        i.top + a.offsetHeight - e.pageY < 20 ? a.scrollTop = a.scrollTop + 60 : e.pageY - i.top < 20 && (a.scrollTop = a.scrollTop - 60), i.left + a.offsetWidth - e.pageX < 20 ? a.scrollLeft = a.scrollLeft + 60 : e.pageX - i.left < 20 && (a.scrollLeft = a.scrollLeft - 60)
                    },
                    change: function() {
                        var e = $(this).closest("ul"),
                            t = $(e).find(".kanban-load-more");
                        $(e).append($(t).detach())
                    },
                    start: function(e, t) {
                        $("body").css("overflow", "hidden"), $(t.helper).addClass("tilt"), $(t.helper).find(".panel-body").css("background", "#fbfbfb"), tilt_direction($(t.helper))
                    },
                    stop: function(e, t) {
                        $("body").removeAttr("style"), $(t.helper).removeClass("tilt"), $("html").off("mousemove", $(t.helper).data("move_handler")), $(t.helper).removeData("move_handler")
                    },
                    update: function(e, t) {
                        a(t, this)
                    }
                }), $(".status").sortable({
                    cancel: ".not-sortable"
                })
            })
        }, 200)
    }
}

function kan_ban_sort(e, t) {
    $('input[name="sort_type"]').val(e);
    var a = $('input[name="sort"]'),
        i = a.val().toLowerCase();
    a.val("asc" == i ? "DESC" : "ASC"), init_kan_ban_sort_icon(a.val(), e), t()
}

function init_kan_ban_sort_icon(e, t) {
    $("body").find(".kanban-sort-icon").remove(), $("body").find("." + t).prepend(" <i class='kanban-sort-icon fa fa-sort-amount-" + e.toLowerCase() + "'></i>")
}

function init_newsfeed_form() {
    "undefined" == typeof newsFeedDropzone && $("body").on("submit", "#new-post-form", function() {
        return $.post(this.action, $(this).serialize()).done(function(e) {
            if ((e = JSON.parse(e)).postid) {
                if (0 < newsFeedDropzone.getQueuedFiles().length) return newsFeedDropzone.options.url = admin_url + "newsfeed/add_post_attachments/" + e.postid, void newsFeedDropzone.processQueue();
                newsfeed_new_post(e.postid), clear_newsfeed_post_area()
            }
        }), !1
    }), newsFeedDropzone = new Dropzone("#new-post-form", appCreateDropzoneOptions({
        clickable: ".add-post-attachments",
        autoProcessQueue: !1,
        addRemoveLinks: !0,
        parallelUploads: app.options.newsfeed_maximum_files_upload,
        maxFiles: app.options.newsfeed_maximum_files_upload,
        dragover: function(e) {
            $("#new-post-form").addClass("dropzone-active")
        },
        complete: function(e) {},
        drop: function(e) {
            $("#new-post-form").removeClass("dropzone-active")
        },
        success: function(e, t) {
            0 === this.getUploadingFiles().length && 0 === this.getQueuedFiles().length && (newsfeed_new_post((t = JSON.parse(t)).postid), clear_newsfeed_post_area(), this.removeAllFiles())
        }
    }))
}

function clear_newsfeed_post_area() {
    $("#new-post-form textarea").val(""), $("#post-visibility").selectpicker("deselectAll")
}

function load_post_likes(e) {
    track_load_post_likes <= post_likes_total_pages && ($.post(admin_url + "newsfeed/load_likes_modal", {
        page: track_load_post_likes,
        postid: e
    }).done(function(e) {
        track_load_post_likes++, $("#modal_post_likes_wrapper").append(e)
    }), post_likes_total_pages - 1 <= track_load_post_likes && $(".likes_modal .modal-footer").addClass("hide"))
}

function load_comment_likes(e) {
    track_load_comment_likes <= comment_likes_total_pages && ($.post(admin_url + "newsfeed/load_comment_likes_model", {
        page: track_load_comment_likes,
        commentid: e
    }).done(function(e) {
        track_load_comment_likes++, $("#modal_comment_likes_wrapper").append(e)
    }), comment_likes_total_pages - 1 <= track_load_comment_likes && $(".likes_modal .modal-footer").addClass("hide"))
}

function load_more_comments(t) {
    var a = $(t).data("postid"),
        i = $(t).find('input[name="page"]').val(),
        e = $(t).data("total-pages");
    i <= e && ($.post(admin_url + "newsfeed/init_post_comments/" + a, {
        page: i
    }).done(function(e) {
        $(t).data("track-load-comments", i), $('[data-comments-postid="' + a + '"] .load-more-comments').before(e)
    }), i++, $(t).find('input[name="page"]').val(i), e - 1 <= i && ($(t).addClass("hide"), $(t).removeClass("display-block")))
}

function newsfeed_new_post(e) {
    var t = {};
    t.postid = e, $.post(admin_url + "newsfeed/load_newsfeed", t).done(function(e) {
        var t = $("#newsfeed_data").find(".pinned").length;
        if (0 === t) $("#newsfeed_data").prepend(e);
        else {
            var a = $("#newsfeed_data").find(".pinned").eq(t - 1);
            $(a).after(e)
        }
    })
}

function load_newsfeed(e) {
    var t = {};
    t.page = newsfeed_posts_page, void 0 !== e && 0 != e && (t.postid = e);
    var a = $('input[name="total_pages_newsfeed"]').val();
    newsfeed_posts_page <= a && $.post(admin_url + "newsfeed/load_newsfeed", t).done(function(e) {
        newsfeed_posts_page++, $("#newsfeed_data").append(e)
    })
}

function like_post(t) {
    requestGetJSON("newsfeed/like_post/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || refresh_post_likes(t)
    })
}

function unlike_post(t) {
    requestGetJSON("newsfeed/unlike_post/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || refresh_post_likes(t)
    })
}

function like_comment(t, e) {
    requestGetJSON("newsfeed/like_comment/" + t + "/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || $('[data-commentid="' + t + '"]').replaceWith(e.comment)
    })
}

function unlike_comment(t, e) {
    requestGetJSON("newsfeed/unlike_comment/" + t + "/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || $('[data-commentid="' + t + '"]').replaceWith(e.comment)
    })
}

function add_comment(t) {
    var a = $(t).data("postid");
    $.post(admin_url + "newsfeed/add_comment", {
        content: $(t).val(),
        postid: a
    }).done(function(e) {
        !0 !== (e = JSON.parse(e)).success && "true" != e.success || ($(t).val(""), 0 < $("body").find('[data-comments-postid="' + a + '"] .post-comment').length ? $("body").find('[data-comments-postid="' + a + '"] .post-comment').prepend(e.comment) : refresh_post_comments(a))
    })
}

function remove_post_comment(t, e) {
    requestGetJSON("newsfeed/remove_post_comment/" + t + "/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || $('.comment[data-commentid="' + t + '"]').remove()
    })
}

function refresh_post_likes(t) {
    requestGet("newsfeed/init_post_likes/" + t + "?refresh_post_likes=true").done(function(e) {
        $('[data-likes-postid="' + t + '"]').html(e)
    })
}

function refresh_post_comments(t) {
    $.post(admin_url + "newsfeed/init_post_comments/" + t + "?refresh_post_comments=true").done(function(e) {
        $('[data-comments-postid="' + t + '"]').html(e)
    })
}

function delete_post(t) {
    confirm_delete() && $.post(admin_url + "newsfeed/delete_post/" + t, function(e) {
        !0 !== e.success && "true" != e.success || $('[data-main-postid="' + t + '"]').remove()
    }, "json")
}

function pin_post(e) {
    requestGetJSON("newsfeed/pin_newsfeed_post/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || window.location.reload()
    })
}

function unpin_post(e) {
    requestGetJSON("newsfeed/unpin_newsfeed_post/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || window.location.reload()
    })
}

function _gen_lead_add_inline_on_select_field(e) {
    var t;
    ($("body").hasClass("leads-email-integration") || $("body").hasClass("web-to-lead-form")) && (e = "lead_" + e), t = '<div id="new_lead_' + e + '_inline" class="form-group"><label for="new_' + e + '_name">' + $('label[for="' + e + '"]').html().trim() + '</label><div class="input-group"><input type="text" id="new_' + e + '_name" name="new_' + e + '_name" class="form-control"><div class="input-group-addon"><a href="#" onclick="lead_add_inline_select_submit(\'' + e + '\'); return false;" class="lead-add-inline-submit-' + e + '"><i class="fa fa-check"></i></a></div></div></div>', $(".form-group-select-input-" + e).after(t), $("body").find("#new_" + e + "_name").focus(), $('.lead-save-btn,#form_info button[type="submit"],#leads-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !0), $(".inline-field-new").addClass("disabled").css("opacity", .5), $(".form-group-select-input-" + e).addClass("hide")
}

function new_lead_status_inline() {
    _gen_lead_add_inline_on_select_field("status")
}

function new_lead_source_inline() {
    _gen_lead_add_inline_on_select_field("source")
}

function lead_add_inline_select_submit(a) {
    var i = $("#new_" + a + "_name").val().trim();
    if ("" !== i) {
        var e = a; - 1 < a.indexOf("lead_") && (e = e.replace("lead_", ""));
        var t = {};
        t.name = i, t.inline = !0, $.post(admin_url + "leads/" + e, t).done(function(e) {
            if (!0 === (e = JSON.parse(e)).success || "true" == e.success) {
                var t = $("body").find("select#" + a);
                t.append('<option value="' + e.id + '">' + i + "</option>"), t.selectpicker("val", e.id), t.selectpicker("refresh"), t.parents(".form-group").removeClass("has-error")
            }
        })
    }
    $("#new_lead_" + a + "_inline").remove(), $(".form-group-select-input-" + a).removeClass("hide"), $('.lead-save-btn,#form_info button[type="submit"],#leads-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !1), $(".inline-field-new").removeClass("disabled").removeAttr("style")
}

function init_lead(e, t) {
    $("#task-modal").is(":visible") && $("#task-modal").modal("hide"), init_lead_modal_data(e, void 0, t) && $("#lead-modal").modal("show")
}

function validate_lead_form() {
    var a = {
            name: "required",
            source: "required",
            status: {
                required: {
                    depends: function(e) {
                        return !(0 < $("[lead-is-junk-or-lost]").length)
                    }
                }
            }
        },
        i = {};
    $.each(leadUniqueValidationFields, function(e, t) {
        a[t] = {}, "email" == t && (a[t].email = !0), a[t].remote = {
            url: admin_url + "leads/validate_unique_field",
            type: "post",
            data: {
                field: t,
                lead_id: function() {
                    return $("#lead-modal").find('input[name="leadid"]').val()
                }
            }
        }, void 0 !== app.lang[t + "_exists"] && (i[t] = {
            remote: app.lang[t + "_exists"]
        })
    }), appValidateForm($("#lead_form"), a, lead_profile_form_handler, i)
}

function validate_lead_convert_to_client_form() {
    var e = {
        firstname: "required",
        lastname: "required",
        password: {
            required: {
                depends: function(e) {
                    if (!1 === $('input[name="send_set_password_email"]').prop("checked")) return !0
                }
            }
        },
        email: {
            required: !0,
            email: !0,
            remote: {
                url: admin_url + "misc/contact_email_exists",
                type: "post",
                data: {
                    email: function() {
                        return $('#lead_to_client_form input[name="email"]').val()
                    },
                    userid: ""
                }
            }
        }
    };
    1 == app.options.company_is_required && (e.company = "required"), appValidateForm($("#lead_to_client_form"), e)
}

function lead_profile_form_handler(e) {
    var t = (e = $(e)).serialize();
    $("#lead-modal").find('input[name="leadid"]').val();
    return $(".lead-save-btn").addClass("disabled"), $.post(e.attr("action"), t).done(function(e) {
        "" !== (e = JSON.parse(e)).message && alert_float("success", e.message), e.proposal_warning && 0 != e.proposal_warning ? ($("body").find("#lead_proposal_warning").removeClass("hide"), $("body").find("#lead-modal").animate({
            scrollTop: 0
        }, 800)) : _lead_init_data(e, e.id), $.fn.DataTable.isDataTable(".table-leads") ? table_leads.DataTable().ajax.reload(null, !1) : $("body").hasClass("kan-ban-body") && leads_kanban()
    }).fail(function(e) {
        return alert_float("danger", e.responseText), !1
    }), !1
}

function update_all_proposal_emails_linked_to_lead(t) {
    $.post(admin_url + "leads/update_all_proposal_emails_linked_to_lead/" + t, {
        update: !0
    }).done(function(e) {
        (e = JSON.parse(e)).success && alert_float("success", e.message), init_lead_modal_data(t)
    })
}

function _lead_init_data(e, i) {
    var t = window.location.hash,
        a = $("#lead-modal");
    $("#lead_reminder_modal").html(e.leadView.reminder_data), a.find(".data").html(e.leadView.data), a.modal({
        show: !0,
        backdrop: "static"
    }), init_tags_inputs(), init_selectpicker(), init_form_reminder(), init_datepicker(), init_color_pickers(), validate_lead_form();
    if (-1 < ["#tab_lead_profile", "#attachments", "#lead_notes", "#lead_activity", "#gdpr"].indexOf(t) && (window.location.hash = t), initDataTableInline($("#consentHistoryTable")), $("#lead-modal").find(".gpicker").googleDrivePicker({
            onPick: function(e) {
                leadExternalFileUpload(e, "gdrive", i)
            }
        }), "" !== i && void 0 !== i) {
        "undefined" != typeof Dropbox && document.getElementById("dropbox-chooser-lead").appendChild(Dropbox.createChooseButton({
            success: function(e) {
                leadExternalFileUpload(e, "dropbox", i)
            },
            linkType: "preview",
            extensions: app.options.allowed_files.split(",")
        })), "undefined" != typeof leadAttachmentsDropzone && leadAttachmentsDropzone.destroy(), leadAttachmentsDropzone = new Dropzone("#lead-attachment-upload", appCreateDropzoneOptions({
            sending: function(e, t, a) {
                a.append("id", i), 0 === this.getQueuedFiles().length && a.append("last_file", !0)
            },
            success: function(e, t) {
                t = JSON.parse(t), 0 === this.getUploadingFiles().length && 0 === this.getQueuedFiles().length && _lead_init_data(t, t.id)
            }
        })), a.find('.nav-tabs a[href="' + window.location.hash + '"]').tab("show");
        var n = a.find("#lead_activity .feed-item:last-child .text").html();
        void 0 !== n ? a.find("#lead-latest-activity").html(n) : a.find(".lead-latest-activity > .lead-info-heading").addClass("hide"), 0 < $("[lead-is-junk-or-lost]").length && $(".form-group-select-input-status").find(".req").remove()
    }
}

function init_lead_modal_data(t, e, a) {
    var i = (void 0 !== e ? e : "leads/lead/") + (void 0 !== t ? t : "");
    if (!0 === a) {
        var n = "?"; - 1 < i.indexOf("?") && (n += "&"), i += n + "edit=true"
    }
    requestGetJSON(i).done(function(e) {
        _lead_init_data(e, t)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function print_lead_information() {
    var e = $("#leadViewWrapper").clone(),
        t = e.find(".lead-name").text().trim();
    e.find("p").css("font-size", "100%").css("font", "inherit").css("vertical-align", "baseline").css("margin", "0px"), e.find("h4").css("font-size", "100%"), e.find(".lead-field-heading").css("color", "#777").css("margin-bottom", "3px"), e.find(".lead-field-heading + p").css("margin-bottom", "15px");
    var a = _create_print_window(t);
    a.document.write("<html><head><title>" + app.lang.lead + "</title>"), _add_print_window_default_styles(a), a.document.write("<style>"), a.document.write(".lead-information-col { float: left; width: 33.33333333%;}"), a.document.write("</style>"), a.document.write("</head><body>"), a.document.write("<h1>" + t + "</h1>"), a.document.write('<div id="#leadViewWrapper">' + e.html() + "</div>"), a.document.write("</body></html>"), a.document.close(), a.focus(), a.print(), setTimeout(function() {
        a.close()
    }, 1e3)
}

function print_expense_information() {
    var e = $("#tab_expense").clone(),
        t = $("#expenseHeadings"),
        a = t.find("#expenseCategory").text().trim() + "<h4>" + t.find("#expenseName").text().trim() + "</h4>";
    e.find("#expenseReceipt").remove(), e.find("#amountWrapper").css("margin-bottom", "15px");
    var i = _create_print_window(a);
    i.document.write("<html><head><title>" + app.lang.expense + "</title>"), _add_print_window_default_styles(i), i.document.write("</head><body>"), i.document.write("<h1>" + a + "</h1>"), i.document.write('<div id="#tab_expense">' + e.html() + "</div>"), i.document.write("</body></html>"), i.document.close(), i.focus(), i.print(), setTimeout(function() {
        i.close()
    }, 1e3)
}

function print_ticket_message(e, t) {
    var a = $("[data-" + t + '-id="' + e + '"]').html(),
        i = $("#ticket_subject").text().trim(),
        n = _create_print_window(i);
    n.document.write("<html><head><title>" + app.lang.ticket + "</title>"), _add_print_window_default_styles(n), n.document.write("</head><body>"), n.document.write("<h1>" + i + "</h1>"), n.document.write(a), n.document.write("</body></html>"), n.document.close(), n.focus(), n.print(), setTimeout(function() {
        n.close()
    }, 1e3)
}

function leads_kanban_sort(e) {
    kan_ban_sort(e, leads_kanban)
}

function leads_kanban_update(e, t) {
    if (t === e.item.parent()[0]) {
        var a = {};
        a.status = $(e.item.parent()[0]).data("lead-status-id"), a.leadid = $(e.item).data("lead-id");
        var i = [],
            n = $(e.item).parents(".leads-status").find("li"),
            s = 1;
        $.each(n, function() {
            i.push([$(this).data("lead-id"), s]), s++
        }), a.order = i, setTimeout(function() {
            $.post(admin_url + "leads/update_lead_status", a).done(function(e) {
                leads_kanban()
            })
        }, 200)
    }
}

function init_leads_status_sortable() {
    $("#kan-ban").sortable({
        helper: "clone",
        item: ".kan-ban-col",
        update: function(e, t) {
            var a = [],
                i = $(".kan-ban-col"),
                n = 0;
            $.each(i, function() {
                a.push([$(this).data("col-status-id"), n]), n++
            });
            var s = {};
            s.order = a, $.post(admin_url + "leads/update_status_order", s)
        }
    })
}

function leads_kanban(e) {
    init_kanban("leads/kanban", leads_kanban_update, ".leads-status", 315, 360, init_leads_status_sortable)
}

function delete_lead_attachment(t, e, a) {
    confirm_delete() && requestGetJSON("leads/delete_attachment/" + e + "/" + a).done(function(e) {
        !0 !== e.success && "true" != e.success || ($(t).parents(".lead-attachment-wrapper").remove(), _lead_init_data(e, e.id))
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function delete_lead_note(t, e, a) {
    confirm_delete() && requestGetJSON("leads/delete_note/" + e + "/" + a).done(function(e) {
        !0 !== e.success && "true" != e.success || ($(t).parents(".lead-note").remove(), _lead_init_data(e, e.id))
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function lead_mark_as_lost(t) {
    requestGetJSON("leads/mark_as_lost/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || (alert_float("success", e.message), $("body").find("tr#lead_" + t).remove(), $("body").find('#kan-ban li[data-lead-id="' + t + '"]').remove()), _lead_init_data(e, e.id)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function lead_unmark_as_lost(e) {
    requestGetJSON("leads/unmark_as_lost/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || alert_float("success", e.message), _lead_init_data(e, e.id)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function lead_mark_as_junk(t) {
    requestGetJSON("leads/mark_as_junk/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || (alert_float("success", e.message), $("body").find("tr#lead_" + t).remove(), $("body").find('#kan-ban li[data-lead-id="' + t + '"]').remove()), _lead_init_data(e, e.id)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function lead_mark_as(e, t) {
    var a = {};
    a.status = e, a.leadid = t, $.post(admin_url + "leads/update_lead_status", a).done(function(e) {
        table_leads.DataTable().ajax.reload(null, !1)
    })
}

function lead_unmark_as_junk(e) {
    requestGetJSON("leads/unmark_as_junk/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || alert_float("success", e.message), _lead_init_data(e, e.id)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function convert_lead_to_customer(e) {
    var t = $("#lead-modal"),
        a = "hidden.bs.modal.convert";
    t.on(a, function() {
        t.find(".data").html(""), requestGet("leads/get_convert_data/" + e).done(function(e) {
            $("#lead_convert_to_customer").html(e), $("#convert_lead_to_client_modal").modal({
                show: !0,
                backdrop: "static",
                keyboard: !1
            })
        }).fail(function(e) {
            alert_float("danger", e.responseText)
        }).always(function() {
            t.off(a)
        })
    }), t.modal("hide")
}

function leads_bulk_action(e) {
    if (confirm_delete()) {
        var t = $("#mass_delete").prop("checked"),
            a = [],
            i = {};
        if (0 == t || void 0 === t) {
            if (i.lost = $("#leads_bulk_mark_lost").prop("checked"), i.status = $("#move_to_status_leads_bulk").val(), i.assigned = $("#assign_to_leads_bulk").val(), i.source = $("#move_to_source_leads_bulk").val(), i.last_contact = $("#leads_bulk_last_contact").val(), i.tags = $("#tags_bulk").tagit("assignedTags"), i.visibility = $('input[name="leads_bulk_visibility"]:checked').val(), i.assigned = void 0 === i.assigned ? "" : i.assigned, i.visibility = void 0 === i.visibility ? "" : i.visibility, "" === i.status && !1 === i.lost && "" === i.assigned && "" === i.source && "" === i.last_contact && 0 == i.tags.length && "" === i.visibility) return
        } else i.mass_delete = !0;
        var n = table_leads.find("tbody tr");
        $.each(n, function() {
            var e = $($(this).find("td").eq(0)).find("input");
            !0 === e.prop("checked") && a.push(e.val())
        }), i.ids = a, $(e).addClass("disabled"), setTimeout(function() {
            $.post(admin_url + "leads/bulk_action", i).done(function() {
                window.location.reload()
            }).fail(function(e) {
                $("#lead-modal").modal("hide"), alert_float("danger", e.responseText)
            })
        }, 200)
    }
}

function init_proposal_editor() {
    tinymce.remove("div.editable");
    var a = [];
    $.each(proposalsTemplates, function(e, t) {
        a.push({
            url: admin_url + "proposals/get_template?name=" + t,
            title: t
        })
    });
    var e = {
        selector: "div.editable",
        inline: !0,
        theme: "inlite",
        relative_urls: !1,
        remove_script_host: !1,
        inline_styles: !0,
        verify_html: !1,
        cleanup: !1,
        apply_source_formatting: !1,
        valid_elements: "+*[*]",
        valid_children: "+body[style], +style[type]",
        file_browser_callback: elFinderBrowser,
        table_default_styles: {
            width: "100%"
        },
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        pagebreak_separator: '<p pagebreak="true"></p>',
        plugins: ["advlist pagebreak autolink autoresize lists link image charmap hr", "searchreplace visualblocks visualchars code", "media nonbreaking table contextmenu", "paste textcolor colorpicker"],
        autoresize_bottom_margin: 50,
        insert_toolbar: "image media quicktable | bullist numlist | h2 h3 | hr",
        selection_toolbar: "save_button bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect h2 h3",
        contextmenu: "image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
        setup: function(e) {
            e.addCommand("mceSave", function() {
                save_proposal_content(!0)
            }), e.addShortcut("Meta+S", "", "mceSave"), e.on("MouseLeave blur", function() {
                tinymce.activeEditor.isDirty() && save_proposal_content()
            }), e.on("MouseDown ContextMenu", function() {
                is_mobile() || $("#small-table").hasClass("hide") || small_table_full_view()
            }), e.on("blur", function() {
                $.Shortcuts.start()
            }), e.on("focus", function() {
                $.Shortcuts.stop()
            })
        }
    };
    is_mobile() && (e.theme = "modern", e.mobile = {}, e.mobile.theme = "mobile", e.mobile.toolbar = _tinymce_mobile_toolbar(), e.inline = !1, window.addEventListener("beforeunload", function(e) {
        tinymce.activeEditor.isDirty() && save_proposal_content()
    })), 0 < a.length && (e.templates = a, e.plugins[3] = "template " + e.plugins[3], e.contextmenu = e.contextmenu.replace("inserttable", "inserttable template")), tinymce.init(e)
}

function add_proposal_comment() {
    var e = $("#comment").val();
    if ("" != e) {
        var t = {};
        t.content = e, t.proposalid = proposal_id, $("body").append('<div class="dt-loader"></div>'), $.post(admin_url + "proposals/add_proposal_comment", t).done(function(e) {
            e = JSON.parse(e), $("body").find(".dt-loader").remove(), 1 == e.success && ($("#comment").val(""), get_proposal_comments())
        })
    }
}

function get_proposal_comments() {
    "undefined" != typeof proposal_id && requestGet("proposals/get_proposal_comments/" + proposal_id).done(function(e) {
        $("body").find("#proposal-comments").html(e)
    })
}

function remove_proposal_comment(t) {
    confirm_delete() && requestGetJSON("proposals/remove_comment/" + t).done(function(e) {
        1 == e.success && $('[data-commentid="' + t + '"]').remove()
    })
}

function edit_proposal_comment(t) {
    var a = $("body").find('[data-proposal-comment-edit-textarea="' + t + '"] textarea').val();
    "" != a && ($.post(admin_url + "proposals/edit_comment/" + t, {
        content: a
    }).done(function(e) {
        1 == (e = JSON.parse(e)).success && (alert_float("success", e.message), $("body").find('[data-proposal-comment="' + t + '"]').html(nl2br(a)))
    }), toggle_proposal_comment_edit(t))
}

function toggle_proposal_comment_edit(e) {
    $("body").find('[data-proposal-comment="' + e + '"]').toggleClass("hide"), $("body").find('[data-proposal-comment-edit-textarea="' + e + '"]').toggleClass("hide")
}

function proposal_convert_template(e) {
    var t, a = $(e).data("template");
    if ("estimate" == a) t = "estimate";
    else {
        if ("invoice" != a) return !1;
        t = "invoice"
    }
    requestGet("proposals/get_" + t + "_convert_data/" + proposal_id).done(function(e) {
        $(".proposal-pipeline-modal").is(":visible") && $(".proposal-pipeline-modal").modal("hide"), $("#convert_helper").html(e), $("#convert_to_" + t).modal({
            show: !0,
            backdrop: "static"
        }), reorder_items()
    })
}

function save_proposal_content(t) {
    var a = tinyMCE.activeEditor,
        e = {};
    e.proposal_id = proposal_id, e.content = a.getContent(), $.post(admin_url + "proposals/save_proposal_data", e).done(function(e) {
        e = JSON.parse(e), void 0 !== t && alert_float("success", e.message), a.save()
    }).fail(function(e) {
        var t = JSON.parse(e.responseText);
        alert_float("danger", t.message)
    })
}

function sync_proposals_data(e, t) {
    var a = {},
        i = $("#sync_data_proposal_data");
    a.country = i.find('select[name="country"]').val(), a.zip = i.find('input[name="zip"]').val(), a.state = i.find('input[name="state"]').val(), a.city = i.find('input[name="city"]').val(), a.address = i.find('textarea[name="address"]').val(), a.phone = i.find('input[name="phone"]').val(), a.rel_id = e, a.rel_type = t, $.post(admin_url + "proposals/sync_data", a).done(function(e) {
        e = JSON.parse(e), alert_float("success", e.message), i.modal("hide")
    })
}

function init_table_announcements(e) {
    if (void 0 === e && $("body").hasClass("dashboard")) return !1;
    initDataTable(".table-announcements", admin_url + "announcements", void 0, void 0, "undefined", [1, "desc"])
}

function init_table_tickets(e) {
    if (void 0 === e && ($("body").hasClass("dashboard") || $("body").hasClass("single-ticket"))) return !1;
    if (0 !== $("body").find(".tickets-table").length) {
        var t = {},
            a = $("._hidden_inputs._filters.tickets_filters input"),
            i = $("table.tickets-table thead .ticket_created_column").index();
        $.each(a, function() {
            t[$(this).attr("name")] = '[name="' + $(this).attr("name") + '"]'
        }), t.project_id = '[name="project_id"]';
        var n = [0],
            s = admin_url + "tickets";
        if ($("body").hasClass("tickets-page") && (s += "?bulk_actions=true"), _table_api = initDataTable(".tickets-table", s, n, n, t, [i, "desc"]), _table_api && $("body").hasClass("dashboard")) {
            var o = [4, i, 5, 6];
            for (var l in o) _table_api.column(o[l]).visible(!1, !1);
            _table_api.columns.adjust()
        }
    }
}

function init_table_staff_projects(e) {
    if (void 0 === e && $("body").hasClass("dashboard")) return !1;
    if (0 !== $("body").find(".table-staff-projects").length) {
        var t = {},
            a = $("._hidden_inputs._filters.staff_projects_filter input");
        $.each(a, function() {
            t[$(this).attr("name")] = '[name="' + $(this).attr("name") + '"]'
        }), initDataTable(".table-staff-projects", admin_url + "projects/staff_projects", "undefined", "undefined", t, [2, "asc"])
    }
}

function do_task_checklist_items_height(e) {
    void 0 === e && (e = $("body").find("textarea[name='checklist-description']")), $.each(e, function() {
        var e = $(this).val();
        $(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth")) && $(this).height(0).height(this.scrollHeight), "" === e && $(this).removeAttr("style")
    })
}

function recalculate_checklist_items_progress() {
    var e = $('input[name="checklist-box"]:checked').length,
        t = $('input[name="checklist-box"]').length,
        a = 0,
        i = $(".task-progress-bar");
    if ($(".task-total-checklist-completed").text(e), 0 == t ? ($("body").find(".chk-heading").remove(), $("#task-no-checklist-items").removeClass("hide")) : $("#task-no-checklist-items").addClass("hide"), !(2 < t)) return i.parents(".progress").addClass("hide"), 0 < e ? $(".chk-toggle-buttons").removeClass("hide") : $(".chk-toggle-buttons").addClass("hide"), !1;
    i.parents(".progress").removeClass("hide"), 0 == (a = 100 * e / t) ? $(".task-progress-bar").addClass("text-dark") : $(".task-progress-bar").removeClass("text-dark"), i.css("width", a.toFixed(2) + "%"), i.text(a.toFixed(2) + "%"), 0 < e ? $(".chk-toggle-buttons").removeClass("hide") : $(".chk-toggle-buttons").addClass("hide"), 100 == a ? i.removeClass("progress-bar-default").addClass("progress-bar-success") : i.removeClass("progress-bar-success").addClass("progress-bar-default")
}

function remove_checklist_item_template(n) {
    requestGetJSON("tasks/remove_checklist_item_template/" + n).done(function(e) {
        if (!0 === e.success || "true" == e.success) {
            var t = $("body").find("select.checklist-items-template-select"),
                i = t.find('option[value="' + n + '"]').html().trim(),
                a = $("#task-modal .checklist");
            $.each(a, function(e, t) {
                var a = $(t);
                a.find('textarea[name="checklist-description"]').val().trim() == i && a.find(".save-checklist-template").removeClass("hide")
            }), t.find('option[value="' + n + '"]').remove(), t.selectpicker("refresh"), 1 === t.find("option").length && (t.selectpicker("destroy"), $(".checklist-templates-wrapper").addClass("hide"))
        }
    })
}

function save_checklist_item_template(e, a) {
    var i = $('.checklist[data-checklist-id="' + e + '"] textarea').val();
    $.post(admin_url + "tasks/save_checklist_item_template", {
        description: i
    }).done(function(e) {
        e = JSON.parse(e), $(a).addClass("hide");
        var t = $(".checklist-templates-wrapper");
        t.find('select option[value=""]').after('<option value="' + e.id + '">' + i.trim() + "</option>"), t.removeClass("hide"), t.find("select").selectpicker("refresh")
    })
}

function update_checklist_order() {
    var e = [],
        t = $("body").find(".checklist");
    if (0 !== t.length) {
        var a = 1;
        $.each(t, function() {
            e.push([$(this).data("checklist-id"), a]), a++
        });
        var i = {};
        i.order = e, $.post(admin_url + "tasks/update_checklist_order", i)
    }
}

function add_task_checklist_item(e, t, a) {
    a && $(a).addClass("disabled"), t = void 0 === t ? "" : t, $.post(admin_url + "tasks/add_checklist_item", {
        taskid: e,
        description: t
    }).done(function() {
        init_tasks_checklist_items(!0, e)
    }).always(function() {
        a && $(a).removeClass("disabled")
    })
}

function update_task_checklist_item(i) {
    var n = $.Deferred();
    return setTimeout(function() {
        var t = i.val();
        t = t.trim();
        var a = i.parents(".checklist").data("checklist-id");
        $.post(admin_url + "tasks/update_checklist_item", {
            description: t,
            listid: a
        }).done(function(e) {
            n.resolve(), !0 === (e = JSON.parse(e)).can_be_template && i.parents(".checklist").find(".save-checklist-template").removeClass("hide"), "" === t && $("#checklist-items").find('.checklist[data-checklist-id="' + a + '"]').remove()
        })
    }, 300), n.promise()
}

function delete_checklist_item(e, t) {
    requestGetJSON("tasks/delete_checklist_item/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || ($(t).parents(".checklist").remove(), recalculate_checklist_items_progress())
    })
}

function init_tasks_checklist_items(a, e) {
    $.post(admin_url + "tasks/init_checklist_items", {
        taskid: e
    }).done(function(e) {
        if ($("#checklist-items").html(e), void 0 !== a) {
            var t = $("#checklist-items").find(".checklist textarea").eq(0);
            "" === t.val() && t.focus()
        }
        recalculate_checklist_items_progress(), update_checklist_order()
    })
}

function _task_attachments_more_and_less_checks() {
    var e = $("body").find(".task_attachments_wrapper"),
        t = e.find(".task-attachment-col"),
        a = $("body").find("#show-more-less-task-attachments-col .task-attachments-more");
    0 === t.length ? e.remove() : 2 == t.length && a.hasClass("hide") ? $("body").find("#show-more-less-task-attachments-col").remove() : 0 !== $(".task_attachments_wrapper .task-attachment-col:visible").length || a.hasClass("hide") || a.click(), $.each($("#task-modal .comment-content"), function() {
        0 === $(this).find(".task-attachment-col").length && $(this).find(".download-all").remove()
    })
}

function remove_task_attachment(e, t) {
    confirm_delete() && requestGetJSON("tasks/remove_task_attachment/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || $('[data-task-attachment-id="' + t + '"]').remove(), _task_attachments_more_and_less_checks(), e.comment_removed && $("#comment_" + e.comment_removed).remove()
    })
}

function add_task_comment(e) {
    var t = {};
    0 < taskCommentAttachmentDropzone.files.length ? taskCommentAttachmentDropzone.processQueue(e) : (tinymce.activeEditor ? t.content = tinyMCE.activeEditor.getContent() : (t.content = $("#task_comment").val(), t.no_editor = !0), t.taskid = e, $.post(admin_url + "tasks/add_task_comment", t).done(function(e) {
        _task_append_html((e = JSON.parse(e)).taskHtml), tinymce.remove("#task_comment")
    }))
}

function remove_task_comment(t) {
    confirm_delete() && requestGetJSON("tasks/remove_comment/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || ($('[data-commentid="' + t + '"]').remove(), $('[data-comment-attachment="' + t + '"]').remove(), _task_attachments_more_and_less_checks())
    })
}

function remove_assignee(e, t) {
    confirm_delete() && requestGetJSON("tasks/remove_assignee/" + e + "/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || (alert_float("success", e.message), _task_append_html(e.taskHtml))
    })
}

function remove_follower(e, t) {
    confirm_delete() && requestGetJSON("tasks/remove_follower/" + e + "/" + t).done(function(e) {
        !0 !== e.success && "true" != e.success || (alert_float("success", e.message), _task_append_html(e.taskHtml))
    })
}

function mark_complete(e) {
    task_mark_as(5, e)
}

function unmark_complete(e) {
    task_mark_as(4, e, "tasks/unmark_complete/" + e)
}

function task_mark_as(t, a, e) {
    e = void 0 === e ? "tasks/mark_as/" + t + "/" + a : e;
    var i = $("#task-modal").is(":visible");
    e += "?single_task=" + i, $("body").append('<div class="dt-loader"></div>'), requestGetJSON(e).done(function(e) {
        $("body").find(".dt-loader").remove(), !0 !== e.success && "true" != e.success || (reload_tasks_tables(), i && _task_append_html(e.taskHtml), 5 == t && "function" == typeof _maybe_remove_task_from_project_milestone && _maybe_remove_task_from_project_milestone(a), 0 === $(".tasks-kanban").length && alert_float("success", e.message))
    })
}

function task_change_priority(e, t) {
    url = "tasks/change_priority/" + e + "/" + t;
    var a = $("#task-modal").is(":visible");
    url += "?single_task=" + a, requestGetJSON(url).done(function(e) {
        !0 !== e.success && "true" != e.success || (reload_tasks_tables(), a && _task_append_html(e.taskHtml))
    })
}

function task_change_milestone(e, t) {
    url = "tasks/change_milestone/" + e + "/" + t;
    var a = $("#task-modal").is(":visible");
    url += "?single_task=" + a, requestGetJSON(url).done(function(e) {
        !0 !== e.success && "true" != e.success || (reload_tasks_tables(), a && _task_append_html(e.taskHtml))
    })
}

function delete_user_unfinished_timesheet(e) {
    confirm_delete() && requestGetJSON("tasks/delete_user_unfinished_timesheet/" + e).done(function(e) {
        _init_timers_top_html(JSON.parse(e.timers)), reload_tasks_tables()
    })
}

function reload_tasks_tables() {
    $.each([".table-tasks", ".table-rel-tasks", ".table-rel-tasks-leads", ".table-timesheets", ".table-timesheets-report"], function(e, t) {
        $.fn.DataTable.isDataTable(t) && $(t).DataTable().ajax.reload(null, !1)
    })
}

function make_task_public(e) {
    requestGetJSON("tasks/make_public/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || (reload_tasks_tables(), _task_append_html(e.taskHtml))
    })
}

function new_task(e, t) {
    e = void 0 !== e ? e : admin_url + "tasks/task";
    var a = $("#lead-modal");
    a.is(":visible") && (-1 === (e += "&opened_from_lead_id=" + a.find('input[name="leadid"]').val()).indexOf("?") && (e = e.replace("&", "?")), a.modal("hide"));
    var i = $("#task-modal");
    i.is(":visible") && i.modal("hide");
    var n = $("#_task_modal");
    n.is(":visible") && n.modal("hide"), requestGet(e).done(function(e) {
        $("#_task").html(e), $("body").find("#_task_modal").modal({
            show: !0,
            backdrop: "static"
        }), $("#timer-select-task").is(":visible") && ($(".system-popup-close").click(), window._timer_id = t)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function showHideTagsPlaceholder(e) {
    var t = e.data("ui-tagit").tagInput,
        a = e.data("ui-tagit").options.placeholderText;
    0 < e.tagit("assignedTags").length ? t.removeAttr("placeholder") : t.attr("placeholder", a)
}

function new_task_from_relation(e, t, a) {
    void 0 === t && void 0 === a && (a = $(e).data("new-rel-id"), t = $(e).data("new-rel-type")), new_task(admin_url + "tasks/task?rel_id=" + a + "&rel_type=" + t)
}

function edit_task(e) {
    requestGet("tasks/task/" + e).done(function(e) {
        $("#_task").html(e), $("#task-modal").modal("hide"), $("body").find("#_task_modal").modal({
            show: !0,
            backdrop: "static"
        })
    })
}

function task_form_handler(e) {
    tinymce.triggerSave(), $("#_task_modal").find('input[name="startdate"]').prop("disabled", !1), $("#_task_modal").find('button[type="submit"]').prop("disabled", !0), $("#_task_modal input[type=file]").each(function() {
        "" === $(this).val() && $(this).prop("disabled", !0)
    });
    var t = e.action,
        a = new FormData($(e)[0]);
    return $.ajax({
        type: $(e).attr("method"),
        data: a,
        mimeType: $(e).attr("enctype"),
        contentType: !1,
        cache: !1,
        processData: !1,
        url: t
    }).done(function(e) {
        if (!0 !== (e = JSON.parse(e)).success && "true" != e.success || alert_float("success", e.message), window._timer_id) return requestGet(admin_url + "/tasks/get_task_by_id/" + e.id).done(function(e) {
            $('[data-timer-id="' + window._timer_id + '"').click();
            var t = '<option value="' + (e = JSON.parse(e)).id + '" title="' + e.name + '" selected>' + e.name + "</option>";
            $("#timer_add_task_id").append(t), $("#timer_add_task_id").trigger("change").data("AjaxBootstrapSelect").list.cache = {}, $("#timer_add_task_id").selectpicker("refresh"), delete window._timer_id
        }), $("#_task_modal").modal("hide"), $("#task-modal").modal("hide"), !1;
        if ($("body").hasClass("project")) {
            var t = window.location.href,
                a = [];
            t = t.split("?");
            var i = get_url_param("group"),
                n = get_url_param("exclude_completed");
            i && (a.group = i), n && (a.exclude_completed = n), a.taskid = e.id, window.location.href = buildUrl(t[0], a)
        } else $("#_task_modal").attr("data-task-created", !0), $("#_task_modal").modal("hide"), init_task_modal(e.id), reload_tasks_tables(), $("body").hasClass("kan-ban-body") && $("body").hasClass("tasks") && tasks_kanban()
    }).fail(function(e) {
        alert_float("danger", JSON.parse(e.responseText))
    }), !1
}

function system_popup(e) {
    e.content = void 0 === e.content ? "" : e.content;
    var a = $("<div/>", {
            id: "system-popup",
            class: "system-popup"
        }).appendTo("body"),
        t = "";
    return t += '<div class="popup-wrapper fadeIn animated">', t += '<h2 class="popup-message">', t += e.message, t += "</h2>", t += '<div class="popup-content">', t += e.content, t += '<button type="button" class="system-popup-close"> </button>', t += "</div>", t += "</div>", a.html(t).removeClass("hide"), $("body").addClass("system-popup"), a.find(".system-popup-close").on("click", function() {
        var t = this;
        requestGet("misc/clear_system_popup").done(function(e) {
            setTimeout(function() {
                $("body").removeClass("system-popup"), a.fadeOut(400, function() {
                    a.remove()
                }), $(t).off("click")
            }, 50)
        })
    }), a
}

function timer_action(e, t, a, i) {
    a = void 0 === a ? "" : a;
    var n = $("#timer-select-task");
    if ("" !== t || !n.is(":visible")) {
        if ("" !== a && "0" == t) {
            var s = {
                content: ""
            };
            return s.content += '<div class="row">', s.content += '<div class="form-group">', "1" == app.options.has_permission_create_task && (s.content += '<div class="input-group" style="margin:0 auto;width:60%;">'), s.content += '<select id="timer_add_task_id" data-empty-title="' + app.lang.search_tasks + '" data-width="60%" class="ajax-search" data-live-search="true">', s.content += "</select>", "1" == app.options.has_permission_create_task && (s.content += '<div class="input-group-addon" style="opacity: 1;">', s.content += '<a href="#" onclick="new_task(\'tasks/task\',' + a + '); return false;"><i class="fa fa-plus"></i></a>', s.content += "</div>"), s.content += "</div></div>", s.content += '<div class="form-group">', s.content += '<textarea id="timesheet_note" placeholder="' + app.lang.note + '" style="margin:0 auto;width:60%;" rows="4" class="form-control"></textarea>', s.content += "</div>", s.content += "<button type='button' onclick='timer_action(this,document.getElementById(\"timer_add_task_id\").value," + a + ");return false;' class='btn btn-info'>" + app.lang.confirm + "</button>", s.message = app.lang.task_stop_timer, system_popup(s).attr("id", "timer-select-task"), init_ajax_search("tasks", "#timer_add_task_id", void 0, admin_url + "tasks/ajax_search_assign_task_to_timer"), !1
        }
        $(e).addClass("disabled");
        var o = {};
        o.task_id = t, o.timer_id = a, o.note = $("body").find("#timesheet_note").val(), o.note || (o.note = "");
        var l = $("#task-modal").is(":visible"),
            d = admin_url + "tasks/timer_tracking?single_task=" + l;
        i && (d += "&admin_stop=" + i), $.post(d, o).done(function(e) {
            e = JSON.parse(e), $("body").hasClass("member") && window.location.reload(), l && _task_append_html(e.taskHtml), n.is(":visible") && n.find(".system-popup-close").click(), _init_timers_top_html(JSON.parse(e.timers)), $(".popover-top-timer-note").popover("hide"), reload_tasks_tables()
        })
    }
}

function init_task_modal(e, t) {
    var a = "",
        i = $("#lead-modal"),
        n = $("#_task_modal");
    i.is(":visible") ? (a += "?opened_from_lead_id=" + i.find('input[name="leadid"]').val(), i.modal("hide")) : null != n.attr("data-lead-id") && (a += "?opened_from_lead_id=" + n.attr("data-lead-id")), requestGet("tasks/get_task_data/" + e + a).done(function(e) {
        _task_append_html(e), void 0 !== t && setTimeout(function() {
            $('[data-task-comment-href-id="' + t + '"]').click()
        }, 1e3)
    }).fail(function(e) {
        $("#task-modal").modal("hide"), alert_float("danger", e.responseText)
    })
}

function _task_append_html(e) {
    var t = $("#task-modal");
    t.find(".data").html(e), recalculate_checklist_items_progress(), do_task_checklist_items_height(), setTimeout(function() {
        t.modal("show"), t.is(":visible") && init_tags_inputs(), init_form_reminder("task"), fix_task_modal_left_col_height(), is_mobile() && init_new_task_comment(!0)
    }, 150)
}

function task_tracking_stats(e) {
    requestGet("tasks/task_tracking_stats/" + e).done(function(e) {
        $("<div/>", {
            id: "tracking-stats"
        }).appendTo("body").html(e), $("#task-tracking-stats-modal").modal("toggle")
    })
}

function init_timers() {
    requestGetJSON("tasks/get_staff_started_timers").done(function(e) {
        _init_timers_top_html(e)
    })
}

function _init_timers_top_html(e) {
    $("#top-timers");
    var t = $("#top-timers").find(".icon-started-timers");
    0 < e.total_timers ? t.removeClass("hide").html(e.total_timers) : t.addClass("hide"), $("#started-timers-top").html(e.html)
}

function edit_task_comment(e) {
    var t = $('[data-edit-comment="' + e + '"]');
    if (t.next().addClass("hide"), t.removeClass("hide"), !is_ios()) {
        tinymce.remove("#task_comment_" + e);
        var a = _simple_editor_config();
        a.auto_focus = "task_comment_" + e, a.setup = function(e) {
            initStickyTinyMceToolbarInModal(e, document.querySelector(".task-modal-single"))
        }, init_editor("#task_comment_" + e, a), tinymce.triggerSave()
    }
}

function cancel_edit_comment(e) {
    var t = $('[data-edit-comment="' + e + '"]');
    tinymce.remove('[data-edit-comment="' + e + '"] textarea'), t.addClass("hide"), t.next().removeClass("hide")
}

function save_edited_comment(t, e) {
    tinymce.triggerSave();
    var a = {};
    a.id = t, a.task_id = e, a.content = $('[data-edit-comment="' + t + '"]').find("textarea").val(), is_ios() && (a.no_editor = !0), $.post(admin_url + "tasks/edit_comment", a).done(function(e) {
        !0 === (e = JSON.parse(e)).success || "true" == e.success ? (alert_float("success", e.message), _task_append_html(e.taskHtml)) : cancel_edit_comment(t), tinymce.remove('[data-edit-comment="' + t + '"] textarea')
    })
}

function fix_task_modal_left_col_height() {
    is_mobile() || $("body").find(".task-single-col-left").css("min-height", $("body").find(".task-single-col-right").outerHeight(!0) + "px")
}

function tasks_kanban_update(e, t) {
    if (t === e.item.parent()[0]) {
        var a = $(e.item.parent()[0]).data("task-status-id"),
            i = $(e.item.parent()[0]).find("[data-task-id]"),
            n = {
                order: []
            },
            s = 0;
        $.each(i, function() {
            n.order.push([$(this).data("task-id"), s]), s++
        }), task_mark_as(a, $(e.item).data("task-id")), check_kanban_empty_col("[data-task-id]"), setTimeout(function() {
            $.post(admin_url + "tasks/update_order", n)
        }, 200)
    }
}

function tasks_kanban() {
    init_kanban("tasks/kanban", tasks_kanban_update, ".tasks-status", 265, 360)
}

function edit_task_inline_description(e, a) {
    tinyMCE.remove("#task_view_description"), $(e).hasClass("editor-initiated") ? $(e).removeClass("editor-initiated") : ($(e).addClass("editor-initiated"), $.Shortcuts.stop(), tinymce.init({
        selector: "#task_view_description",
        theme: "inlite",
        skin: "perfex",
        directionality: "true" == isRTL ? "rtl" : "",
        auto_focus: "task_view_description",
        plugins: "table link paste contextmenu textpattern" + ("true" == isRTL ? " directionality" : ""),
        contextmenu: "link table paste pastetext",
        insert_toolbar: "quicktable",
        selection_toolbar: "bold italic | quicklink h2 h3 blockquote",
        inline: !0,
        table_default_styles: {
            width: "100%"
        },
        setup: function(t) {
            t.on("blur", function(e) {
                t.isDirty() && $.post(admin_url + "tasks/update_task_description/" + a, {
                    description: t.getContent()
                }), setTimeout(function() {
                    t.remove(), $.Shortcuts.start()
                }, 500)
            })
        }
    }))
}

function tasks_bulk_action(e) {
    if (confirm_delete()) {
        var t = [],
            a = {},
            i = $("#mass_delete").prop("checked");
        if (0 == i || void 0 === i) {
            a.status = $("#move_to_status_tasks_bulk_action").val();
            var n = $("#task_bulk_assignees");
            a.assignees = n.length ? n.selectpicker("val") : "";
            var s = $("#tags_bulk");
            a.tags = s.length ? s.tagit("assignedTags") : "";
            var o = $("#task_bulk_milestone");
            if (a.milestone = o.length ? o.selectpicker("val") : "", a.billable = $("#task_bulk_billable").val(), a.billable = void 0 === a.billable ? "" : a.billable, a.priority = $("#task_bulk_priority").val(), a.priority = void 0 === a.priority ? "" : a.priority, "" === a.status && "" === a.priority && "" === a.tags && "" === a.assignees && "" === a.milestone && "" === a.billable) return
        } else a.mass_delete = !0;
        var l = $($("#tasks_bulk_actions").attr("data-table")).find("tbody tr");
        $.each(l, function() {
            var e = $($(this).find("td").eq(0)).find("input");
            !0 === e.prop("checked") && t.push(e.val())
        }), a.ids = t, $(e).addClass("disabled"), setTimeout(function() {
            $.post(admin_url + "tasks/bulk_action", a).done(function() {
                window.location.reload()
            })
        }, 200)
    }
}

function load_small_table_item(e, t, a, i, n) {
    var s = $('input[name="' + a + '"]').val();
    "" === s || window.location.hash ? window.location.hash && !e && (e = window.location.hash.substring(1)) : (e = s, $('input[name="' + a + '"]').val("")), void 0 !== e && "" !== e && (destroy_dynamic_scripts_in_element($(t)), $("body").hasClass("small-table") || toggle_small_view(n, t), $('input[name="' + a + '"]').val(e), do_hash_helper(e), $(t).load(admin_url + i + "/" + e), is_mobile() && $("html, body").animate({
        scrollTop: $(t).offset().top + 150
    }, 600))
}

function init_invoice(e) {
    load_small_table_item(e, "#invoice", "invoiceid", "invoices/get_invoice_data_ajax", ".table-invoices")
}

function init_credit_note(e) {
    load_small_table_item(e, "#credit_note", "credit_note_id", "credit_notes/get_credit_note_data_ajax", ".table-credit-notes")
}

function init_estimate(e) {
    load_small_table_item(e, "#estimate", "estimateid", "estimates/get_estimate_data_ajax", ".table-estimates")
}

function init_proposal(e) {
    load_small_table_item(e, "#proposal", "proposal_id", "proposals/get_proposal_data_ajax", ".table-proposals")
}

function init_expense(e) {
    load_small_table_item(e, "#expense", "expenseid", "expenses/get_expense_data_ajax", ".table-expenses")
}

function clear_billing_and_shipping_details() {
    for (var e in billingAndShippingFields) - 1 < billingAndShippingFields[e].indexOf("country") ? $('select[name="' + billingAndShippingFields[e] + '"]').selectpicker("val", "") : ($('input[name="' + billingAndShippingFields[e] + '"]').val(""), $('textarea[name="' + billingAndShippingFields[e] + '"]').val("")), "billing_country" == billingAndShippingFields[e] && ($('input[name="include_shipping"]').prop("checked", !1), $('input[name="include_shipping"]').change());
    init_billing_and_shipping_details()
}

function init_billing_and_shipping_details() {
    var e, t = $('input[name="include_shipping"]').prop("checked");
    for (var a in billingAndShippingFields) e = "", -1 < billingAndShippingFields[a].indexOf("country") ? e = $("#" + billingAndShippingFields[a] + " option:selected").data("subtext") : -1 < billingAndShippingFields[a].indexOf("shipping_street") || -1 < billingAndShippingFields[a].indexOf("billing_street") ? $('textarea[name="' + billingAndShippingFields[a] + '"]').length && (e = $('textarea[name="' + billingAndShippingFields[a] + '"]').val().replace(/(?:\r\n|\r|\n)/g, "<br />")) : e = $('input[name="' + billingAndShippingFields[a] + '"]').val(), -1 < billingAndShippingFields[a].indexOf("shipping") && (t || (e = "")), void 0 === e && (e = ""), e = "" !== e ? e : "--", $("." + billingAndShippingFields[a]).html(e);
    $("#billing_and_shipping_details").modal("hide")
}

function record_payment(e) {
    void 0 !== e && "" !== e && $("#invoice").load(admin_url + "invoices/record_invoice_payment_ajax/" + e)
}

function schedule_invoice_send(e) {
    $("#invoice").load(admin_url + "email_schedule_invoice/create/" + e)
}

function schedule_estimate_send(e) {
    $("#estimate").load(admin_url + "email_schedule_estimate/create/" + e)
}

function edit_invoice_scheduled_email(e) {
    $("#invoice").load(admin_url + "email_schedule_invoice/edit/" + e)
}

function edit_estimate_scheduled_email(e) {
    $("#estimate").load(admin_url + "email_schedule_estimate/edit/" + e)
}

function add_item_to_preview(e) {
    requestGetJSON("invoice_items/get_item_by_id/" + e).done(function(e) {
        clear_item_preview_values(), $('.main textarea[name="description"]').val(e.description), $('.main textarea[name="long_description"]').val(e.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " ")), _set_item_preview_custom_fields_array(e.custom_fields), $('.main input[name="quantity"]').val(1);
        var t = [];
        e.taxname && e.taxrate && t.push(e.taxname + "|" + e.taxrate), e.taxname_2 && e.taxrate_2 && t.push(e.taxname_2 + "|" + e.taxrate_2), $(".main select.tax").selectpicker("val", t), $('.main input[name="unit"]').val(e.unit);
        var a = $("body").find('.accounting-template select[name="currency"]'),
            i = a.attr("data-base"),
            n = a.find("option:selected").val(),
            s = $('.main input[name="rate"]');
        if (i == n) s.val(e.rate);
        else {
            var o = e["rate_currency_" + n];
            o && 0 !== parseFloat(o) ? s.val(o) : s.val(e.rate)
        }
        $(document).trigger({
            type: "item-added-to-preview",
            item: e,
            item_type: "item"
        })
    })
}

function _set_item_preview_custom_fields_array(e) {
    for (var t = ["input", "number", "date_picker", "date_picker_time", "colorpicker"], a = 0; a < e.length; a++) {
        var i = e[a];
        if (-1 < $.inArray(i.type, t)) $('tr.main td[data-id="' + i.id + '"] input').val(i.value).trigger("change");
        else if ("textarea" == i.type) $('tr.main td[data-id="' + i.id + '"] textarea').val(i.value);
        else if ("select" == i.type || "multiselect" == i.type) {
            if (!empty(i.value)) n = (n = i.value.split(",")).map(function(e) {
                return e.trim()
            }), $('tr.main td[data-id="' + i.id + '"] select').selectpicker("val", n)
        } else if ("checkbox" == i.type) {
            var n;
            if (!empty(i.value)) n = (n = i.value.split(",")).map(function(e) {
                return e.trim()
            }), $.each(n, function(e, t) {
                $('tr.main td[data-id="' + i.id + '"] input[type="checkbox"][value="' + t + '"]').prop("checked", !0)
            })
        }
    }
}

function add_task_to_preview_as_item(a) {
    requestGetJSON("tasks/get_billable_task_data/" + a).done(function(e) {
        e.taxname = $("select.main-tax").selectpicker("val");
        var t = $(".main");
        t.find('textarea[name="description"]').val(e.name), t.find('textarea[name="long_description"]').val(e.description), t.find('input[name="quantity"]').val(e.total_hours), t.find('input[name="rate"]').val(e.hourly_rate), t.find('input[name="unit"]').val(""), $('input[name="task_id"]').val(a), $(document).trigger({
            type: "item-added-to-preview",
            item: e,
            item_type: "task"
        })
    })
}

function clear_item_preview_values(e) {
    var t = $("table.items tbody").find("tr:last-child").find("select").selectpicker("val"),
        a = $(".main");
    a.find("textarea").val(""), a.find('td.custom_field input[type="checkbox"]').prop("checked", !1), a.find("td.custom_field input:not(:checkbox):not(:hidden)").val(""), a.find("td.custom_field select").selectpicker("val", ""), a.find('input[name="quantity"]').val(1), a.find("select.tax").selectpicker("val", t), a.find('input[name="rate"]').val(""), a.find('input[name="unit"]').val(""), $('input[name="task_id"]').val(""), $('input[name="expense_id"]').val("")
}

function add_item_to_table(n, s, e, t) {
    if ("" !== (n = void 0 === n || "undefined" == n ? get_item_preview_values() : n).description || "" !== n.long_description || "" !== n.rate) {
        var p = "",
            _ = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find("tbody .item").length + 1;
        lastAddedItemKey = _, p += '<tr class="sortable item" data-merge-invoice="' + e + '" data-bill-expense="' + t + '">', p += '<td class="dragger">', isNaN(n.qty) && (n.qty = 1), ("" === n.rate || isNaN(n.rate)) && (n.rate = 0);
        var o = n.rate * n.qty,
            a = "newitems[" + _ + "][taxname][]";
        $("body").append('<div class="dt-loader"></div>');
        var l = /<br[^>]*>/gi;
        return get_taxes_dropdown_template(a, n.taxname).done(function(e) {
            p += '<input type="hidden" class="order" name="newitems[' + _ + '][order]">', p += "</td>", p += '<td class="bold description"><textarea name="newitems[' + _ + '][description]" class="form-control" rows="5">' + n.description + "</textarea></td>", p += '<td><textarea name="newitems[' + _ + '][long_description]" class="form-control item_long_description" rows="5">' + n.long_description.replace(l, "\n") + "</textarea></td>";
            var t = $("tr.main td.custom_field"),
                c = !1;
            0 < t.length && $.each(t, function() {
                var a = $(this).clone(),
                    e = "",
                    t = $(this).find("[data-fieldid]"),
                    i = "newitems[" + _ + "][custom_fields][items][" + t.attr("data-fieldid") + "]";
                if (t.is(":checkbox")) {
                    var n = $(this).find('input[type="checkbox"]:checked'),
                        s = a.find('input[type="checkbox"]');
                    $.each(s, function(e, t) {
                        var a = Math.random().toString(20).slice(2);
                        $(this).attr("id", a).attr("name", i).next("label").attr("for", a), "1" == $(this).attr("data-custom-field-required") && (c = !0)
                    }), $.each(n, function(e, t) {
                        a.find('input[value="' + $(t).val() + '"]').attr("checked", !0)
                    }), e = a.html()
                } else if (t.is("input") || t.is("textarea")) t.is("input") ? a.find("[data-fieldid]").attr("value", t.val()) : a.find("[data-fieldid]").html(t.val()), a.find("[data-fieldid]").attr("name", i), "1" == a.find("[data-fieldid]").attr("data-custom-field-required") && (c = !0), e = a.html();
                else if (t.is("select")) {
                    "1" == $(this).attr("data-custom-field-required") && (c = !0);
                    var o = $(this).find("select[data-fieldid]").selectpicker("val");
                    o = (o = new Array(o))[0].constructor === Array ? o[0] : o;
                    var l = a.find("select"),
                        d = $("<div/>");
                    l.attr("name", i);
                    var r = l.clone();
                    d.append(r), $.each(o, function(e, t) {
                        d.find('select option[value="' + t + '"]').attr("selected", !0)
                    }), e = d.html()
                }
                p += '<td class="custom_field">' + e + "</td>"
            }), p += '<td style="display: none;"><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="newitems[' + _ + '][qty]" value="' + n.qty + '" class="form-control">', n.unit && void 0 !== n.unit || (n.unit = ""), p += '<input type="text" placeholder="' + app.lang.unit + '" name="newitems[' + _ + '][unit]" class="form-control input-transparent text-right" value="' + n.unit + '">', p += "</td>", p += '<td class="rate"><input type="number" data-toggle="tooltip" title="' + app.lang.item_field_not_formatted + '" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + _ + '][rate]" value="' + n.rate + '" class="form-control"></td>', p += '<td class="taxrate">' + e + "</td>", p += '<td class="amount" align="right">' + format_money(o, !0) + "</td>", p += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_item(this,' + s + '); return false;"><i class="fa fa-trash"></i></a></td>', p += "</tr>", $("table.items tbody").append(p), $(document).trigger({
                type: "item-added-to-table",
                data: n,
                row: p
            }), setTimeout(function() {
                calculate_total()
            }, 15);
            var a = $('input[name="task_id"]').val(),
                i = $('input[name="expense_id"]').val();
            return "" !== a && void 0 !== a && (billed_tasks = a.split(","), $.each(billed_tasks, function(e, t) {
                $("#billed-tasks").append(hidden_input("billed_tasks[" + _ + "][]", t))
            })), "" !== i && void 0 !== i && (billed_expenses = i.split(","), $.each(billed_expenses, function(e, t) {
                $("#billed-expenses").append(hidden_input("billed_expenses[" + _ + "][]", t))
            })), $("#item_select").hasClass("ajax-search") && "" !== $("#item_select").selectpicker("val") && $("#item_select").prepend("<option></option>"), init_selectpicker(), init_datepicker(), init_color_pickers(), clear_item_preview_values(), reorder_items(), $("body").find("#items-warning").remove(), $("body").find(".dt-loader").remove(), $("#item_select").selectpicker("val", ""), c && $(".invoice-form").length ? validate_invoice_form() : c && $(".estimate-form").length ? validate_estimate_form() : c && $(".proposal-form").length ? validate_proposal_form() : c && $(".credit-note-form").length && validate_credit_note_form(), $('select[name="task_select"]').find('[value="' + a + '"]').remove(), $('select[name="task_select"]').selectpicker("refresh"), !0
        }), !1
    }
}

function get_taxes_dropdown_template(e, t) {
    jQuery.ajaxSetup({
        async: !1
    });
    var a = $.post(admin_url + "misc/get_taxes_dropdown_template/", {
        name: e,
        taxname: t
    });
    return jQuery.ajaxSetup({
        async: !0
    }), a
}

function deselect_ajax_search(e) {
    var t = $("select#" + $(e).attr("data-id"));
    t.data("AjaxBootstrapSelect").list.cache = {};
    var a = t.parents(".bootstrap-select");
    t.html("").append('<option value=""></option>').selectpicker("val", "multiple" == t.attr("multiple") ? [] : ""), a.removeClass("ajax-remove-values-option").find(".ajax-clear-values").remove(), setTimeout(function() {
        t.trigger("selected.cleared.ajax.bootstrap.select", e), t.trigger("change").data("AjaxBootstrapSelect").list.cache = {}
    }, 50)
}

function init_ajax_project_search_by_customer_id(e) {
    init_ajax_search("project", e = void 0 === e ? "#project_id.ajax-search" : e, {
        customer_id: function() {
            return $("#clientid").val()
        }
    })
}

function init_ajax_projects_search(e) {
    init_ajax_search("project", e = void 0 === e ? "#project_id.ajax-search" : e)
}

function init_items_sortable(e) {
    var t = $("#wrapper").find(".items tbody");
    0 !== t.length && t.sortable({
        helper: fixHelperTableHelperSortable,
        handle: ".dragger",
        placeholder: "ui-placeholder",
        itemPath: "> tbody",
        itemSelector: "tr.sortable",
        items: "tr.sortable",
        update: function() {
            void 0 === e ? reorder_items() : save_ei_items_order()
        },
        sort: function(e, t) {
            var a = $(e.target);
            if (!/html|body/i.test(a.offsetParent()[0].tagName)) {
                var i = e.pageY - a.offsetParent().offset().top - t.helper.outerHeight(!0) / 2;
                t.helper.css({
                    top: i + "px"
                })
            }
        }
    })
}

function save_ei_items_order() {
    var e = $(".table.items-preview"),
        t = e.find("tbody tr"),
        a = 1,
        i = e.attr("data-type"),
        n = [];
    if (!i) return !1;
    $.each(t, function() {
        n.push([$(this).data("item-id"), a]), $(this).find("td.item_no").html(a), a++
    }), setTimeout(function() {
        $.post(admin_url + "misc/update_ei_items_order/" + i, {
            data: n
        })
    }, 200)
}

function reorder_items() {
    var e = $(".table.has-calculations tbody tr.item"),
        t = 1;
    $.each(e, function() {
        $(this).find("input.order").val(t), t++
    })
}

function get_item_preview_values() {
    var e = {};
    return e.description = $('.main textarea[name="description"]').val(), e.long_description = $('.main textarea[name="long_description"]').val(), e.qty = $('.main input[name="quantity"]').val(), e.taxname = $(".main select.tax").selectpicker("val"), e.rate = $('.main input[name="rate"]').val(), e.unit = $('.main input[name="unit"]').val(), e
}

function calculate_total() {
    if ($("body").hasClass("no-calculate-total")) return !1;
    var a, i, e, n, s, o, l = {},
        d = 0,
        r = 0,
        t = 1,
        c = 0,
        p = $(".table.has-calculations tbody tr.item"),
        _ = $("#discount_area"),
        m = $('input[name="adjustment"]').val(),
        u = $('input[name="discount_percent"]').val(),
        f = $('input[name="discount_total"]').val(),
        h = $(".discount-total-type.selected"),
        v = $('select[name="discount_type"]').val();
    $(".tax-area").remove(), $.each(p, function() {
        "" === (t = $(this).find("[data-quantity]").val()) && (t = 1, $(this).find("[data-quantity]").val(1)), s = accounting.toFixed($(this).find("td.rate input").val() * t, app.options.decimal_places), s = parseFloat(s), $(this).find("td.amount").html(format_money(s, !0)), d += s, n = $(this), (e = $(this).find("select.tax").selectpicker("val")) && $.each(e, function(e, t) {
            i = n.find('select.tax [value="' + t + '"]').data("taxrate"), a = s / 100 * i, l.hasOwnProperty(t) ? l[t] = l[t] += a : 0 != i && (o = t.split("|"), tax_row = '<tr class="tax-area"><td>' + o[0] + "(" + i + '%)</td><td id="tax_id_' + slugify(t) + '"></td></tr>', $(_).after(tax_row), l[t] = a)
        })
    }), "" !== u && 0 != u && "before_tax" == v && h.hasClass("discount-type-percent") ? c = d * u / 100 : "" !== f && 0 != f && "before_tax" == v && h.hasClass("discount-type-fixed") && (c = f), $.each(l, function(e, t) {
        if ("" !== u && 0 != u && "before_tax" == v && h.hasClass("discount-type-percent")) total_tax_calculated = t * u / 100, t -= total_tax_calculated;
        else if ("" !== f && 0 != f && "before_tax" == v && h.hasClass("discount-type-fixed")) {
            t -= t * (f / d * 100) / 100
        }
        r += t, t = format_money(t), $("#tax_id_" + slugify(e)).html(t)
    }), r += d, "" !== u && 0 != u && "after_tax" == v && h.hasClass("discount-type-percent") ? c = r * u / 100 : "" !== f && 0 != f && "after_tax" == v && h.hasClass("discount-type-fixed") && (c = f), r -= c, m = parseFloat(m), isNaN(m) || (r += m);
    var b = "-" + format_money(c);
    $('input[name="discount_total"]').val(accounting.toFixed(c, app.options.decimal_places)), $(".discount-total").html(b), $(".adjustment").html(format_money(m)), $(".subtotal").html(format_money(d) + hidden_input("subtotal", accounting.toFixed(d, app.options.decimal_places))), $(".total").html(format_money(r) + hidden_input("total", accounting.toFixed(r, app.options.decimal_places))), $(document).trigger("sales-total-calculated")
    
    $(".tax-area").hide();
}

function exclude_tax_from_amount(e, t) {
    return totalTax = accounting.toFixed(t * e / (100 + e), app.options.decimal_places), accounting.toFixed(t - totalTax, app.options.decimal_places)
}

function delete_item(e, t) {
    $(e).parents("tr").addClass("animated fadeOut", function() {
        setTimeout(function() {
            $(e).parents("tr").remove(), calculate_total()
        }, 50)
    }), t && 0 < $('input[name="isedit"]').length && $("#removed-items").append(hidden_input("removed_items[]", t))
}

function format_money(e, t) {
    return void 0 !== t && t ? accounting.formatMoney(e, {
        symbol: ""
    }) : accounting.formatMoney(e)
}

function init_currency(e, t) {
    var a = $("body").find(".accounting-template");
    (a.length || e) && requestGetJSON("misc/get_currency/" + (e || a.find('select[name="currency"]').val())).done(function(e) {
        accounting.settings.currency.decimal = e.decimal_separator, accounting.settings.currency.thousand = e.thousand_separator, accounting.settings.currency.symbol = e.symbol, accounting.settings.currency.format = "after" == e.placement ? "%v %s" : "%s%v", calculate_total(), t && t()
    })
}

function delete_invoice_attachment(t) {
    confirm_delete() && requestGet("invoices/delete_attachment/" + t).done(function(e) {
        1 == e && ($("body").find('[data-attachment-id="' + t + '"]').remove(), init_invoice($("body").find('input[name="_attachment_sale_id"]').val()))
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function delete_credit_note_attachment(t) {
    confirm_delete() && requestGet("credit_notes/delete_attachment/" + t).done(function(e) {
        1 == e && ($("body").find('[data-attachment-id="' + t + '"]').remove(), init_credit_note($("body").find('input[name="_attachment_sale_id"]').val()))
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function delete_estimate_attachment(a) {
    confirm_delete() && requestGet("estimates/delete_attachment/" + a).done(function(e) {
        if (1 == e) {
            $("body").find('[data-attachment-id="' + a + '"]').remove();
            var t = $("body").find('input[name="_attachment_sale_id"]').val();
            $("body").hasClass("estimates-pipeline") ? estimate_pipeline_open(t) : init_estimate(t)
        }
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function delete_proposal_attachment(a) {
    confirm_delete() && requestGet("proposals/delete_attachment/" + a).done(function(e) {
        if (1 == e) {
            var t = $("body").find('input[name="_attachment_sale_id"]').val();
            $("body").find('[data-attachment-id="' + a + '"]').remove(), $("body").hasClass("proposals-pipeline") ? proposal_pipeline_open(t) : init_proposal(t)
        }
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
}

function init_invoices_total(e) {
    if (0 !== $("#invoices_total").length) {
        var t = $(".invoices-total-inline"),
            a = $(".invoices-total");
        if (!$("body").hasClass("invoices-total-manual") || void 0 !== e || a.hasClass("initialized"))
            if (0 < t.length && a.hasClass("initialized")) t.removeClass("invoices-total-inline");
            else {
                a.addClass("initialized");
                var i = $("body").find('select[name="invoices_total_years"]').selectpicker("val"),
                    n = [];
                $.each(i, function(e, t) {
                    "" !== t && n.push(t)
                });
                var s = {
                        currency: $("body").find('select[name="total_currency"]').val(),
                        years: n,
                        init_total: !0
                    },
                    o = $('input[name="project_id"]').val(),
                    l = $('.customer_profile input[name="userid"]').val();
                void 0 !== o ? s.project_id = o : void 0 !== l && (s.customer_id = l), $.post(admin_url + "invoices/get_invoices_total", s).done(function(e) {
                    $("#invoices_total").html(e)
                })
            }
    }
}

function init_estimates_total(e) {
    if (0 !== $("#estimates_total").length) {
        var t = $(".estimates-total");
        if (!$("body").hasClass("estimates-total-manual") || void 0 !== e || t.hasClass("initialized")) {
            t.addClass("initialized");
            var a = $("body").find('select[name="total_currency"]').val(),
                i = $("body").find('select[name="estimates_total_years"]').selectpicker("val"),
                n = [];
            $.each(i, function(e, t) {
                "" !== t && n.push(t)
            });
            var s = "",
                o = "",
                l = $('.customer_profile input[name="userid"]').val(),
                d = $('input[name="project_id"]').val();
            void 0 !== l ? s = l : void 0 !== d && (o = d), $.post(admin_url + "estimates/get_estimates_total", {
                currency: a,
                init_total: !0,
                years: n,
                customer_id: s,
                project_id: o
            }).done(function(e) {
                $("#estimates_total").html(e)
            })
        }
    }
}

function init_expenses_total() {
    if (0 !== $("#expenses_total").length) {
        var e = $("body").find('select[name="expenses_total_currency"]').val(),
            t = $("body").find('select[name="expenses_total_years"]').selectpicker("val"),
            a = [];
        $.each(t, function(e, t) {
            "" !== t && a.push(t)
        });
        var i = "",
            n = $('.customer_profile input[name="userid"]').val();
        void 0 !== i && (i = n);
        var s = "",
            o = $('input[name="project_id"]').val();
        void 0 !== s && (s = o), $.post(admin_url + "expenses/get_expenses_total", {
            currency: e,
            init_total: !0,
            years: a,
            customer_id: i,
            project_id: s
        }).done(function(e) {
            $("#expenses_total").html(e)
        })
    }
}

function validate_invoice_form(e) {
    e = void 0 === e ? "#invoice-form" : e, appValidateForm($(e), {
        clientid: {
            required: {
                depends: function() {
                    return !$("select#clientid").hasClass("customer-removed")
                }
            }
        },
        date: "required",
        currency: "required",
        repeat_every_custom: {
            min: 1
        },
        number: {
            required: !0
        }
    }), $("body").find('input[name="number"]').rules("add", {
        remote: {
            url: admin_url + "invoices/validate_invoice_number",
            type: "post",
            data: {
                number: function() {
                    return $('input[name="number"]').val()
                },
                isedit: function() {
                    return $('input[name="number"]').data("isedit")
                },
                original_number: function() {
                    return $('input[name="number"]').data("original-number")
                },
                date: function() {
                    return $('input[name="date"]').val()
                }
            }
        },
        messages: {
            remote: app.lang.invoice_number_exists
        }
    })
}

function validate_credit_note_form(e) {
    e = void 0 === e ? "#credit-note-form" : e, appValidateForm($(e), {
        clientid: {
            required: {
                depends: function() {
                    return !$("select#clientid").hasClass("customer-removed")
                }
            }
        },
        date: "required",
        currency: "required",
        number: {
            required: !0
        }
    }), $("body").find('input[name="number"]').rules("add", {
        remote: {
            url: admin_url + "credit_notes/validate_number",
            type: "post",
            data: {
                number: function() {
                    return $('input[name="number"]').val()
                },
                isedit: function() {
                    return $('input[name="number"]').data("isedit")
                },
                original_number: function() {
                    return $('input[name="number"]').data("original-number")
                },
                date: function() {
                    return $(".credit_note input[name='date']").val()
                }
            }
        },
        messages: {
            remote: app.lang.credit_note_number_exists
        }
    })
}

function validate_estimate_form(e) {
    e = void 0 === e ? "#estimate-form" : e, appValidateForm($(e), {
        clientid: {
            required: {
                depends: function() {
                    return !$("select#clientid").hasClass("customer-removed")
                }
            }
        },
        date: "required",
        currency: "required",
        number: {
            required: !0
        }
    }), $("body").find('input[name="number"]').rules("add", {
        remote: {
            url: admin_url + "estimates/validate_estimate_number",
            type: "post",
            data: {
                number: function() {
                    return $('input[name="number"]').val()
                },
                isedit: function() {
                    return $('input[name="number"]').data("isedit")
                },
                original_number: function() {
                    return $('input[name="number"]').data("original-number")
                },
                date: function() {
                    return $("body").find('.estimate input[name="date"]').val()
                }
            }
        },
        messages: {
            remote: app.lang.estimate_number_exists
        }
    })
}

function estimates_pipeline_sort(e) {
    kan_ban_sort(e, estimate_pipeline)
}

function proposal_pipeline_sort(e) {
    kan_ban_sort(e, proposals_pipeline)
}

function estimate_pipeline() {
    init_kanban("estimates/get_pipeline", estimates_pipeline_update, ".pipeline-status", 347, 360)
}

function estimates_pipeline_update(e, t) {
    if (t === e.item.parent()[0]) {
        var a = {};
        a.estimateid = $(e.item).data("estimate-id"), a.status = $(e.item.parent()[0]).data("status-id");
        var i = [],
            n = $(e.item).parents(".pipeline-status").find("li"),
            s = 1;
        $.each(n, function() {
            i.push([$(this).data("estimate-id"), s]), s++
        }), a.order = i, check_kanban_empty_col("[data-estimate-id]"), $.post(admin_url + "estimates/update_pipeline", a)
    }
}

function proposals_pipeline_update(e, t) {
    if (t === e.item.parent()[0]) {
        var a = {
            order: []
        };
        a.proposalid = $(e.item).data("proposal-id"), a.status = $(e.item.parent()[0]).data("status-id");
        var i = $(e.item).parents(".pipeline-status").find("li"),
            n = 1;
        $.each(i, function() {
            a.order.push([$(this).data("proposal-id"), n]), n++
        }), check_kanban_empty_col("[data-proposal-id]"), $.post(admin_url + "proposals/update_pipeline", a)
    }
}

function proposals_pipeline() {
    init_kanban("proposals/get_pipeline", proposals_pipeline_update, ".pipeline-status", 347, 360)
}

function proposal_pipeline_open(e) {
    "" !== e && requestGet("proposals/pipeline_open/" + e).done(function(e) {
        var t = 0 < $(".proposal-pipeline-modal:visible").length;
        $("#proposal").html(e), t ? $("#proposal").find(".modal.proposal-pipeline-modal").removeClass("fade").addClass("in").css("display", "block") : $(".proposal-pipeline-modal").modal({
            show: !0,
            backdrop: "static",
            keyboard: !1
        })
    })
}

function estimate_pipeline_open(e) {
    "" !== e && requestGet("estimates/pipeline_open/" + e).done(function(e) {
        var t = 0 < $(".estimate-pipeline:visible").length;
        $("#estimate").html(e), t ? $("#estimate").find(".modal.estimate-pipeline").removeClass("fade").addClass("in").css("display", "block") : $(".estimate-pipeline").modal({
            show: !0,
            backdrop: "static",
            keyboard: !1
        })
    })
}

function delete_sales_note(n, e) {
    confirm_delete() && requestGetJSON("misc/delete_note/" + e).done(function(e) {
        if (!0 === e.success || "true" == e.success) {
            $(n).parents(".sales-note-wrapper").remove();
            var t = $("#sales-notes-wrapper"),
                a = t.attr("data-total") - 1,
                i = $(".notes-total");
            t.attr("data-total", a), a <= 0 ? i.addClass("hide") : i.html('<span class="badge">' + a + "</span>")
        }
    })
}

function get_sales_notes(e, t) {
    requestGet(t + "/get_notes/" + e).done(function(e) {
        $("#sales_notes_area").html(e);
        var t = $("#sales-notes-wrapper").attr("data-total");
        0 < t && $(".notes-total").html('<span class="badge">' + t + "</span>").removeClass("hide")
    })
}

function insert_proposal_merge_field(e) {
    tinymce.activeEditor.execCommand("mceInsertContent", !1, $(e).text())
}

function small_table_full_view() {
    $("#small-table").toggleClass("hide"), $(".small-table-right-col").toggleClass("col-md-12 col-md-7"), $(window).trigger("resize")
}

function save_sales_number_settings(e) {
    var t = {};
    t.prefix = $("body").find('input[name="s_prefix"]').val(), $.post($(e).data("url"), t).done(function(e) {
        (e = JSON.parse(e)).success && e.message && (alert_float("success", e.message), $("#prefix").html(t.prefix))
    })
}

function do_prefix_year(e) {
    var n = _split_formatted_date_by_separator(e);
    void 0 !== n && $.each(n, function(e, t) {
        if (4 == t.length) {
            var a = $("#prefix_year");
            if (a.hasClass("format-n-yy")) t = t.substr(-2);
            else if (a.hasClass("format-mm-yyyy")) {
                var i;
                "d-m-Y" == app.options.date_format || "d/m/Y" == app.options.date_format || "Y-m-d" == app.options.date_format || "d.m.Y" == app.options.date_format ? i = 1 : "m-d-Y" != app.options.date_format && "m.d.Y" != app.options.date_format && "m/d/Y" != app.options.date_format || (i = 0), $("#prefix_month").html(n[i])
            }
            a.html(t)
        }
    })
}

function unformat_date(e) {
    var t = _split_formatted_date_by_separator(e),
        a = 1,
        i = 0,
        n = 2;
    return "d-m-Y" == app.options.date_format || "d/m/Y" == app.options.date_format || "d.m.Y" == app.options.date_format ? (n = 0, a = 1, i = 2) : "m-d-Y" != app.options.date_format && "m.d.Y" != app.options.date_format && "m/d/Y" != app.options.date_format || (n = 1, a = 0, i = 2), t[i] + "-" + t[a] + "-" + t[n]
}

function _split_formatted_date_by_separator(e) {
    var t;
    return -1 < e.indexOf(".") ? t = e.split(".") : -1 < e.indexOf("-") ? t = e.split("-") : -1 < e.indexOf("/") && (t = e.split("/")), t
}

function init_tabs_scrollable() {
    "true" != isRTL ? ($(window).width() <= 768 && $("body").find(".toggle_view").remove(), $(".horizontal-scrollable-tabs").horizontalTabs()) : ($(".arrow-left, .arrow-right").css("display", "none"), $(".horizontal-scrollable-tabs").removeClass("horizontal-scrollable-tabs"), $(".nav-tabs-horizontal").removeClass("nav-tabs-horizontal"))
}

function view_contact_consent(e) {
    requestGet("clients/consents/" + e).done(function(e) {
        $("#consent_data").html(e), initDataTableInline($("#consentHistoryTable")), $("#consentModal").modal("show")
    })
}

function view_lead_consent(e) {
    window.location.hash = "gdpr", init_lead(e)
}

function set_notification_read_inline(t) {
    requestGet("misc/set_notification_read_inline/" + t).done(function() {
        var e = $("body").find('.notification-wrapper[data-notification-id="' + t + '"]');
        e.find(".notification-box,.notification-box-all").removeClass("unread"), e.find(".not-mark-as-read-inline").tooltip("destroy").remove()
    })
}

function mark_all_notifications_as_read_inline() {
    requestGet("misc/mark_all_notifications_as_read_inline/").done(function() {
        var e = $("body").find(".notification-wrapper");
        e.find(".notification-box,.notification-box-all").removeClass("unread"), e.find(".not-mark-as-read-inline").tooltip("destroy").remove()
    })
}

function delete_sale_activity(e) {
    confirm_delete() && requestGet("misc/delete_sale_activity/" + e).done(function() {
        $("body").find('[data-sale-activity-id="' + e + '"]').remove()
    })
}

function view_event(e) {
    void 0 !== e && $.post(admin_url + "utilities/view_event/" + e).done(function(e) {
        $("#event").html(e), $("#viewEvent").modal("show"), init_datepicker(), init_selectpicker(), validate_calendar_form()
    })
}

function delete_event(e) {
    confirm_delete() && requestGetJSON("utilities/delete_event/" + e).done(function(e) {
        !0 !== e.success && "true" != e.success || window.location.reload()
    })
}

function validate_calendar_form() {
    appValidateForm($("body").find("._event form"), {
        title: "required",
        start: "required",
        reminder_before: "required"
    }, calendar_form_handler), appValidateForm($("body").find("#viewEvent form"), {
        title: "required",
        start: "required",
        reminder_before: "required"
    }, calendar_form_handler)
}

function calendar_form_handler(e) {
    return $.post(e.action, $(e).serialize()).done(function(e) {
        !0 !== (e = JSON.parse(e)).success && "true" != e.success || (alert_float("success", e.message), setTimeout(function() {
            var e = window.location.href;
            e = e.split("?"), window.location.href = e[0]
        }, 500))
    }), !1
}

function fetch_notifications(e) {
    requestGetJSON("misc/notifications_check").done(function(e) {
        var n = notifications_wrapper;
        n.html(e.html);
        var t = n.find("ul.notifications").attr("data-total-unread");
        document.title = 0 < t ? "(" + t + ") " + doc_initial_title : doc_initial_title;
        var a = e.notificationsIds;
        if ("firefox" == app.browser && 1 < a.length) {
            var i = a[0];
            (a = []).push(i)
        }
        setTimeout(function() {
            0 < a.length && $.each(a, function(e, i) {
                var t = 'li[data-notification-id="' + i + '"]',
                    a = n.find(t);
                $.notify("", {
                    title: app.lang.new_notification,
                    body: a.find(".notification-title").text(),
                    requireInteraction: !0,
                    icon: a.find(".notification-image").attr("src"),
                    tag: i,
                    closeTime: "0" != app.options.dismiss_desktop_not_after ? 1e3 * app.options.dismiss_desktop_not_after : null
                }).close(function() {
                    requestGet("misc/set_desktop_notification_read/" + i).done(function(e) {
                        var t = n.find(".icon-total-indicator");
                        n.find('li[data-notification-id="' + i + '"] .notification-box').removeClass("unread");
                        var a = t.text();
                        a = a.trim(), 0 < (a -= 1) ? (document.title = "(" + a + ") " + doc_initial_title, t.html(a)) : (document.title = doc_initial_title, t.addClass("hide"))
                    })
                }).click(function(e) {
                    parent.focus(), window.focus(), setTimeout(function() {
                        n.find(t + " .notification-link").addClass("desktopClick").click(), e.target.close()
                    }, 70)
                })
            })
        }, 10)
    })
}

function init_new_task_comment(e) {
    tinymce.editors.task_comment && tinymce.remove("#task_comment"), "undefined" != typeof taskCommentAttachmentDropzone && taskCommentAttachmentDropzone.destroy(), $("#dropzoneTaskComment").removeClass("hide"), $("#addTaskCommentBtn").removeClass("hide"), taskCommentAttachmentDropzone = new Dropzone("#task-comment-form", appCreateDropzoneOptions({
        uploadMultiple: !0,
        clickable: "#dropzoneTaskComment",
        previewsContainer: ".dropzone-task-comment-previews",
        autoProcessQueue: !1,
        addRemoveLinks: !0,
        parallelUploads: 20,
        maxFiles: 20,
        paramName: "file",
        sending: function(e, t, a) {
            a.append("taskid", $("#addTaskCommentBtn").attr("data-comment-task-id")), tinyMCE.activeEditor ? a.append("content", tinyMCE.activeEditor.getContent()) : a.append("content", $("#task_comment").val())
        },
        success: function(e, t) {
            t = JSON.parse(t), 0 === this.getUploadingFiles().length && 0 === this.getQueuedFiles().length && (_task_append_html(t.taskHtml), tinymce.remove("#task_comment"))
        }
    }));
    var t = _simple_editor_config();
    void 0 !== e && !1 !== e || (t.auto_focus = !0);
    var a = is_ios(),
        i = $("#task-modal #taskid").val();
    t.plugins[0] += " mention", t.content_style = "span.mention {        background-color: #eeeeee;        padding: 3px;    }", t.setup = function(e) {
        initStickyTinyMceToolbarInModal(e, document.querySelector(".task-modal-single")), e.on("init", function() {
            0 === $("#mention-autocomplete-css").length && $("<link>").appendTo("head").attr({
                id: "mention-autocomplete-css",
                type: "text/css",
                rel: "stylesheet",
                href: site_url + "assets/plugins/tinymce/plugins/mention/autocomplete.css"
            }), 0 === $("#mention-css").length && $("<link>").appendTo("head").attr({
                type: "text/css",
                id: "mention-css",
                rel: "stylesheet",
                href: site_url + "assets/plugins/tinymce/plugins/mention/rte-content.css"
            })
        })
    };
    var n = [];
    t.mentions = {
        source: function(e, t, a) {
            n.length < 1 ? $.getJSON(admin_url + "tasks/get_staff_names_for_mentions/" + i, function(e) {
                t(n = e)
            }) : t(n)
        },
        insert: function(e) {
            return '<span class="mention" contenteditable="false" data-mention-id="' + e.id + '">@' + e.name + "</span>&nbsp;"
        }
    }, a || init_editor("#task_comment", t)
}

function init_ajax_search(t, e, a, i) {
    var n = $("body").find(e);
    if (n.length) {
        var s = {
            ajax: {
                url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                data: function() {
                    var e = {};
                    return e.type = t, e.rel_id = "", e.q = "{{{q}}}", void 0 !== a && jQuery.extend(e, a), e
                }
            },
            locale: {
                emptyTitle: app.lang.search_ajax_empty,
                statusInitialized: app.lang.search_ajax_initialized,
                statusSearching: app.lang.search_ajax_searching,
                statusNoResults: app.lang.not_results_found,
                searchPlaceholder: app.lang.search_ajax_placeholder,
                currentlySelected: app.lang.currently_selected
            },
            requestDelay: 500,
            cache: !1,
            preprocessData: function(e) {
                for (var t = [], a = e.length, i = 0; i < a; i++) {
                    var n = {
                        value: e[i].id,
                        text: e[i].name
                    };
                    e[i].subtext && (n.data = {
                        subtext: e[i].subtext
                    }), t.push(n)
                }
                return t
            },
            preserveSelectedPosition: "after",
            preserveSelected: !0
        };
        n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s)
    }
}

function merge_field_format_url(e, t, a, i) {
    return e && -1 < e.indexOf("%7B") && -1 < e.indexOf("%7D") && (e = e.replaceAll("%7B", "{").replaceAll("%7D", "}")), e
}

function salesGoogleDriveSave(e) {
    salesExtenalFileUpload(e, "gdrive")
}

function leadExternalFileUpload(e, t, a) {
    $.post(admin_url + "leads/add_external_attachment", {
        files: e,
        lead_id: a,
        external: t
    }).done(function() {
        init_lead_modal_data(a)
    })
}

function taskExternalFileUpload(e, t, a) {
    $.post(admin_url + "tasks/add_external_attachment", {
        files: e,
        task_id: a,
        external: t
    }).done(function() {
        init_task_modal(a)
    })
}

function salesExtenalFileUpload(e, t) {
    var a = {};
    a.rel_id = $("body").find('input[name="_attachment_sale_id"]').val(), a.type = $("body").find('input[name="_attachment_sale_type"]').val(), a.files = e, a.external = t, $.post(admin_url + "misc/add_sales_external_attachment", a).done(function() {
        "estimate" == a.type ? $("body").hasClass("estimates-pipeline") ? estimate_pipeline_open(a.rel_id) : init_estimate(a.rel_id) : "proposal" == a.type ? $("body").hasClass("proposals-pipeline") ? proposal_pipeline_open(a.rel_id) : init_proposal(a.rel_id) : "function" == typeof window["init_" + a.type] && window["init_" + a.type](a.rel_id), $("#sales_attach_file").modal("hide")
    })
}

function set_search_history(e) {
    for (var t = $("#search-history"), a = "", i = 0; i < e.length; i++) a += '<li data-index="' + i + '"><a href="#" class="history">' + e[i] + ' <span class="remove-history pointer pull-right" style="z-index:1500"><i class="fa fa-remove"></i></span></a></li>';
    t.html(a)
}

function requestGet(e, t) {
    t = void 0 === t ? {} : t;
    var a = {
        type: "GET",
        url: -1 < e.indexOf(admin_url) ? e : admin_url + e
    };
    return $.ajax($.extend({}, a, t))
}

function requestGetJSON(e, t) {
    return (t = void 0 === t ? {} : t).dataType = "json", requestGet(e, t)
}

function get_templates(e, t) {
    "proposals" === e ? $("#proposal-templates").load(admin_url + "templates", {
        rel_type: e,
        rel_id: t
    }) : "contracts" === e && $("#contract-templates").load(admin_url + "templates", {
        rel_type: e,
        rel_id: t
    })
}

function initStickyTinyMceToolbarInModal(e, t) {
    e.on("init", function() {
        setStickyTinyMceToolbarInModal(e, t)
    }), e.on("Remove", function(e) {
        $(t).off("scroll.editor")
    }), $(t).on("scroll.editor", function() {
        setStickyTinyMceToolbarInModal(e, t)
    })
}

function setStickyTinyMceToolbarInModal(e, t) {
    var a = e.editorContainer,
        i = $(a).find(".mce-toolbar-grp");
    $(a).find(".mce-statusbar");
    if (isTinyMceStickyInModal(e) && $(a).isInViewport()) return $(a).css({
        paddingTop: i.outerHeight()
    }), void i.css({
        top: t.scrollTop - 30,
        bottom: "auto",
        position: "fixed",
        width: $(a).width(),
        zIndex: 1,
        borderBottom: "1px solid rgba(0,0,0,0.2)"
    });
    $(a).css({
        paddingTop: 0
    }), i.css({
        position: "relative",
        width: "auto",
        borderBottom: "none",
        top: 0
    })
}

function isTinyMceStickyInModal(e) {
    return e.editorContainer.getBoundingClientRect().top < 0
}

function add_template(e, t) {
    $("#modal-wrapper").load(admin_url + "templates/modal", {
        slug: "new",
        rel_type: e,
        rel_id: t
    }, function() {
        $("#TemplateModal").is(":hidden") && $("#TemplateModal").modal({
            backdrop: "static",
            show: !0
        }), appValidateForm($("#template-form"), {
            name: "required"
        }), tinymce.remove("#content"), init_editor("#content")
    })
}

function edit_template(e, t, a) {
    $("#modal-wrapper").load(admin_url + "templates/modal", {
        slug: "edit",
        id: t,
        rel_type: e,
        rel_id: a
    }, function() {
        $("#TemplateModal").is(":hidden") && $("#TemplateModal").modal({
            backdrop: "static",
            show: !0
        }), appValidateForm($("#template-form"), {
            name: "required"
        }), tinymce.remove("#content"), init_editor("#content")
    })
}

function delete_template(t, a, e) {
    confirm_delete() && $.post(admin_url + "templates/delete/" + e).done(function(e) {
        !0 !== (e = JSON.parse(e)).success && "true" != e.success || ("proposals" === a ? $(t).parents(".proposal-templates-wrapper").html("") : "contracts" === a && $(t).parents(".contract-templates-wrapper").html(""), get_templates(a))
    })
}

function insert_template(e, a, t) {
    requestGetJSON(admin_url + "templates/index/" + t).done(function(e) {
        var t = e.data;
        tinymce.activeEditor.execCommand("mceInsertContent", !1, t.content), "proposals" == a ? $('a[aria-controls="tab_proposal"]').click() : "contracts" == a && $('a[aria-controls="tab_content"]').click(), tinymce.activeEditor.focus()
    })
}

function retrieve_imap_folders(e, t) {
    var n = $.Deferred();
    return $("#folders-loader").addClass("spinning").removeClass("hidden"), $.post(e, t).done(function(e) {
        if ((e = JSON.parse(e)).hasOwnProperty("alert_type")) alert_float(e.alert_type, e.message);
        else {
            var t = "",
                a = $("#folder"),
                i = a.selectpicker("val");
            e.forEach(function(e) {
                t += '<option name="' + e + '"' + (e == i ? " selected" : "") + ">" + e + "</option>"
            }), a.html(t), a.selectpicker("refresh"), i || a.selectpicker("val", a.find("option:eq(0)")[0].value)
        }
        n.resolve(e)
    }).fail(function(e) {
        n.reject(e)
    }).always(function() {
        $("#folders-loader").removeClass("spinning").addClass("hidden")
    }), n.promise()
}

function initDatatableOffline(e) {
    console.warn('"initDatatableOffline" is deprecated, use "initDataTableInline" instead.'), initDataTableInline(e)
}

function init_currency_symbol() {
    console.warn('"init_currency_symbol" is deprecated, use "init_currency" instead'), init_currency()
}
$("body").on("loaded.bs.select change", "select.ajax-search", function(e) {
    var t = $(this).selectpicker("val");
    if ((!Array.isArray(t) || 0 != t.length) && t && !$(this).is(":disabled")) {
        var a = $(this).parents(".bootstrap-select.ajax-search");
        if (0 === a.find(".ajax-clear-values").length) {
            var i = $(this).attr("id");
            a.addClass("ajax-remove-values-option").find("button.dropdown-toggle").after('<span class="pointer ajax-clear-values" onclick="deselect_ajax_search(this); return false;" data-id="' + i + '"><i class="fa fa-remove"></i></span>')
        }
    }
}), $("body").on("rendered.bs.select", "select", function() {
    $(this).parents().removeClass("select-placeholder"), $(this).parents(".form-group").find(".select-placeholder").removeClass("select-placeholder")
}), $("body").on("loaded.bs.select", "select", function() {
    1 == $(this).data("toggle") && $(this).selectpicker("toggle")
}), $("body").on("loaded.bs.select", "._select_input_group", function(e) {
    $(this).parents(".form-group").find(".input-group-select .input-group-addon").css("opacity", "1")
}), $(window).on("load resize", function(e) {
    $("body").hasClass("page-small") || set_body_small(), setTimeout(function() {
        mainWrapperHeightFix()
    }, "load" == e.type ? 150 : 0)
}), $(document).on("mousemove", function(e) {
    !is_mobile() && $("body").hasClass("hide-sidebar") && e.pageX <= 10 && $(".hide-menu").click()
}), $(function() {
    var e, t;
    0 < totalUnreadNotifications && (document.title = "(" + totalUnreadNotifications + ") " + doc_initial_title), $(".screen-options-btn").on("click", function() {
        $(".screen-options-area").slideToggle()
    }), $("body").hasClass("has-deprecated-errors") && (e = $("div:contains('A PHP Error was encountered')"), t = 0, $.each(e, function() {
        t += $(this).outerHeight(), $(this).css("background", "#fff")
    }), 0 < t && $("#menu, #setup-menu-wrapper").css("top", t + 70 + "px")), $("form").has('[data-entities-encode="true"]').on("submit.app.entity", function(e) {
        $(this).validate().checkForm() && $.each($('[data-entities-encode="true"]'), function() {
            $(this).hasClass("_entities-processed") || ($(this).val(htmlEntities($(this).val())), $(this).addClass("_entities-processed"))
        })
    }), add_hotkey("Shift+C", function() {
        var e = $("#lead-modal"),
            t = $("#task-modal");
        if (e.is(":visible")) convert_lead_to_customer(e.find('input[name="leadid"]').val());
        else if (t.is(":visible")) {
            var a = t.find(".tasks-comments");
            a.is(":visible") || a.css("display", "block"), init_new_task_comment()
        } else window.location.href = admin_url + "clients/client"
    }), add_hotkey("Shift+I", function() {
        window.location.href = admin_url + "invoices/invoice"
    }), add_hotkey("Shift+E", function() {
        var e = $("#lead-modal"),
            t = $("#task-modal");
        e.is(":visible") || t.is(":visible") ? e.is(":visible") ? $("a[lead-edit]").click() : t.is(":visible") && edit_task(t.find("[data-task-single-id]").attr("data-task-single-id")) : window.location.href = admin_url + "estimates/estimate"
    }), add_hotkey("Shift+F", function() {
        var e = $("#task-modal");
        if (e.is(":visible")) {
            var t = e.find("[data-task-single-id]");
            5 != t.attr("data-status") && mark_complete(t.attr("data-task-single-id"))
        }
    }), add_hotkey("Ctrl+Shift+P", function() {
        window.location.href = admin_url + "proposals/proposal"
    }), add_hotkey("Ctrl+Shift+E", function() {
        window.location.href = admin_url + "expenses/expense"
    }), add_hotkey("Shift+L", function() {
        init_lead()
    }), add_hotkey("Shift+T", function() {
        var e = $(".new-task-relation");
        0 < e.length ? new_task(admin_url + "tasks/task?rel_id=" + e.attr("data-rel-id") + "&rel_type=" + e.attr("data-rel-type")) : $("body").hasClass("project") ? new_task(admin_url + "tasks/task?rel_id=" + project_id + "&rel_type=project") : new_task()
    }), add_hotkey("Shift+P", function() {
        window.location.href = admin_url + "projects/project"
    }), add_hotkey("Shift+S", function() {
        window.location.href = admin_url + "tickets/add"
    }), add_hotkey("Ctrl+Shift+S", function() {
        window.location.href = admin_url + "staff/member"
    }), add_hotkey("Ctrl+Shift+L", function() {
        logout()
    }), add_hotkey("Alt+D", function() {
        window.location.href = admin_url
    }), add_hotkey("Alt+C", function() {
        window.location.href = admin_url + "clients"
    }), add_hotkey("Alt+T", function() {
        window.location.href = admin_url + "tasks/list_tasks"
    }), add_hotkey("Alt+I", function() {
        window.location.href = admin_url + "invoices/list_invoices"
    }), add_hotkey("Alt+E", function() {
        window.location.href = admin_url + "estimates/list_estimates"
    }), add_hotkey("Alt+P", function() {
        window.location.href = admin_url + "projects"
    }), add_hotkey("Alt+L", function() {
        window.location.href = admin_url + "leads"
    }), add_hotkey("Ctrl+Alt+T", function() {
        window.location.href = admin_url + "tickets"
    }), add_hotkey("Ctrl+Alt+E", function() {
        window.location.href = admin_url + "expenses/list_expenses"
    }), add_hotkey("Alt+R", function() {
        window.location.href = admin_url + "reports/sales"
    }), add_hotkey("Alt+S", function() {
        window.location.href = admin_url + "settings"
    }), add_hotkey("Shift+K", function() {
        $("#search_input").focus()
    }), add_hotkey("Shift+D", function() {
        $("body .dataTables_wrapper").eq(0).find(".dataTables_filter input").focus()
    }), add_hotkey("Shift+F", function() {
        $(".hide-menu").click()
    }), $.Shortcuts.start(), $(document).on("focusin", function(e) {
        $(e.target).closest(".mce-window").length && e.stopImmediatePropagation()
    }), 1 != app.options.show_setup_menu_item_only_on_hover || is_mobile() || side_bar.hover(function() {
        setTimeout(function() {
            setup_menu_item.css("display", "block")
        }, 200)
    }, function() {
        setTimeout(function() {
            setup_menu_item.css("display", "none")
        }, 1e3)
    });
    var a = $("body").find("ul.nav-tabs");
    tab_active && a.find('[href="#' + tab_active + '"]').click(), tab_group && (a.find("li").not('[role="presentation"]').removeClass("active"), a.find('[data-group="' + tab_group + '"]').parents("li").addClass("active")), moment.locale(app.locale), moment().tz(app.options.timezone).format(), init_editor(), $("body").on("click", "#started-timers-top,.popover-top-timer-note", function(e) {
        e.stopPropagation()
    }), init_tags_inputs(), init_color_pickers(), initDataTableInline(), $("body").on("change", ".onoffswitch input", function(e, t) {
        $(this).data("switch-url") && switch_field(this)
    }), custom_fields_hyperlink(), init_lightbox(), init_progress_bars(), init_datepicker(), $(document).on("app.form-validate", function(e, t) {
        if (!0 === select_picker_validated_event) return !0;
        select_picker_validated_event = !0, $(t).on("change", "select.ajax-search, select.selectpicker", function(e) {
            if ($(this).selectpicker("val") && !$(this).is(":disabled") && void 0 !== $(this).rules() && 1 === Object.keys($(this).rules()).length && $(this).rules().hasOwnProperty("required")) {
                var t = $(this).parents(".form-group");
                t.find("#" + $(this).attr("name") + "-error").remove(), t.removeClass("has-error")
            }
        })
    }), init_selectpicker(), set_body_small(), init_form_reminder(), init_ajax_search("customer", "#clientid.ajax-search");
    var i = side_bar.find('li > a[href="' + location + '"]');
    if (i.length && (i.parents("li").not(".quick-links").addClass("active"), i.prop("aria-expanded", !0), i.parents("ul.nav-second-level").prop("aria-expanded", !0), i.parents("li").find("a:first-child").prop("aria-expanded", !0)), setup_menu.hasClass("display-block")) {
        var n = setup_menu.find('li > a[href="' + location + '"]');
        n.length && (n.parents("li").addClass("active"), n.prev("active"), n.parents("ul.nav-second-level").prop("aria-expanded", !0), n.parents("li").find("a:first-child").prop("aria-expanded", !0))
    }
    side_bar.metisMenu(), setup_menu.metisMenu(), $(".hide-menu").click(function(e) {
        e.preventDefault(), $("body").hasClass("hide-sidebar") ? $("body").removeClass("hide-sidebar").addClass("show-sidebar") : $("body").removeClass("show-sidebar").addClass("hide-sidebar"), setup_menu.hasClass("display-block") && $(".close-customizer").click()
    }), is_mobile() && content_wrapper.on("click", function() {
        $("body").hasClass("show-sidebar") && $(".hide-menu").click(), setup_menu.hasClass("display-block") && $(".close-customizer").click()
    }), "safari" == app.browser && $("body").on("input", ".bootstrap-select .bs-searchbox input", function() {
        $(this).trigger("keyup")
    }), mainWrapperHeightFix(), init_tabs_scrollable(), $("#top-timers").on("click", function() {
        init_timers()
    }), set_search_history(app.user_recent_searches), $("#search-history").on("click", ".remove-history", function(e) {
        e.stopImmediatePropagation(), e.preventDefault();
        var a = $(this).parents("li").index();
        requestGet("misc/remove_recent_search/" + a).done(function(e) {
            var t = $("#search-history");
            t.find("li:eq(" + a + ")").remove(), 0 == t.find("li").length && t.removeClass("display-block")
        })
    }), $("#search_input").on("click focus", function() {
        if ("" == $(this).val()) {
            var e = $("#search-history");
            0 < e.find("li").length && e.css("width", $(this).outerWidth() + "px"), e.addClass("display-block")
        }
    }), $("#search-history").on("click", "a.history", function(e) {
        e.preventDefault();
        var t = $(this).text().trim();
        $("#search_input").val(t), $("#search_input").trigger("paste")
    }), $("#search_input").on("keyup paste" + ("safari" == app.browser ? " input" : ""), function() {
        var e = $("#search-history");
        e.removeClass("display-block");
        var t = $(this).val().trim(),
            a = $("#search_results"),
            i = $("#top_search_button button");
        if ("" === t) return content_wrapper.unhighlight(), a.html(""), original_top_search_val = "", i.html('<i class="fa fa-search"></i>').removeClass("search_remove"), void e.addClass("display-block");
        t.length < 2 && -1 === app.user_language.indexOf("chinese") && -1 === app.user_language.indexOf("japanese") || (i.html('<i class="fa fa-remove"></i>').addClass("search_remove"), delay(function() {
            t != original_top_search_val && $.post(admin_url + "misc/search", {
                q: t
            }).done(function(e) {
                e = JSON.parse(e), content_wrapper.unhighlight(), a.html(e.results), content_wrapper.highlight(t), original_top_search_val = t, set_search_history(e.history)
            })
        }, 700))
    });
    var s = get_url_param("q");
    if (s && $("#search_input").val(s).trigger("keyup"), $("body").on("blur", "#timesheet_duration", function() {
            var e = $(this),
                t = $(this).val();
            if (-1 < (t = t.replace(/[^0-9:]/gi, "")).indexOf(":")) {
                var a = t.split(":");
                if (0 === a[0].length && (a[0] = "00"), 60 <= a[1]) {
                    var i = Math.floor(parseInt(a[1] / 60));
                    a[0] = i + parseInt(a[0]), a[1] = a[1] - 60 * i
                }
                1 === a[0].toString().length && (a[0] = "0" + a[0]), 1 === a[1].toString().length ? a[1] = "0" + a[1] : 0 === a[1].toString().length && (a[1] = "00"), t = a[0] + ":" + a[1]
            } else 1 === t.length && -1 === t.indexOf(":") ? t = "0" + t + ":00" : 2 <= t.length && -1 === t.indexOf(":") && (t += ":00");
            t = "00:00" == t ? "" : t, e.val(t)
        }), $("body").on("click", ".timesheet-toggle-enter-type", function(e) {
            e.preventDefault();
            var t = $(this).find("span.switch-to").removeClass("switch-to").addClass("hide");
            $(this).find("span").not(t).removeClass("hide").addClass("switch-to"), $(".timesheet-start-end-time, .timesheet-duration").toggleClass("hide"), $(".timesheet-start-end-time input").val(""), $(".timesheet-duration input").val("")
        }), $("body").on("hidden.bs.modal", ".modal-reminder", function(e) {
            var t = $(this),
                a = t.find('input[name="rel_id"]').val(),
                i = t.find('input[name="rel_type"]').val();
            t.find("form").attr("action", admin_url + "misc/add_reminder/" + a + "/" + i), t.find("form").removeAttr("data-edit"), t.find(":input:not([type=hidden]), textarea").val(""), t.find('input[type="checkbox"]').prop("checked", !1), t.find("select").selectpicker("val", "")
        }), $("body").on("shown.bs.modal", ".modal-reminder", function(e) {
            0 == $(this).find('form[data-edit="true"]').length && $(this).find("#date").focus()
        }), $("body").on("click", ".delete-reminder", function() {
            return confirm_delete() && requestGetJSON($(this).attr("href")).done(function(e) {
                alert_float(e.alert_type, e.message), $("#task-modal").is(":visible") && _task_append_html(e.taskHtml), reload_reminders_tables()
            }), !1
        }), $("body").on("keypress", 'textarea[name="checklist-description"]', function(e) {
            if ("13" == e.which) {
                var t = $(this);
                return update_task_checklist_item(t).done(function() {
                    add_task_checklist_item(t.attr("data-taskid"))
                }), !1
            }
        }), $("body").on("blur paste", 'textarea[name="checklist-description"]', function() {
            update_task_checklist_item($(this))
        }), $("body").on("show.bs.select", "select.checklist-items-template-select", _make_task_checklist_items_deletable), $("body").on("refreshed.bs.select", "select.checklist-items-template-select", _make_task_checklist_items_deletable), $("body").on("changed.bs.select", "select.custom-field-multi-select", function(e) {
            var t = $(this).val();
            $(this).find('option[value=""]').prop("selected", 0 === t.length), $(this).selectpicker("refresh")
        }), $("body").on("change", ".task-single-inline-field", function() {
            var e = $("body").find(".task-single-inline-field"),
                i = {};
            $.each(e, function() {
                var e = $(this).attr("name"),
                    t = $(this).val(),
                    a = $(this).parents(".task-single-inline-wrap");
                "startdate" == e && "" === t ? a.addClass("text-danger") : "startdate" == e && "" !== t && a.removeClass("text-danger"), ("startdate" == e && "" !== t || "startdate" != e) && (i[$(this).attr("name")] = t, "startdate" != e && "" === t ? a.css("opacity", .5) : a.css("opacity", 1))
            });
            var t = $("#task-modal").find("[data-task-single-id]").attr("data-task-single-id");
            $.post(admin_url + "tasks/task_single_inline_update/" + t, i)
        }), $("body").on("change", "#task-modal #checklist_items_templates", function() {
            var e = $(this).val(),
                t = $(this).find('option[value="' + e + '"]').html().trim();
            "" !== t && (add_task_checklist_item($("#task-modal").find("[data-task-single-id]").attr("data-task-single-id"), t), $(this).selectpicker("val", ""))
        }), $("body").on("click", ".task-date-as-comment-id", function(e) {
            e.preventDefault();
            var t = $(this).attr("href").split("#"),
                a = $("#" + t[t.length - 1]).position();
            $("#task-modal").scrollTop(a.top)
        }), $("body").on("click", "table.dataTable tbody .tags-labels .label-tag", function() {
            $(this).parents("table").DataTable().search($(this).find(".tag").text()).draw(), $("div.dataTables_filter input").focus()
        }), $("body").on("click", "table.dataTable tbody .customer-group-list", function() {
            $(this).parents("table").DataTable().search($(this).text()).draw(), $("div.dataTables_filter input").focus()
        }), $("[data-can-view-own], [data-can-view]").on("change", function() {
            var e = $(this).attr("data-can-view-own");
            view_chk_selector = $(this).parents("tr").find("td input[" + (void 0 !== e && !1 !== e ? "data-can-view" : "data-can-view-own") + "]"), 1 != view_chk_selector.data("not-applicable") && (view_chk_selector.prop("checked", !1), view_chk_selector.prop("disabled", !0 === $(this).prop("checked")))
        }), "undefined" != typeof taskid && "" !== taskid && init_task_modal(taskid), $("body").on("change", 'input[name="checklist-box"]', function() {
            requestGet(admin_url + "tasks/checkbox_action/" + $(this).parents(".checklist").data("checklist-id") + "/" + (!0 === $(this).prop("checked") ? 1 : 0)), recalculate_checklist_items_progress(), $(this).prop("checked") && $('button[data-hide="1"]').hasClass("hide") && $(this).closest(".checklist ").addClass("hide")
        }), $("body").on("keyup paste click", "textarea[name='checklist-description']", function(e) {
            do_task_checklist_items_height($(this))
        }), $("body").on("click focus", "#task_comment", function(e) {
            init_new_task_comment()
        }), $("body").on("click", ".task-single-delete-timesheet", function(e) {
            if (e.preventDefault(), confirm_delete()) {
                var t = $(this).data("task-id");
                requestGet($(this).attr("href")).done(function(e) {
                    init_task_modal(t), setTimeout(function() {
                        reload_tasks_tables(), init_timers()
                    }, 20)
                })
            }
        }), $("body").on("click", ".task-single-add-timesheet", function(e) {
            e.preventDefault();
            var t = $("body").find('#task-modal input[name="timesheet_start_time"]').val(),
                a = $("body").find('#task-modal input[name="timesheet_end_time"]').val(),
                i = $("body").find('#task-modal input[name="timesheet_duration"]').val();
            if ("" !== t && "" !== a || "" !== i) {
                var n = {};
                n.timesheet_duration = i, n.start_time = t, n.end_time = a, n.timesheet_task_id = $(this).data("task-id"), n.note = $("body").find("#task_single_timesheet_note").val(), n.timesheet_staff_id = $("body").find('#task-modal select[name="single_timesheet_staff_id"]').val(), $.post(admin_url + "tasks/log_time", n).done(function(e) {
                    !0 === (e = JSON.parse(e)).success || "true" == e.success ? (init_task_modal(n.timesheet_task_id), alert_float("success", e.message), setTimeout(function() {
                        reload_tasks_tables()
                    }, 20)) : alert_float("warning", e.message)
                })
            }
        }), $("body").on("click", ".copy_task_action", function() {
            var e = {};
            return $(this).prop("disabled", !0), e.copy_from = $(this).data("task-copy-from"), e.copy_task_assignees = $("body").find("#copy_task_assignees").prop("checked"), e.copy_task_followers = $("body").find("#copy_task_followers").prop("checked"), e.copy_task_checklist_items = $("body").find("#copy_task_checklist_items").prop("checked"), e.copy_task_attachments = $("body").find("#copy_task_attachments").prop("checked"), e.copy_task_status = $("body").find('input[name="copy_task_status"]:checked').val(), $.post(admin_url + "tasks/copy", e).done(function(e) {
                if (!0 === (e = JSON.parse(e)).success || "true" == e.success) {
                    var t = $("#_task_modal");
                    t.is(":visible") && t.modal("hide"), init_task_modal(e.new_task_id), reload_tasks_tables()
                }
                alert_float(e.alert_type, e.message)
            }), !1
        }), $("body").on("click", ".new-task-to-milestone", function(e) {
            e.preventDefault();
            var t = $(this).parents(".milestone-column").data("col-status-id");
            new_task(admin_url + "tasks/task?rel_type=project&rel_id=" + project_id + "&milestone_id=" + t), $('body [data-toggle="popover"]').popover("hide")
        }), $("body").on("shown.bs.modal", "#_task_modal", function(e) {
            $(e.currentTarget).hasClass("edit") ? "" !== $(this).find(".tinymce-task").val().trim() && init_editor(".tinymce-task", {
                height: 200
            }) : $("body").find("#_task_modal #name").focus(), init_tags_inputs()
        }), $("body").on("hidden.bs.modal", "#_task_modal", function() {
            tinyMCE.remove(".tinymce-task"), "undefined" != typeof _ticket_message && (_ticket_message = void 0), null == $(this).attr("data-lead-id") || $(this).attr("data-task-created") || init_lead($(this).attr("data-lead-id")), destroy_dynamic_scripts_in_element($("body #_task_modal")), $("#_task").empty()
        }), $("body").on("hide.bs.modal", "#task-modal", function() {
            if (1 == $("#lightbox").is(":visible")) return !1;
            "undefined" != typeof taskAttachmentDropzone && taskAttachmentDropzone.destroy();
            var e = tinyMCE.get("#task_view_description");
            e && (e.blur(), tinyMCE.remove("#task_view_description"))
        }), $("body").on("hide.bs.modal", "#task-modal", function(t) {
            tinymce.editors.forEach(function(e) {
                e.id.includes("task_comment") && "" !== e.getContent() && $(e.editorContainer).is(":visible") && t.preventDefault()
            })
        }), $("body").on("hidden.bs.modal", "#task-modal", function() {
            destroy_dynamic_scripts_in_element($(this)), $(this).find(".data").empty()
        }), $("body").on("shown.bs.modal", "#task-modal", function() {
            do_task_checklist_items_height(), init_tags_inputs(), fix_task_modal_left_col_height(), $(document).off("focusin.modal");
            var e = window.location.href;
            if (-1 < e.indexOf("#comment_")) {
                var t = e.split("#comment_");
                t = t[t.length - 1], $('[data-task-comment-href-id="' + t + '"]').click()
            }
        }), $("body").on("blur", "#task-modal ul.tagit li.tagit-new input", function() {
            setTimeout(function() {
                task_single_update_tags()
            }, 100)
        }), $("body").on("change", 'select[name="select-assignees"]', function() {
            $("body").append('<div class="dt-loader"></div>');
            var e = {};
            e.assignee = $('select[name="select-assignees"]').val(), "" !== e.assignee && (e.taskid = $(this).attr("data-task-id"), $.post(admin_url + "tasks/add_task_assignees", e).done(function(e) {
                $("body").find(".dt-loader").remove(), e = JSON.parse(e), reload_tasks_tables(), _task_append_html(e.taskHtml)
            }))
        }), $("body").on("change", 'select[name="select-followers"]', function() {
            var e = {};
            e.follower = $('select[name="select-followers"]').val(), "" !== e.follower && (e.taskid = $(this).attr("data-task-id"), $("body").append('<div class="dt-loader"></div>'), $.post(admin_url + "tasks/add_task_followers", e).done(function(e) {
                e = JSON.parse(e), $("body").find(".dt-loader").remove(), _task_append_html(e.taskHtml)
            }))
        }), $("body").on("click", ".close-task-stats", function() {
            $("#task-tracking-stats-modal").modal("hide")
        }), $("body").on("hidden.bs.modal", "#task-tracking-stats-modal", function() {
            $("#tracking-stats").remove()
        }), $("body").on("show.bs.modal", "#task-tracking-stats-modal", function() {
            var e = $("body").find("#task-tracking-stats-chart");
            setTimeout(function() {
                "undefined" != typeof taskTrackingChart && taskTrackingChart.destroy(), taskTrackingChart = new Chart(e, {
                    type: "line",
                    data: taskTrackingStatsData,
                    options: {
                        legend: {
                            display: !1
                        },
                        responsive: !0,
                        maintainAspectRatio: !1,
                        tooltips: {
                            enabled: !0,
                            mode: "single",
                            callbacks: {
                                label: function(e, t) {
                                    return decimalToHM(e.yLabel)
                                }
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: !0,
                                    min: 0,
                                    userCallback: function(e, t, a) {
                                        return decimalToHM(e)
                                    }
                                }
                            }]
                        }
                    }
                })
            }, 800)
        }), $("body").on("shown.bs.modal", "#sync_data_proposal_data", function() {
            "lead" == $("#sync_data_proposal_data").data("rel-type") && $("#lead-modal .data").eq(0).css("height", $("#sync_data_proposal_data .modal-content").height() + 80 + "px").css("overflow-x", "hidden")
        }), $("body").on("hidden.bs.modal", "#sync_data_proposal_data", function() {
            "lead" == $("#sync_data_proposal_data").data("rel-type") && $("#lead-modal .data").prop("style", "")
        }), "undefined" != typeof openLeadID && "" !== openLeadID && init_lead(openLeadID, !!get_url_param("edit")), $("body").on("click", ".leads-kan-ban .cpicker", function() {
            var e = $(this).data("color"),
                t = $(this).parents(".panel-heading-bg").data("status-id");
            $.post(admin_url + "leads/change_status_color", {
                color: e,
                status_id: t
            })
        }), $("body").on("click", "[lead-edit]", function(e) {
            e.preventDefault();
            var t = $("body .lead-edit");
            if ($("body .lead-view").toggleClass("hide"), t.toggleClass("hide"), !t.hasClass("hide")) {
                var a = $("#lead-modal").find("#address"),
                    i = a[0].scrollHeight;
                a.is("textarea") && (a.height(0).height(i - 15), a.css("padding-top", "9px"))
            }
        }), $("body").on("click", ".new-lead-from-status", function(e) {
            e.preventDefault();
            var t = $(this).parents(".kan-ban-col").data("col-status-id");
            init_lead_modal_data(void 0, admin_url + "leads/lead?status_id=" + t), $('body [data-toggle="popover"]').popover("hide")
        }), $("body").on("change", "input.include_leads_custom_fields", function() {
            var e = $(this).val(),
                t = $(this).data("field-id");
            2 == e ? $("#merge_db_field_" + t).removeClass("hide") : $("#merge_db_field_" + t).addClass("hide"), 3 == e ? $("#merge_db_contact_field_" + t) : $("#merge_db_contact_field_" + t).addClass("hide")
        }), 0 < calendar_selector.length) {
        validate_calendar_form();
        var o = {
            customButtons: {},
            locale: app.locale,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay"
            },
            editable: !1,
            dayMaxEventRows: parseInt(app.options.calendar_events_limit) + 1,
            views: {
                day: {
                    dayMaxEventRows: !1
                }
            },
            direction: "true" == isRTL ? "rtl" : "ltr",
            eventStartEditable: !1,
            firstDay: parseInt(app.options.calendar_first_day),
            initialView: app.options.default_view_calendar,
            timeZone: app.options.timezone,
            loading: function(e, t) {
                e ? $(".dt-loader").removeClass("hide") : $(".dt-loader").addClass("hide")
            },
            eventSources: [function(e, t, a) {
                var i = {};
                return $("#calendar_filters").find("input:checkbox:checked").map(function() {
                    i[$(this).attr("name")] = !0
                }).get(), jQuery.isEmptyObject(i) || (i.calendar_filters = !0), $.getJSON(admin_url + "utilities/get_calendar_data", $.extend({}, i, {
                    start: e.startStr,
                    end: e.endStr
                })).then(function(e) {
                    t(e.map(function(e) {
                        return $.extend({}, e, {
                            start: e.start || e.date,
                            end: e.end || e.date
                        })
                    }))
                })
            }],
            moreLinkClick: function(e) {
                r.gotoDate(e.date), r.changeView("dayGridDay"), setTimeout(function() {
                    $(".fc-popover-close").click()
                }, 250)
            },
            eventDidMount: function(e) {
                var t = $(e.el);
                t.attr("title", e.event.extendedProps._tooltip), t.attr("onclick", e.event.extendedProps.onclick), t.attr("data-toggle", "tooltip"), e.event.extendedProps.url || t.on("click", function() {
                    view_event(e.event.extendedProps.eventid)
                })
            },
            dateClick: function(e) {
                e.dateStr.length <= 10 && (e.dateStr += " 00:00");
                var t = (new DateFormatter).formatDate(new Date(e.dateStr), vformat = 24 == app.options.time_format ? app.options.date_format + " H:i" : app.options.date_format + " g:i A");
                return $("input[name='start'].datetimepicker").val(t), $("#newEventModal").modal("show"), !1
            }
        };
        if ($("body").hasClass("dashboard") && (o.customButtons.viewFullCalendar = {
                text: app.lang.calendar_expand,
                click: function() {
                    window.location.href = admin_url + "utilities/calendar"
                }
            }, o.headerToolbar.left += ",viewFullCalendar"), o.customButtons.calendarFilter = {
                text: app.lang.filter_by.toLowerCase(),
                click: function() {
                    slideToggle("#calendar_filters")
                }
            }, o.headerToolbar.right += ",calendarFilter", 1 == app.user_is_staff_member && ("" !== app.options.google_api && (o.googleCalendarApiKey = app.options.google_api), "" !== app.calendarIDs && (app.calendarIDs = JSON.parse(app.calendarIDs), 0 != app.calendarIDs.length)))
            if ("" !== app.options.google_api)
                for (var l = 0; l < app.calendarIDs.length; l++) {
                    var d = {};
                    d.googleCalendarId = app.calendarIDs[l], o.eventSources.push(d)
                } else console.error("You have setup Google Calendar IDs but you dont have specified Google API key. To setup Google API key navigate to Setup->Settings->Google");
        var r = new FullCalendar.Calendar(calendar_selector[0], o);
        r.render(), get_url_param("new_event") && ($("input[name='start'].datetimepicker").val(get_url_param("date")), $("#newEventModal").modal("show"))
    }
    $("body").on("change", 'select[name="tax"]', function() {
        var e = $("body").find('select[name="tax2"]'),
            t = $(this);
        "" !== t.val() ? e.prop("disabled", !1) : (e.prop("disabled", !0), "" !== e.val() && (t.selectpicker("val", e.val()), e.val(""), t.selectpicker("refresh"))), e.selectpicker("refresh")
    }), $("body").on("click", "#invoice_create_credit_note", function(e) {
        if (2 == $(this).attr("data-status")) return !0;
        var t = $("#confirm_credit_note_create_from_invoice");
        t.modal("show"), t.find("#confirm-invoice-credit-note").attr("href", $(this).attr("href")), e.preventDefault()
    }), $("body").on("change blur", ".apply-credits-to-invoice .apply-credits-field", function() {
        var e = $("#apply_credits"),
            t = e.find("input.apply-credits-field"),
            a = 0,
            i = e.attr("data-credits-remaining");
        $.each(t, function() {
            if (!0 === $(this).valid()) {
                var e = $(this).val();
                e = parseFloat(e), isNaN(e) ? $(this).val(0) : a += e
            }
        }), e.find("#credits-alert").remove(), e.find(".amount-to-credit").html(format_money(a)), i < a ? ($(".credits-table").before($("<div/>", {
            id: "credits-alert",
            class: "alert alert-danger"
        }).html(app.lang.credit_amount_bigger_then_credit_note_remaining_credits)), e.find('[type="submit"]').prop("disabled", !0)) : (e.find(".credit-note-balance-due").html(format_money(i - a)), e.find('[type="submit"]').prop("disabled", !1))
    }), $("body").on("change blur", ".apply-credits-from-invoice .apply-credits-field", function() {
        var e = $("#apply_credits"),
            t = e.find("input.apply-credits-field"),
            a = 0,
            i = e.attr("data-balance-due");
        $.each(t, function() {
            if (!0 === $(this).valid()) {
                var e = $(this).val();
                e = parseFloat(e), isNaN(e) ? $(this).val(0) : a += e
            }
        }), e.find("#credits-alert").remove(), e.find(".amount-to-credit").html(format_money(a)), i < a ? ($(".credits-table").before($("<div/>", {
            id: "credits-alert",
            class: "alert alert-danger"
        }).html(app.lang.credit_amount_bigger_then_invoice_balance)), e.find('[type="submit"]').prop("disabled", !0)) : (e.find(".invoice-balance-due").html(format_money(i - a)), e.find('[type="submit"]').prop("disabled", !1))
    }), $('input[name="notify_type"]').on("change", function() {
        var e = $('input[name="notify_type"]:checked').val(),
            t = $("#specific_staff_notify"),
            a = $("#role_notify");
        "specific_staff" == e ? (t.removeClass("hide"), a.addClass("hide")) : "roles" == e ? (t.addClass("hide"), a.removeClass("hide")) : "assigned" == e && (t.addClass("hide"), a.addClass("hide"))
    }), $("body").on("shown.bs.modal", "#lead-modal", function(e) {
        custom_fields_hyperlink(), 0 === $("body").find('#lead-modal input[name="leadid"]').length && $("body").find('#lead-modal input[name="name"]').focus(), init_tabs_scrollable(), $("body").find(".lead-wrapper").hasClass("open-edit") && $("body").find("a[lead-edit]").click()
    }), $("body").on("show.bs.modal", "#lead-modal", function(e) {
        0 == $("#lead-more-dropdown").find("li").length && $("#lead-more-btn").css("opacity", 0).css("pointer-events", "none")
    }), $("#lead-modal").on("hidden.bs.modal", function(e) {
        destroy_dynamic_scripts_in_element($(this)), $(this).data("bs.modal", null), $("#lead_reminder_modal").html(""), $("#lead-modal").is(":visible") || history.pushState("", document.title, window.location.pathname + window.location.search), $("body #lead-modal .datetimepicker").datetimepicker("destroy"), "undefined" != typeof leadAttachmentsDropzone && leadAttachmentsDropzone.destroy()
    }), $("body").on("submit", "#lead-modal .consent-form", function() {
        var e = $(this).serialize();
        return $.post($(this).attr("action"), e).done(function(e) {
            init_lead_modal_data((e = JSON.parse(e)).lead_id)
        }), !1
    }), $("body").on("click", '#lead-modal a[data-toggle="tab"]', function() {
        "#tab_lead_profile" == this.hash || "#attachments" == this.hash || "#lead_notes" == this.hash || "#gdpr" == this.hash || "#lead_activity" == this.hash ? window.location.hash = this.hash : history.pushState("", document.title, window.location.pathname + window.location.search), $(document).resize()
    }), $("body").on("click", "#lead_enter_activity", function() {
        var e = $("#lead_activity_textarea").val(),
            t = $("#lead-modal").find('input[name="leadid"]').val();
        "" !== e && $.post(admin_url + "leads/add_activity", {
            leadid: t,
            activity: e
        }).done(function(e) {
            _lead_init_data(e = JSON.parse(e), e.id)
        }).fail(function(e) {
            alert_float("danger", e.responseText)
        })
    }), $("body").on("submit", "#lead-modal #lead-notes", function() {
        var e = $(this),
            t = $(e).serialize();
        return $.post(e.attr("action"), t).done(function(e) {
            _lead_init_data(e = JSON.parse(e), e.id)
        }).fail(function(e) {
            alert_float("danger", e.responseText)
        }), !1
    });
    var c = {
        custom_view: "[name='custom_view']",
        assigned: "[name='view_assigned']",
        status: "[name='view_status[]']",
        source: "[name='view_source']"
    };
    if ((table_leads = $("table.table-leads")).length) {
        var p = table_leads.find("#th-consent"),
            _ = [0],
            m = [0, table_leads.find("#th-assigned").index()];
        0 < p.length && (_.push(p.index()), m.push(p.index())), _table_api = initDataTable(table_leads, admin_url + "leads/table", m, _, c, [table_leads.find("th.date-created").index(), "desc"]), _table_api && 0 < p.length && _table_api.on("draw", function() {
            var e = table_leads.find("tbody tr");
            $.each(e, function() {
                $(this).find("td:eq(3)").addClass("bg-light-gray")
            })
        }), $.each(c, function(e, t) {
            $("select" + t).on("change", function() {
                $("[name='view_status[]']").prop("disabled", "lost" == $(this).val() || "junk" == $(this).val()).selectpicker("refresh"), table_leads.DataTable().ajax.reload()
            })
        })
    }
    if ($("body").on("change", 'input[name="contacted_today"]', function() {
            var e = $(this).prop("checked"),
                t = $(".lead-select-date-contacted");
            0 == e ? t.removeClass("hide") : t.addClass("hide")
        }), $("body").on("change", 'input[name="contacted_indicator"]', function() {
            var e = $(".lead-select-date-contacted");
            "yes" == $(this).val() ? e.removeClass("hide") : e.addClass("hide")
        }), $("body").on("click", ".close-reminder-modal", function() {
            $(".reminder-modal-" + $(this).data("rel-type") + "-" + $(this).data("rel-id")).modal("hide")
        }), $("form").not("#single-ticket-form,#calendar-event-form,#proposal-form, #department-form").areYouSure(), $("body").on("click", ".editor-add-content-notice", function() {
            var e = $(this);
            setTimeout(function() {
                e.remove(), tinymce.triggerSave()
            }, 500)
        }), $(".bulk_actions").on("change", 'input[name="mass_delete"]', function() {
            var e = $("#bulk_change");
            !0 === $(this).prop("checked") && e.find("select").selectpicker("val", ""), e.toggleClass("hide"), $(".mass_delete_separator").toggleClass("hide")
        }), $("body").on("change loaded.bs.select", "#item_select", function() {
            var e = $(".items-wrapper .items-select-wrapper"),
                t = $(".items-wrapper .input-group-addon");
            0 === t.length ? $(".items-wrapper .bootstrap-select").css("width", "100%") : $(".items-wrapper .bootstrap-select").css("width", e.width() - t.width() + 12 + "px")
        }), $(".send-test-sms").on("click", function() {
            var e = $(this).data("id"),
                t = $('#sms_test_response[data-id="' + e + '"]'),
                a = $('textarea[data-id="' + e + '"]').val(),
                i = $('input.test-phone[data-id="' + e + '"]').val(),
                n = $(this);
            t.empty(), "" != (a = a.trim()) && "" != i && (n.prop("disabled", !0), $.post(window.location.href, {
                message: a,
                number: i,
                id: e,
                sms_gateway_test: !0
            }).done(function(e) {
                1 == (e = JSON.parse(e)).success ? t.html('<div class="alert alert-success no-mbot mtop15">SMS Sent Successfully!</div>') : t.html('<div class="alert alert-warning no-mbot mtop15">' + e.error + "</div>")
            }).always(function() {
                n.prop("disabled", !1)
            }))
        }), $("body").on("hidden.bs.modal", "#__todo", function() {
            var e = $("#__todo");
            e.find('input[name="todoid"]').val(""), e.find('textarea[name="description"]').val(""), e.find(".add-title").addClass("hide"), e.find(".edit-title").addClass("hide")
        }), $("body").on("shown.bs.modal", "#__todo", function() {
            var e = $("#__todo");
            e.find('textarea[name="description"]').focus(), "" !== e.find('input[name="todoid"]').val() ? (e.find(".add-title").addClass("hide"), e.find(".edit-title").removeClass("hide")) : (e.find(".add-title").removeClass("hide"), e.find(".edit-title").addClass("hide"))
        }), $("#top_search_button button").on("click", function() {
            var e = $("#search_input");
            $(this).hasClass("search_remove") && ($(this).html('<i class="fa fa-search"></i>').removeClass("search_remove"), original_top_search_val = "", $("#search_results").html(""), e.val("")), e.focus()
        }), $("body").click(function(e) {
            $(e.target).parents("#top_search_dropdown").hasClass("search-results") || $("#top_search_dropdown").remove()
        }), $("body").tooltip({
            selector: '[data-toggle="tooltip"]'
        }), $("body").popover({
            selector: '[data-toggle="popover"]'
        }), $("body").on("click", "._filter_data ul.dropdown-menu li a,.not-mark-as-read-inline,.not_mark_all_as_read a", function(e) {
            e.stopPropagation(), e.preventDefault()
        }), $("body").on("shown.bs.modal", ".modal", function() {
            $("body").addClass("modal-open"), $("body").find("#started-timers-top").parents("li").removeClass("open")
        }), $("body").on("hidden.bs.modal", ".modal", function(e) {
            $(".modal:visible").length && $(document.body).addClass("modal-open"), $(this).data("bs.modal", null)
        }), $(".datepicker.activity-log-date").on("change", function() {
            table_activity_log.DataTable().ajax.reload()
        }), $(".btn-import-submit").on("click", function() {
            $(this).hasClass("simulate") && $("#import_form").append(hidden_input("simulate", !0)), $("#import_form").submit()
        }), $("body").on("change", "#unlimited_cycles", function() {
            $(this).parents(".recurring-cycles").find("#cycles").prop("disabled", $(this).prop("checked"))
        }), $("body").on("change", '[name="repeat_every"], [name="recurring"]', function() {
            var e = $(this).val();
            "custom" == e ? $(".recurring_custom").removeClass("hide") : $(".recurring_custom").addClass("hide"), "" !== e && 0 != e ? $("body").find("#cycles_wrapper").removeClass("hide") : ($("body").find("#cycles_wrapper").addClass("hide"), $("body").find("#cycles_wrapper #cycles").val(0), $("#unlimited_cycles").prop("checked", !0).change())
        }), $("body").on("change", "#mass_select_all", function() {
            var e, t, a;
            e = $(this).data("to-table"), t = $(".table-" + e).find("tbody tr"), a = $(this).prop("checked"), $.each(t, function() {
                $($(this).find("td").eq(0)).find("input").prop("checked", a)
            })
        }), $("body").on("show.bs.modal", ".modal.email-template", function() {
            init_editor($(this).data("editor-id"), {
                urlconverter_callback: "merge_field_format_url"
            })
        }), $("body").on("hidden.bs.modal", ".modal.email-template", function() {
            tinymce.remove($(this).data("editor-id"))
        }), $(".close-customizer").on("click", function(e) {
            e.preventDefault(), setup_menu.addClass("true" == isRTL ? "fadeOutRight" : "fadeOutLeft"), requestGet("misc/set_setup_menu_closed")
        }), $(".open-customizer").on("click", function(e) {
            e.preventDefault(), setup_menu.hasClass("true" == isRTL ? "fadeOutRight" : "fadeOutLeft") && setup_menu.removeClass("true" == isRTL ? "fadeOutRight" : "fadeOutLeft"), setup_menu.addClass("display-block " + ("true" == isRTL ? "fadeInRight" : "fadeInLeft")), is_mobile() || requestGet("misc/set_setup_menu_open"), mainWrapperHeightFix()
        }), $("body").on("click", ".cpicker", function() {
            var e = $(this).data("color");
            if ($(this).hasClass("cpicker-big")) return !1;
            $(this).parents(".cpicker-wrapper").find(".cpicker-big").removeClass("cpicker-big").addClass("cpicker-small"), $(this).removeClass("cpicker-small", "fast").addClass("cpicker-big", "fast"), $(this).hasClass("kanban-cpicker") ? ($(this).parents(".panel-heading-bg").css("background", e), $(this).parents(".panel-heading-bg").css("border", "1px solid " + e)) : $(this).hasClass("calendar-cpicker") && $("body").find('._event input[name="color"]').val(e)
        }), $("body").on("click", ".notification_link", function() {
            var e = $(this).data("link");
            e.split("#")[1] || (window.location.href = e)
        }), $("body").on("click" + ("ontouchstart" in window ? " touchstart" : ""), ".notifications a.notification-top, .notification_link", function(e) {
            e.preventDefault();
            var t, a = $(this),
                i = a.hasClass("notification_link") ? a.data("link") : e.currentTarget.href,
                n = i.split("#"),
                s = !0;
            if (n[1] && -1 < n[1].indexOf("="))
                if (s = !1, t = n[1].split("=")[1], -1 < n[1].indexOf("postid")) postid = t, 769 < $(window).width() ? $(".open_newsfeed.desktop").click() : $(".open_newsfeed.mobile").click();
                else if (-1 < n[1].indexOf("taskid")) {
                var o = void 0;
                if (-1 < i.indexOf("#comment_")) {
                    var l = i.split("#comment_");
                    o = l[l.length - 1]
                }
                init_task_modal(t, o)
            } else -1 < n[1].indexOf("leadid") ? init_lead(t) : -1 < n[1].indexOf("eventid") && view_event(t);
            a.hasClass("desktopClick") || a.parent("li").find(".not-mark-as-read-inline").click(), s && setTimeout(function() {
                window.location.href = n
            }, 50)
        }), $(".notifications-wrapper").on("show.bs.dropdown", function() {
            0 < notifications_wrapper.find(".notifications").attr("data-total-unread") && $.post(admin_url + "misc/set_notifications_read").done(function(e) {
                !0 !== (e = JSON.parse(e)).success && "true" != e.success || (document.title = doc_initial_title, $(".icon-notifications").addClass("hide"))
            })
        }), init_table_tickets(), init_table_announcements(), init_table_staff_projects(), (table_activity_log = $("table.table-activity-log")).length) {
        var u = [];
        u.activity_log_date = '[name="activity_log_date"]', initDataTable(table_activity_log, window.location.href, "undefined", "undefined", u, [1, "desc"])
    }
    if (table_invoices = $("table.table-invoices"), table_estimates = $("table.table-estimates"), 0 < table_invoices.length || 0 < table_estimates.length) {
        var f = {},
            h = $("._hidden_inputs._filters input");
        $.each(h, function() {
            f[$(this).attr("name")] = '[name="' + $(this).attr("name") + '"]'
        }), table_invoices.length && initDataTable(table_invoices, admin_url + "invoices/table" + ($("body").hasClass("recurring") ? "?recurring=1" : ""), "undefined", "undefined", f, $("body").hasClass("recurring") ? [table_invoices.find("th.next-recurring-date").index(), "asc"] : [
            [3, "desc"],
            [0, "desc"]
        ]), table_estimates.length && initDataTable(table_estimates, admin_url + "estimates/table", "undefined", "undefined", f, [
            [3, "desc"],
            [0, "desc"]
        ])
    }
    if ((table_tasks = $(".table-tasks")).length) {
        var v, b = {};
        v = $("._hidden_inputs._filters._tasks_filters input"), $.each(v, function() {
            b[$(this).attr("name")] = '[name="' + $(this).attr("name") + '"]'
        });
        var g = [0],
            k = admin_url + "tasks/table";
        $("body").hasClass("tasks-page") && (k += "?bulk_actions=true"), _table_api = initDataTable(table_tasks, k, g, g, b, [table_tasks.find("th.duedate").index(), "asc"]), _table_api && $("body").hasClass("dashboard") && _table_api.column(5).visible(!1, !1).column(6).visible(!1, !1).columns.adjust()
    }
    $("#send_file").on("show.bs.modal", function(e) {
        var t = $("#send_file");
        t.find('input[name="filetype"]').val($($(e.relatedTarget)).data("filetype")), t.find('input[name="file_path"]').val($($(e.relatedTarget)).data("path")), t.find('input[name="file_name"]').val($($(e.relatedTarget)).data("file-name")), 0 < $('input[name="email"]').length && t.find('input[name="send_file_email"]').val($('input[name="email"]').val())
    }), $("#send_file form").on("submit", function() {
        $(this).find('button[type="submit"]').prop("disabled", !0)
    }), $("body").on("change", 'input[name="send_set_password_email"]', function() {
        $("body").find(".client_password_set_wrapper").toggleClass("hide")
    }), $("body").on("change", '.todo input[type="checkbox"]', function() {
        var e = !0 === $(this).prop("checked") ? 1 : 0,
            t = $(this).val();
        window.location.href = admin_url + "todo/change_todo_status/" + t + "/" + e
    });
    var y = $(".todos-sortable");
    (0 < y.length && (y = y.sortable({
        connectWith: ".todo",
        items: "li",
        handle: ".dragger",
        appendTo: "body",
        update: function(e, t) {
            this === t.item.parent()[0] && update_todo_items()
        }
    })), $("body").on("click", ".open_newsfeed, .close_newsfeed", function(e) {
        e.preventDefault(), void 0 === $(this).data("close") ? requestGet("newsfeed/get_data").done(function(e) {
            $("#newsfeed").html(e), load_newsfeed(postid), init_newsfeed_form(), init_selectpicker(), init_lightbox()
        }) : !0 === $(this).data("close") && (newsFeedDropzone.destroy(), $("#newsfeed").html(""), postid = void(track_load_comment_likes = track_load_post_likes = newsfeed_posts_page = 0)), $("#newsfeed").toggleClass("hide"), $("body").toggleClass("noscroll")
    }), 0 < $("[data-newsfeed-auto]").length && (769 < $(window).width() ? $(".open_newsfeed.desktop").click() : $(".open_newsfeed.mobile").click()), $("body").on("keyup", ".comment-input input", function(e) {
        13 == e.keyCode && add_comment(this)
    }), $("#modal_post_likes").on("show.bs.modal", function(e) {
        track_load_post_likes = 0, $("#modal_post_likes_wrapper").empty(), $(".likes_modal .modal-footer").removeClass("hide");
        var t = $(e.relatedTarget),
            a = $(t).data("postid");
        post_likes_total_pages = $(t).data("total-pages"), $(".load_more_post_likes").attr("data-postid", a), load_post_likes(a)
    }), $("#modal_post_comment_likes").on("show.bs.modal", function(e) {
        $("#modal_comment_likes_wrapper").empty(), track_load_comment_likes = 0, $(".likes_modal .modal-footer").removeClass("hide");
        var t = $(e.relatedTarget),
            a = $(t).data("commentid");
        comment_likes_total_pages = $(t).data("total-pages"), $(".load_more_post_comment_likes").attr("data-commentid", a), load_comment_likes(a)
    }), $(".load_more_post_likes").on("click", function(e) {
        e.preventDefault(), load_post_likes($(this).data("postid"))
    }), $(".load_more_post_comment_likes").on("click", function(e) {
        e.preventDefault(), load_comment_likes($(this).data("commentid"))
    }), $(".add-attachments").on("click", function(e) {
        e.preventDefault(), $("#post-attachments").toggleClass("hide")
    }), init_invoices_total(), init_expenses_total(), init_estimates_total(), init_items_sortable(), $(".settings-textarea-merge-field").on("click", function(e) {
        e.preventDefault();
        var t = $(this).text().trim(),
            a = $('textarea[name="settings[' + $(this).data("to") + ']"]');
        a.val(a.val() + "\n" + t)
    }), $("body").hasClass("estimates-pipeline")) && estimate_pipeline_open($('input[name="estimateid"]').val());
    $("body").hasClass("proposals-pipeline") && proposal_pipeline_open($('input[name="proposalid"]').val());
    $("body").on("submit", "._transaction_form", function() {
        calculate_total(), $("body").find("#items-warning").remove();
        var e = $(this).find("table.items"),
            t = e.find(".main");
        return t.find('[name="description"]').length && 0 < t.find('[name="description"]').val().trim().length && 0 < t.find('[name="rate"]').val().trim().length ? (e.before('<div class="alert alert-warning mbot20" id="items-warning">' + app.lang.item_forgotten_in_preview + '<i class="fa fa-angle-double-down pointer pull-right fa-2x" style="margin-top:-4px;" onclick="add_item_to_table(\'undefined\',\'undefined\',undefined); return false;"></i></div>'), $("html,body").animate({
            scrollTop: $("#items-warning").offset().top
        }), !1) : e.length && 0 === e.find(".item").length ? (e.before('<div class="alert alert-warning mbot20" id="items-warning">' + app.lang.no_items_warning + "</div>"), $("html,body").animate({
            scrollTop: $("#items-warning").offset().top
        }), !1) : (reorder_items(), $('select[name="currency"]').prop("disabled", !1), $('select[name="project_id"]').prop("disabled", !1), $('input[name="date"]').prop("disabled", !1), $(this).find(".transaction-submit").prop("disabled", !0), !0)
    }), $("body").on("click", ".transaction-submit", function() {
        var e = $(this),
            t = e.parents("form._transaction_form");
        t.valid() && (e.hasClass("save-as-draft") ? t.append(hidden_input("save_as_draft", "true")) : e.hasClass("save-and-send") ? t.append(hidden_input("save_and_send", "true")) : e.hasClass("save-and-record-payment") ? t.append(hidden_input("save_and_record_payment", "true")) : e.hasClass("save-and-send-later") && t.append(hidden_input("save_and_send_later", "true"))), t.submit()
    }), $("body").on("submit", "#sales-notes", function() {
        var t = $(this);
        if ("" !== t.find('textarea[name="description"]').val()) return $.post(t.attr("action"), $(t).serialize()).done(function(e) {
            t.find('textarea[name="description"]').val(""), t.hasClass("estimate-notes-form") ? get_sales_notes(e, "estimates") : t.hasClass("invoice-notes-form") ? get_sales_notes(e, "invoices") : t.hasClass("proposal-notes-form") ? get_sales_notes(e, "proposals") : t.hasClass("contract-notes-form") && get_sales_notes(e, "contracts")
        }), !1
    }), $("body").on("change", 'input[name="show_quantity_as"]', function() {
        $("body").find("th.qty").html($(this).data("text"))
    }), $("body").on("change", 'div.credit_note input[name="date"]', function() {
        do_prefix_year($(this).val())
    }), $("body").on("change", 'div.invoice input[name="date"], div.estimate input[name="date"], div.proposal input[name="date"]', function() {
        var e = $(this).val();
        if (do_prefix_year(e), !(0 < $('input[name="isedit"]').length)) {
            var t = "duedate",
                a = admin_url + "invoices/get_due_date";
            0 < $("body").find("div.estimate").length ? (a = admin_url + "estimates/get_due_date", t = "expirydate") : 0 < $("body").find("div.proposal").length && (a = admin_url + "proposals/get_due_date", t = "open_till"), "" === e && $('input[name="' + t + '"]').val(""), "" !== e && $.post(a, {
                date: e
            }).done(function(e) {
                e && $('input[name="' + t + '"]').val(e)
            })
        }
    }), $("#sales_attach_file").on("hidden.bs.modal", function(e) {
        $("#sales_uploaded_files_preview").empty(), $(".dz-file-preview").empty()
    }), "undefined" != typeof Dropbox && 0 < $("#dropbox-chooser-sales").length && document.getElementById("dropbox-chooser-sales").appendChild(Dropbox.createChooseButton({
        success: function(e) {
            salesExtenalFileUpload(e, "dropbox")
        },
        linkType: "preview",
        extensions: app.options.allowed_files.split(",")
    })), 0 < $("#sales-upload").length && new Dropzone("#sales-upload", appCreateDropzoneOptions({
        sending: function(e, t, a) {
            a.append("rel_id", $("body").find('input[name="_attachment_sale_id"]').val()), a.append("type", $("body").find('input[name="_attachment_sale_type"]').val())
        },
        success: function(e, t) {
            t = JSON.parse(t);
            var a, i = $("body").find('input[name="_attachment_sale_type"]').val();
            a = "delete_" + i + "_attachment", "estimate" == i ? $("body").hasClass("estimates-pipeline") ? estimate_pipeline_open(t.rel_id) : init_estimate(t.rel_id) : "proposal" == i ? $("body").hasClass("proposals-pipeline") ? proposal_pipeline_open(t.rel_id) : init_proposal(t.rel_id) : "function" == typeof window["init_" + i] && window["init_" + i](t.rel_id);
            var n = "";
            !0 !== t.success && "true" != t.success || (n += '<div class="display-block sales-attach-file-preview" data-attachment-id="' + t.attachment_id + '">', n += '<div class="col-md-10">', n += '<div class="pull-left"><i class="attachment-icon-preview fa fa-file-o"></i></div>', n += '<a href="' + site_url + "download/file/sales_attachment/" + t.key + '" target="_blank">' + t.file_name + "</a>", n += '<p class="text-muted">' + t.filetype + "</p>", n += "</div>", n += '<div class="col-md-2 text-right">', n += '<a href="#" class="text-danger" onclick="' + a + "(" + t.attachment_id + '); return false;"><i class="fa fa-times"></i></a>', n += "</div>", n += '<div class="clearfix"></div><hr/>', n += "</div>", $("#sales_uploaded_files_preview").append(n))
        }
    })), $("body").on("click", ".invoice-send-to-client", function(e) {
        e.preventDefault(), $("#invoice_send_to_client_modal").modal("show")
    }), $("body").on("click", ".estimate-send-to-client", function(e) {
        e.preventDefault(), $("#estimate_send_to_client_modal").modal("show")
    }), $("body").on("click", ".close-send-template-modal", function() {
        $("#estimate_send_to_client_modal").modal("hide"), $("#proposal_send_to_customer").modal("hide")
    }), $("body").on("change", "#include_shipping", function() {
        var e = $("#shipping_details");
        !0 === $(this).prop("checked") ? e.removeClass("hide") : e.addClass("hide")
    }), $("body").on("click", ".save-shipping-billing", function(e) {
        init_billing_and_shipping_details()
    }), $("body").on("change", 'select[name="currency"]', function() {
        init_currency()
    }), $("body").on("change", 'input[name="adjustment"],select.tax', function() {
        calculate_total()
    }), $("body").on("click", ".discount-total-type", function(e) {
        e.preventDefault(), $("#discount-total-type-dropdown").find(".discount-total-type").removeClass("selected"), $(this).addClass("selected"), $(".discount-total-type-selected").html($(this).text()), $(this).hasClass("discount-type-percent") ? ($(".input-discount-fixed").addClass("hide").val(0), $(".input-discount-percent").removeClass("hide")) : ($(".input-discount-fixed").removeClass("hide"), $(".input-discount-percent").addClass("hide").val(0), $("#discount_percent-error").remove()), calculate_total()
    }), $("body").on("change", 'select[name="discount_type"]', function() {
        "" === $(this).val() && $('input[name="discount_percent"]').val(0), calculate_total()
    }), $("body").on("change", 'input[name="discount_percent"],input[name="discount_total"]', function() {
        if ("" === $('select[name="discount_type"]').val() && 0 != $(this).val()) return alert("You need to select discount type"), $("html,body").animate({
            scrollTop: 0
        }, "slow"), $("#wrapper").highlight($('label[for="discount_type"]').text()), setTimeout(function() {
            $("#wrapper").unhighlight()
        }, 3e3), !1;
        !0 === $(this).valid() && calculate_total()
    }), $("body").on("change", ".invoice #project_id", function() {
        var t = $(this).selectpicker("val");
        if ("" !== t) requestGetJSON("tasks/get_billable_tasks_by_project/" + t).done(function(e) {
            _init_tasks_billable_select(e, t)
        });
        else {
            var e = $("#clientid").selectpicker("val");
            "" !== e ? requestGetJSON("tasks/get_billable_tasks_by_customer_id/" + e).done(function(e) {
                _init_tasks_billable_select(e)
            }) : _init_tasks_billable_select([], "")
        }
    }), $("body").on("change", 'select[name="task_select"]', function() {
        "" !== $(this).selectpicker("val") && add_task_to_preview_as_item($(this).selectpicker("val"))
    }), $("body").on("change", 'select[name="paymentmode"]', function() {
        var e = $(".do_not_redirect");
        $.isNumeric($(this).val()) ? e.addClass("hide") : e.removeClass("hide")
    }), $("body").on("change", ".f_client_id #clientid", function() {
        var e = $(this).val(),
            o = $('select[name="project_id"]'),
            t = o.html("").clone(),
            l = $(".projects-wrapper");
        if (o.selectpicker("destroy").remove(), o = t, $("#project_ajax_search_wrapper").append(t), init_ajax_project_search_by_customer_id(), clear_billing_and_shipping_details(), !e) return $("#merge").empty(), $("#expenses_to_bill").empty(), $("#invoice_top_info").addClass("hide"), l.addClass("hide"), !1;
        var a = $("body").find('input[name="merge_current_invoice"]').val();
        requestGetJSON("invoices/client_change_data/" + e + "/" + (a = void 0 === a ? "" : a)).done(function(e) {
            $("#merge").html(e.merge_info);
            var t = $("#expenses_to_bill");
            for (var a in 0 === t.length ? e.expenses_bill_info = "" : t.html(e.expenses_bill_info), "" !== e.merge_info || "" !== e.expenses_bill_info ? $("#invoice_top_info").removeClass("hide") : $("#invoice_top_info").addClass("hide"), billingAndShippingFields) - 1 < billingAndShippingFields[a].indexOf("billing") && (-1 < billingAndShippingFields[a].indexOf("country") ? $('select[name="' + billingAndShippingFields[a] + '"]').selectpicker("val", e.billing_shipping[0][billingAndShippingFields[a]]) : -1 < billingAndShippingFields[a].indexOf("billing_street") ? $('textarea[name="' + billingAndShippingFields[a] + '"]').val(e.billing_shipping[0][billingAndShippingFields[a]]) : $('input[name="' + billingAndShippingFields[a] + '"]').val(e.billing_shipping[0][billingAndShippingFields[a]]));
            for (var i in empty(e.billing_shipping[0].shipping_street) || $('input[name="include_shipping"]').prop("checked", !0).change(), billingAndShippingFields) - 1 < billingAndShippingFields[i].indexOf("shipping") && (-1 < billingAndShippingFields[i].indexOf("country") ? $('select[name="' + billingAndShippingFields[i] + '"]').selectpicker("val", e.billing_shipping[0][billingAndShippingFields[i]]) : -1 < billingAndShippingFields[i].indexOf("shipping_street") ? $('textarea[name="' + billingAndShippingFields[i] + '"]').val(e.billing_shipping[0][billingAndShippingFields[i]]) : $('input[name="' + billingAndShippingFields[i] + '"]').val(e.billing_shipping[0][billingAndShippingFields[i]]));
            init_billing_and_shipping_details();
            var n = e.client_currency,
                s = $("body").find('.accounting-template select[name="currency"]');
            0 != (n = parseInt(n)) ? s.val(n) : s.val(s.data("base")), _init_tasks_billable_select(e.billable_tasks, o.selectpicker("val")), !0 === e.customer_has_projects ? l.removeClass("hide") : l.addClass("hide"), s.selectpicker("refresh"), init_currency()
        })
    }), 0 === $("body").find('input[name="isedit"]').length && $('.f_client_id select[name="clientid"]').change(), $("body").on("click", "#get_shipping_from_customer_profile", function(e) {
        e.preventDefault();
        var t = $("#include_shipping");
        !1 === t.prop("checked") && (t.prop("checked", !0), $("#shipping_details").removeClass("hide"));
        var a = $("#clientid").val();
        "" !== a && requestGetJSON("clients/get_customer_billing_and_shipping_details/" + a).done(function(e) {
            $('textarea[name="shipping_street"]').val(e[0].shipping_street), $('input[name="shipping_city"]').val(e[0].shipping_city), $('input[name="shipping_state"]').val(e[0].shipping_state), $('input[name="shipping_zip"]').val(e[0].shipping_zip), $('select[name="shipping_country"]').selectpicker("val", e[0].shipping_country)
        })
    }), "undefined" != typeof accounting && (accounting.settings.currency.precision = app.options.decimal_places, accounting.settings.number.thousand = app.options.thousand_separator, accounting.settings.number.decimal = app.options.decimal_separator, accounting.settings.number.precision = app.options.decimal_places, calculate_total()), $("body").on("change", 'input[name="invoices_to_merge[]"]', function() {
        var e = $(this).prop("checked"),
            a = $(this).val();
        !0 === e ? requestGetJSON("invoices/get_merge_data/" + a).done(function(e) {
            $.each(e.items, function(e, t) {
                "" !== t.rel_type && ("task" == t.rel_type ? $('input[name="task_id"]').val(t.item_related_formatted_for_input) : "expense" == t.rel_type && $('input[name="expense_id"]').val(t.item_related_formatted_for_input)), _set_item_preview_custom_fields_array(t.custom_fields), add_item_to_table(t, "undefined", a)
            })
        }) : $("body").find('[data-merge-invoice="' + a + '"]').remove()
    }), $("body").on("change", 'input[name="bill_expenses[]"]', function() {
        var e = $(this).prop("checked"),
            t = $(this).val();
        !0 === e ? requestGetJSON("invoices/get_bill_expense_data/" + t).done(function(e) {
            $('input[name="expense_id"]').val(t), add_item_to_table(e, "undefined", "undefined", t)
        }) : ($("body").find('[data-bill-expense="' + t + '"]').remove(), $("body").find('#billed-expenses input[value="' + t + '"]').remove())
    }), $("body").on("change", ".invoice_inc_expense_additional_info input", function() {
        var e, t = $(this).attr("data-content"),
            a = $("[data-bill-expense=" + $(this).attr("data-id") + "] .item_long_description");
        current_desc_val = a.val(), current_desc_val = current_desc_val.trim(), "" !== t && (!0 === $(this).prop("checked") ? (e = current_desc_val + "\n" + t, a.val(e.trim())) : (a.val(current_desc_val.replace("\n" + t, "")), a.val(current_desc_val.replace(t, ""))))
    })
}), $(document).keyup(function(e) {
    27 == e.keyCode && ($(".popup-wrapper").is(":visible") && $(".popup-wrapper").find(".system-popup-close").click(), $("#search-history").is(":visible") && $("#search-history").removeClass("display-block"))
}), $("#newsfeed").scroll(function(e) {
    var t = $(e.currentTarget);
    t[0].scrollHeight - t.scrollTop() == t.outerHeight() && load_newsfeed(), $("#newsfeed .close_newsfeed").css("top", $(this).scrollTop() + 20 + "px")
});


// $("#recovery-label").text("%");
// $("#withheld-label").text("%");
function billableAmountFunction(value) {
    var subtotaltext = $(".total").text();
    var subtotalamount = subtotaltext.replace(/[^\d.-]/g, '');
    var value = parseInt(value);
    // alert(subtotaltext + " - " + subtotalamount);

    var subvalue = subtotalamount * (value / 100);
    var fvalue = subtotalamount - subvalue;
    // alert(fvalue);
    $("#billable-amount-text").text("SAR" + subvalue.toFixed(2));
    $("#billable-amount-field").val(subvalue.toFixed(2));

    var taxrate = 0;
    taxrate = subvalue * (15 / 100);

    $("#cutom-tax-amount-text").text("SAR" + taxrate.toFixed(2));
    $("#cutom-tax-amount-field").val(taxrate.toFixed(2));
    // otherDeductionsFunction($("#other-deductions-field").val());
}

function recoveryOfAdvancePayment(value) {
    var subtotaltext = $(".total").text();
    var subtotalamount = subtotaltext.replace(/[^\d.-]/g, '');
    var value = parseInt(value);
    // alert(subtotaltext + " - " + subtotalamount);
    if ($("#recovery-label").text() == '%') {
        var subvalue = subtotalamount * (value / 100);
        var fvalue = subtotalamount - subvalue;
        // alert(fvalue);
        $("#recovery-payment-total-text").text("SAR" + subvalue.toFixed(2));
        $("#recovery-payment-total-field").val(subvalue.toFixed(2));
        $("#grand-total-text").text("SAR" + fvalue.toFixed(2));
        $("#grand-total-field").val(fvalue.toFixed(2));
    } else if ($("#recovery-label").text() == 'Fixed Amount') {
        var fvalue = subtotalamount - value;
        // alert(fvalue + " - " + subtotalamount + " - " + value);
        $("#recovery-payment-total-text").text("SAR" + value.toFixed(2));
        $("#recovery-payment-total-field").val(value.toFixed(2));
        $("#grand-total-text").text("SAR" + fvalue.toFixed(2));
        $("#grand-total-field").val(fvalue.toFixed(2));
    }
    amountWithheldAsRetention($("#withheld_payment").val());
    // otherDeductionsFunction($("#other-deductions-field").val());
}

function amountWithheldAsRetention(value) {
    // recoveryOfAdvancePayment($("#recovery-payment-total-field").val());
    var subtotaltext = $(".total").text();
    var subtotalamount = subtotaltext.replace(/[^\d.-]/g, '') - $("#recovery-payment-total-field").val();
    // var grandtotal = $("#grand-total-field").val();
    // alert(subtotalamount + " - " + value);
    var value = parseInt(value);
    // alert(recoverypayment);
    if ($("#withheld-label").text() == '%') {
        var subvalue = subtotalamount * (value / 100);
        var fvalue = subtotalamount - subvalue;
        // alert(subvalue + " - " + fvalue);
        $("#withheld-payment-total-text").text("SAR" + subvalue.toFixed(2));
        $("#withheld-payment-total-field").val(subvalue.toFixed(2));
        $("#grand-total-text").text("SAR" + fvalue.toFixed(2));
        $("#grand-total-field").val(fvalue.toFixed(2));
    } else if ($("#withheld-label").text() == 'Fixed Amount') {
        var fvalue = subtotalamount - value;
        // alert(fvalue + " - " + subtotalamount + " - " + value);
        $("#withheld-payment-total-text").text("SAR" + value.toFixed(2));
        $("#withheld-payment-total-field").val(value.toFixed(2));
        $("#grand-total-text").text("SAR" + fvalue.toFixed(2));
        $("#grand-total-field").val(fvalue.toFixed(2));
    }
    // recoveryOfAdvancePayment($("#recovery-payment-total-field").val());
    // otherDeductionsFunction($("#other-deductions-field").val());
}

function otherDeductionsFunction(value) {
    // var grandtotal = $("#grand-total-field").val();
    var subtotaltext = $(".total").text();
    var subtotalamount = subtotaltext.replace(/[^\d.-]/g, '') - $("#recovery-payment-total-field").val() - $("#withheld-payment-total-field").val();
    var value = parseInt(value);
    var fvalue = subtotalamount - value;
    // alert(subtotaltext.replace(/[^\d.-]/g, '') + " - " + $("#recovery-payment-total-field").val() + " = " + $("#withheld-payment-total-field").val());
    // alert(subtotalamount + " - " + value + " = " + fvalue);
    // alert(grandtotal + " - " + value);
    $("#other-deductions-text").text("SAR" + value.toFixed(2));
    $("#other-deductions-field").val(value.toFixed(2));
    $("#grand-total-text").text("SAR" + fvalue.toFixed(2));
    $("#grand-total-field").val(fvalue.toFixed(2));
}

function updateAmountLabel(label, type) {
    // alert(label);
    if (type == 'r') {
        $("#recovery-label").text(label);
        $("#recovery_label_field").val(label);
        $("#recovery_payment").val("0");
    } else if (type == 'w') {
        $("#withheld-label").text(label);
        $("#withheld_label_field").val(label);
        $("#withheld_payment").val("0");
    }
}
updateAmountLabel('%', 'r');
updateAmountLabel('%', 'w');