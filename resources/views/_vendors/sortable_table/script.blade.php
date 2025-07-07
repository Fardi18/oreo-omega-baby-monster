<!-- jQuery UI -->
<script src="{{ asset('vendors/jquery-ui/jquery-ui-git.js') }}"></script>

<!-- jQuery UI Touch Punch (https://github.com/furf/jquery-ui-touch-punch) - for support mobile -->
<script src="{{ asset('vendors/jquery-ui-touch-punch/jquery.ui.touch-punch.js') }}"></script>

<script>
    $(document).ready(function() {
        if (typeof AjaxSortingURL == 'undefined') {
            alert("You must set variable (AjaxSortingURL)");
        }

        $('.sorted_table').sortable({
            axis: 'y',
            update: function (event, ui) {
                var data_list = $(this).sortable('serialize');
                $.ajax({
                    type: 'POST',
                    url: AjaxSortingURL,
                    data: {
                        _token: '{{ csrf_token() }}',
                        rows: data_list,
                    },
                    success: function(data){
                        console.log('Successfully sort data');
                    },
                    error: function (data, textStatus, errorThrown) {
                        console.log(data);
                        console.log(textStatus);
                        console.log(errorThrown);
                        alert (textStatus+': '+errorThrown);
                    }
                });
            }
        });
    });
</script>