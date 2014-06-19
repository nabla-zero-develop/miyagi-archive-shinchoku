$(function () {
    "use strict";

    function updateShinchokuTable(data, table) {
        var headers = [];

        $.each(table.find("thead > tr > th"), function (key, value) {
            headers.push("<th>" + value.innerHTML + "</th>");
        });

        table.find("thead > tr").empty().append(headers.join(""));

        var records = [];

        $.each(data, function (key, value) {
            var row = "<tr>";

            for (var name in value) {
                row += "<td>" + value[name] + "</td>";
            }

            row += "</tr>";

            records.push(row);
        });

        table.children("tbody").append(records.join(""));

        table.tablesorter();

        table.find("+ div + img").addClass("gone");
        if (data.length == 0) {
            table.find("+ div").append("指定された日付の進捗はありません");
        }
    }

    function onSelectDate(date) {
        var table = $(this).parent().find(".shinchoku-area > .tablesorter");

        table.children("tbody").empty();
        table.find("+ div").empty();
        table.find("+ div + img").removeClass("gone");

        $.ajax({
            url: "shinchoku_ajax.php",
            cache: false,
            dataType: "json",
            data: {
                categoryid: {department: 1, municipalities: 2, digital_team: 3}[table.parent().parent().attr("id")],
                date: date
            },
            success: function (data) {
                updateShinchokuTable(data, table);
            }
        });
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

    function showResultDialog(message) {
        var resultDialog = $("#upload_result");
        resultDialog.empty().append(message);
        resultDialog.dialog("open");
    }

    function previewCsvFile(event) {
        var data = $.parse(event.target.result, {delimiter: ",", header: true});

        var fields = data.results.fields;
        var rows = data.results.rows;

        if (data.errors.length > 0 || fields.length != 5) {
            showResultDialog("データ書式が間違っています。アップロードできません。");
            return;
        }

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

    function isCsvExtension(filename) {
        var ext = filename.split(".");
        var len = ext.length;
        return len !== 0 && ext[len - 1].toLowerCase() === "csv";
    }

    $("#csv_select").change(function () {
        if (!window.FileReader) {
            // CSV preview is not supported.
            showUploadDialog();
            return;
        }

        var file = $(this).prop('files')[0];

        if (!isCsvExtension(file.name)) {
            showResultDialog("CSVファイル以外はアップロードできません。");
            return;
        }

        var reader = new FileReader();
        reader.onload = previewCsvFile;
        reader.readAsText(file, "Shift_JIS");
    });

    var uploadData = null;

    $("#csv_select").fileupload({
        url: 'upload/index.php',
        dataType: 'json',
        add: function (e, data) {
            // IE8対応: IE8の場合、このイベントまで来ないとファイル名が取得できない
            if (!window.FileReader && !isCsvExtension(data.files[0].name)) {
                $("#csv_dialog").dialog("close");
                showResultDialog("CSVファイル以外はアップロードできません。");
                return;
            }

            uploadData = data;
        },
        done: function (e, data) {
            $("#uploading").dialog("close");

            var file = data.result.files[0];
            showResultDialog(
                !file.error ? "アップロードが完了しました: " + file.name : "アップロードに失敗しました: " + file.error
            );
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
