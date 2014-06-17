$(function () {
    "use strict";

    function updateShinchokuTable(data, table) {
        var records = [];

        $.each(data, function (key, value) {
            var row = "<tr>";

            for (var name in value) {
                row += "<td>" + value[name] + "</td>";
            }

            row += "</tr>";

            records.push(row);
        });

        table.children("tbody").empty().append(records.join(""));

        table.trigger("update");
    }

    function onSelectDate(date) {
        var table = $(this).parent().children(".tablesorter");

        $.getJSON(
            "shinchoku_ajax.php",
            {
                categoryid: {department: 1, municipalities: 2, digital_team: 3}[table.parent().attr("id")],
                date: date
            },
            function (data) {
                updateShinchokuTable(data, table);
            }
        );
    }

    function showUploadDialog(table) {
        $("#csv_preview").empty();

        if (!table) {
            $("#csv_preview_not_support").removeClass();
        } else {
            $("#csv_preview_not_support").addClass("gone");
            $("#csv_preview").append(table);
        }

        $("#csv_dialog").dialog("open");
    }

    function previewCsvFile(event) {
        var data = $.parse(event.target.result, {header: true});

        // TODO: format & error check

        var fields = data.results.fields;
        var rows = data.results.rows;

        var thead = "<thead><tr>";
        for (var i = 0; i < fields.length; i++) {
            thead += "<th>" + fields[i] + "</th>";
        }
        thead += "</tr></thead>";

        var tbody = "<tbody>";
        for (var i = 0; i < rows.length; i++) {
            tbody += "<tr>";
            for (var j = 0; j < fields.length; j++) {
                tbody += "<td>" + rows[i][fields[j]] + "</td>";
            }
            tbody += "</tr>";
        }
        tbody += "</tbody>";

        showUploadDialog(thead + tbody);
    }

    $(".tabs").tabs();

    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: onSelectDate
    });

    $(".tablesorter").tablesorter();

    $("#csv_select").change(function () {
        if (!window.FileReader) {
            // CSV preview is not supported.
            showUploadDialog();
            return;
        }

        var reader = new FileReader();
        reader.onload = previewCsvFile;
        reader.readAsText($(this).prop('files')[0], "Shift_JIS");
    });

    var uploadData = null;

    $("#csv_select").fileupload({
        url: 'upload/index.php',
        dataType: 'json',
        add: function (e, data) {
            uploadData = data;
        },
        done: function (e, data) {
            $("#uploading").dialog("close");

            var file = data.result.files[0];
            $("#upload_result").empty().append(
                !file.error ? "アップロードが完了しました: " + file.name : "アップロードに失敗しました: " + file.error
            );

            $("#upload_result").dialog("open");
        }
    });

    $("#csv_dialog").dialog({
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        width: "auto",
        buttons: {
            "アップロード": function() {
                $(this).dialog("close");
                $("#uploading").dialog("open");
                uploadData.submit();
            },
            "キャンセル": function() {
                $(this).dialog("close");
            }
        }
    });

    $("#uploading").dialog({
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        dialogClass: "no-close"
    });

    $("#upload_result").dialog({
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        buttons: {
            "OK": function() {
                $(this).dialog("close");
            }
        }
    });
});
