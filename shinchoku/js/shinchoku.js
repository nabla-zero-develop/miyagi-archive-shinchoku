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

    $(".tabs").tabs();

    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: onSelectDate
    });

    $(".tablesorter").tablesorter();
});
