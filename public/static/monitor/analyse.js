$(function () {
    Morris.Line({
        element: "morris-one-line-chart",
        data: 
            [{year: "2008", value: 5}, {year: "2009", value: 10}, {year: "2010", value: 8}, {
            year: "2011",
            value: 22
        }, {year: "2012", value: 8}, {year: "2014", value: 10}, {year: "2015", value: 5}],
        xkey: "year",
        ykeys: ["value"],
        resize: !0,
        lineWidth: 4,
        labels: ["Value"],
        lineColors: ["#1ab394"],
        pointSize: 5
    })
});