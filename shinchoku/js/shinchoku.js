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

    function previewCvsFile(event) {
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

        $("#cvspreview").empty().append(thead + tbody);
    }

    $(".tabs").tabs();

    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: onSelectDate
    });

    $(".tablesorter").tablesorter();

    $("#cvsupload").change(function () {
        if (!window.FileReader) {
            // CVS preview function is not supported.
            return;
        }

        var reader = new FileReader();
        reader.onload = previewCvsFile;
        reader.readAsText($(this).prop('files')[0], "Shift_JIS");
    });
});
