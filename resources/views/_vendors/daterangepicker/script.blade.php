<!-- bootstrap-daterangepicker -->
<script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script>
    /**
     * initial daterangepicker with custom options using parameters
     *
     * @param {string} element_id - id of the element input text
     * @param {string} custom_format - datetime format
     * @param {string} initial_value - set initial value when daterangepicker created (""/today/yesterday/this week/last week/this month/last month/this year/last year)
     * @param {boolean} custom_input - show/hide custom input
     * @param {boolean} show_dropdown - show/hide dropdown for change month/year
     * @param {string} min_date - set minimum date that we can choose (format: dd/mm/yyyy)
     * @param {string} max_date - set maximum date that we can choose (format: dd/mm/yyyy)
     * @param {integer} limit_days - set limit days that we can choose as daterange
     */
    function init_daterangepicker_custom(
        element_id = "daterangepicker", 
        custom_format = "DD/MM/YYYY", 
        initial_value = "", 
        custom_input = true,
        show_dropdown = true,
        min_date = '',
        max_date = '',
        limit_days = 0
    ) {
        if (typeof $.fn.daterangepicker === "undefined") {
            return;
        }
        console.log("init_daterangepicker_custom");

        var cb = function (start, end, label) {
            $("#"+element_id+" span").html(
                start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY")
            );
        };

        var optionSet1 = {
            linkedCalendars: false,
            showDropdowns: show_dropdown,
            showWeekNumbers: false,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker24Hour: true,
            alwaysShowCalendars: false,
            ranges: {
                "Today": [
                    moment(), 
                    moment()
                ],
                "Yesterday": [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "This Week": [
                    moment().startOf('week'), 
                    moment()
                ],
                "Last Week": [
                    moment().subtract(1, "week").startOf("week"),
                    moment().subtract(1, "week").endOf("week"),
                ],
                "This Month": [
                    moment().startOf("month"), 
                    moment()
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                "This Year": [
                    moment().startOf('year'), 
                    moment()
                ],
                "Last Year": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year"),
                ],
            },
            opens: "right",
            showCustomRangeLabel: custom_input,
            buttonClasses: ["btn btn-default"],
            applyClass: "btn-small btn-primary",
            cancelClass: "btn-small",
            format: custom_format,
            separator: " to ",
            locale: {
                applyLabel: "Submit",
                cancelLabel: "Clear",
                fromLabel: "From",
                toLabel: "To",
                customRangeLabel: "Custom",
                daysOfWeek: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
                monthNames: [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December",
                ],
                firstDay: 0,
                format: custom_format,
            },
        };

        if (min_date != '') {
            optionSet1.minDate = min_date;
        }
        if (max_date != '') {
            optionSet1.maxDate = max_date;
        }
        if (limit_days > 0) {
            optionSet1.dateLimit = { days: limit_days };
        }
        
        switch (initial_value) {
            case 'today':
                optionSet1.startDate = moment();
                optionSet1.endDate = moment();
                break;
                
            case 'yesterday':
                optionSet1.startDate = moment().subtract(1, "days");
                optionSet1.endDate = moment().subtract(1, "days");
                break;

            case 'this week':
                optionSet1.startDate = moment().startOf('week');
                optionSet1.endDate = moment();
                break;

            case 'last week':
                optionSet1.startDate = moment().subtract(1, "week").startOf("week");
                optionSet1.endDate = moment().subtract(1, "week").endOf("week");
                break;

            case 'this month':
                optionSet1.startDate = moment().startOf("month");
                optionSet1.endDate = moment();
                break;

            case 'last month':
                optionSet1.startDate = moment().subtract(1, "month").startOf("month");
                optionSet1.endDate = moment().subtract(1, "month").endOf("month");
                break;

            case 'this year':
                optionSet1.startDate = moment().startOf('year');
                optionSet1.endDate = moment();
                break;

            case 'last year':
                optionSet1.startDate = moment().subtract(1, "year").startOf("year");
                optionSet1.endDate = moment().subtract(1, "year").endOf("year");
                break;
        }

        $("#"+element_id+"").daterangepicker(optionSet1, cb);

        $("#"+element_id+"").on("show.daterangepicker", function () {
            console.log("show event fired");
        });
        $("#"+element_id+"").on("hide.daterangepicker", function () {
            console.log("hide event fired");
        });
        $("#"+element_id+"").on("apply.daterangepicker", function (ev, picker) {
            console.log(
                "apply event fired, start/end dates are " +
                    picker.startDate.format("DD/MM/YYYY") +
                    " to " +
                    picker.endDate.format("DD/MM/YYYY")
            );
        });
        $("#"+element_id+"").on("cancel.daterangepicker", function (ev, picker) {
            console.log("cancel event fired");
        });

        $("#options1").click(function () {
            $("#"+element_id+"")
                .data("daterangepicker")
                .setOptions(optionSet1, cb);
        });

        $("#options2").click(function () {
            $("#"+element_id+"")
                .data("daterangepicker")
                .setOptions(optionSet2, cb);
        });

        $("#destroy").click(function () {
            $("#"+element_id+"").data("daterangepicker").remove();
        });

        if (initial_value == '') {
            $("#"+element_id+"").val('');
        }
    }

    $(document).ready(function() {
        // sample to call this function
        // init_daterangepicker_custom('daterangepicker');
    });
</script>