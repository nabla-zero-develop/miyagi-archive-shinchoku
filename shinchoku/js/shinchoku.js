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

    function showUploadCancel() {
        $("#upload, #cancel").removeClass("gone");
    }

    function goneUploadCancel() {
        $("#upload, #cancel").addClass("gone");
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

        $("#csv_preview").empty().append(thead + tbody);

        showUploadCancel();
    }

    $(".tabs").tabs();

    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: onSelectDate
    });

    $(".tablesorter").tablesorter();

    $("#csv_upload").change(function () {
        $("#result").empty();

        if (!window.FileReader) {
            // CSV preview is not supported.
            showUploadCancel();
            return;
        }

        var reader = new FileReader();
        reader.onload = previewCsvFile;
        reader.readAsText($(this).prop('files')[0], "Shift_JIS");
    });

    $("#csv_upload").fileupload({
        url: 'upload/index.php',
        dataType: 'json',
        done: function (e, data) {
            var file = data.result.files[0];
            goneUploadCancel();
            if (!file.error) {
                $("#result").append("アップロードが完了しました: " + file.name);
            } else {
                $("#result").append("アップロードに失敗しました: " + file.error);
            }
        }
    });
});
