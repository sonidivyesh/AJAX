<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdoen Filter in Table Using AJAX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #filters {
            margin-top: 2%;
            margin-bottom: 2%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="filters">
            <span>Filter : </span>
            <select name="fetchval" id="fetchval">
                <option hidden disabled selected>Select Option</option>
                <option value="Advertisement">Advertisement</option>
                <option value="Technology">Technology</option>
                <option value="Education">Education</option>
                <option value="Fashion">Fashion</option>
            </select>
            <span><a href="#" style="text-decoration: none;" class="text-danger" onclick="clearFilter()">&nbsp;Clear Filter</a></span>
        </div>

        <table class="table table-striped" id="example">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Post Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


    <!-- Fetch Data -->
    <script>
        function fetchData() {
            $.ajax({
                url: 'api/fetch_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log("API response:", response);
                    var responceData = response.data;

                    if (responceData.length > 0) {
                        $.each(responceData, function(index, e) {
                            fetchedRows = '<tr id="row-' + e.p_no + '">' +
                                '<td>' + (index + 1) + '</td>' +
                                // '<td>' + e.p_no + '</td>' +
                                '<td>' + e.p_username + '</td>' +
                                '<td>' + e.p_tmg + '</td>' +
                                '<td>' + e.p_title + '</td>' +
                                '<td>' + e.p_status + '</td>' +
                                '</tr>';
                            $('tbody').append(fetchedRows);
                        });
                    } else {
                        console.log("Data is empty");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                }
            });
        };

        fetchData();
    </script>

    <!-- Fetch Filtered Data -->
    <script>
        function fetchFilteredData() {
            $("#fetchval").on('change', function() {
                var value = $(this).val();
                // alert(value);
                clearTableRows('example')

                $.ajax({
                    url: 'api/fetch_filtered_data.php',
                    type: 'POST',
                    data: 'request=' + value,
                    dataType: 'json',
                    success: function(response) {
                        // console.log("API response:", response.data);
                        var responceData = response.data;

                        if (responceData.length > 0) {
                            $.each(responceData, function(index, e) {
                                filteredRows = '<tr id="row-' + e.p_no + '">' +
                                    '<td>' + (index + 1) + '</td>' +
                                    // '<td>' + e.p_no + '</td>' +
                                    '<td>' + e.p_username + '</td>' +
                                    '<td>' + e.p_tmg + '</td>' +
                                    '<td>' + e.p_title + '</td>' +
                                    '<td>' + e.p_status + '</td>' +
                                    '</tr>';
                                $('tbody').append(filteredRows);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                    }
                });
            });
        };

        fetchFilteredData();
    </script>

    <!-- Clear Table -->
    <script>
        function clearTableRows(tableId) {
            $('#' + tableId + ' tbody').empty();
        }
    </script>

    <!-- Clear Filter -->
    <script>
        function clearFilter() {
            clearTableRows('example');
            $('#fetchval').val("Select Option");
            fetchData();
        }
    </script>
</body>

</html>