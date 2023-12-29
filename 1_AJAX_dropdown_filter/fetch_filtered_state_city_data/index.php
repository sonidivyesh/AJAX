<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Filter in Table Using AJAX</title>

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
            <div class="row align-items-center ">
                <div class="col-md-4">
                    <span>Filter by State : </span>
                    <select class="form-select mt-3 state" name="fetchState" id="fetchState">
                        <option hidden disabled selected>Select State</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <span>Filter by City : </span>
                    <select class=" form-select mt-3 city" name="fetchCity" id="fetchCity">
                        <option hidden disabled selected>Select City</option>
                    </select>
                </div>

                <div class="col-md-4 text-end">
                    <span>
                        <a href="#" style="text-decoration: none;" class="text-danger" onclick="clearFilter()">&nbsp;Clear Filter</a>
                    </span>
                </div>
            </div>
        </div>

        <table class="table table-striped" id="example">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Post Title</th>
                    <th>Status</th>
                    <th>State</th>
                    <th>City</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- fetch state & city in dropdown -->
    <script>
        var config = {
            cUrl: 'https://api.countrystatecity.in/v1/countries',
            ckey: 'S2dOdlVEVVRRNmxzOUcyMzJ4VnRsU3VHajZCbWNuTm11czRENVZoSA==',
            defaultCountryCode: 'IN'
        }

        var stateSelect = document.querySelector('.state');
        var citySelect = document.querySelector('.city');

        var stateCodeMap = {}; // Add this mapping
        function loadStates() {
            stateSelect.disabled = false;
            citySelect.disabled = true;
            stateSelect.style.pointerEvents = 'auto';
            citySelect.style.pointerEvents = 'none';

            const selectedCountryCode = config.defaultCountryCode;

            stateSelect.innerHTML = '<option value="" hidden disabled selected>Select State</option>';

            fetch(`${config.cUrl}/${selectedCountryCode}/states`, {
                    headers: {
                        "X-CSCAPI-KEY": config.ckey
                    }
                })
                .then(Response => Response.json())
                .then(data => {
                    // Sort the array of states in ascending order by name
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    // console.log(data);

                    data.forEach(state => {
                        const option = document.createElement('option');
                        // option.value = state.iso2;
                        option.value = state.name;
                        option.textContent = state.name;
                        stateSelect.appendChild(option);

                        // Add the state name and ISO2 code to the mapping
                        stateCodeMap[state.name] = state.iso2;
                    });
                })
                .catch(error => console.error('Error loading States: ', error));
        }

        var cityCodeMap = {}; // Add this mapping
        function loadCities() {
            citySelect.disabled = false;
            citySelect.style.pointerEvents = 'auto';

            const selectedCountryCode = config.defaultCountryCode;
            // const selectedStateCode = stateSelect.value;
            const selectedStateName = stateSelect.value;
            const selectedStateCode = stateCodeMap[selectedStateName]; // Use the mapping

            citySelect.innerHTML = '<option value="" hidden disabled selected>Select City</option>';

            fetch(`${config.cUrl}/${selectedCountryCode}/states/${selectedStateCode}/cities`, {
                    headers: {
                        "X-CSCAPI-KEY": config.ckey
                    }
                })
                .then(Response => Response.json())
                .then(data => {
                    data.forEach(city => {
                        const option = document.createElement('option');
                        // option.value = city.iso2;
                        option.value = city.name; // Use city name as the value
                        option.textContent = city.name;
                        citySelect.appendChild(option);

                        // Add the city name and ISO2 code to the mapping
                        cityCodeMap[city.name] = city.iso2;
                    });
                    // console.log('cities: ', data);
                })
                .catch(error => console.error('Error loading Cities: ', error));
        }

        window.onload = loadStates;
    </script>

    <!-- fetch table data -->
    <script>
        function fetchData() {
            $.ajax({
                url: 'api/fetch_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var responceData = response.data;

                    if (responceData) {
                        $.each(responceData, function(index, e) {
                            fetchedRows = '<tr id="row-' + e.p_no + '">' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + e.p_username + '</td>' +
                                '<td>' + e.p_tmg + '</td>' +
                                '<td>' + e.p_title + '</td>' +
                                '<td>' + e.p_status + '</td>' +
                                '<td>' + e.state + '</td>' +
                                '<td>' + e.city + '</td>' +
                                '</tr>';
                            $('tbody').append(fetchedRows);
                        });
                    } else {
                        // Display a message if no data is available
                        var noDataMessage = '<tr><td colspan="7">Data Not Available</td></tr>';
                        $('tbody').append(noDataMessage);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                }
            });
        };

        fetchData();
    </script>

    <!-- fetch filtered state data -->
    <script>
        function fetchFilteredStateData() {
            $("#fetchState").on('change', function() {
                loadCities()

                var selectedStateValue = $(this).val();
                // alert(selectedStateValue);
                // console.log('sending data:', {
                //     'request': selectedStateValue
                // });

                clearTableRows('example')

                $.ajax({
                    url: 'api/fetch_filtered_state_data.php',
                    type: 'POST',
                    data: {
                        'request': selectedStateValue
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log("API response:", response.data);
                        var responceData = response.data;

                        if (responceData) {
                            $.each(responceData, function(index, e) {
                                filteredRows = '<tr id="row-' + e.p_no + '">' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + e.p_username + '</td>' +
                                    '<td>' + e.p_tmg + '</td>' +
                                    '<td>' + e.p_title + '</td>' +
                                    '<td>' + e.p_status + '</td>' +
                                    '<td>' + e.state + '</td>' +
                                    '<td>' + e.city + '</td>' +
                                    '</tr>';
                                $('tbody').append(filteredRows);
                            });
                        } else {
                            // Display a message if no data is available
                            var noDataMessage = '<tr><td colspan="7">Data is not available for this state</td></tr>';
                            $('tbody').append(noDataMessage);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                    }
                });
            });
        };

        fetchFilteredStateData();
    </script>

    <!-- fetch filtered cities data -->
    <script>
        function fetchFilteredCitiesData() {
            $("#fetchCity").on('change', function() {
                var selectedCityValue = $(this).val();
                // alert(selectedCityValue);
                // console.log('sending data:', {
                //     'request': selectedCityValue
                // });

                clearTableRows('example');

                $.ajax({
                    url: 'api/fetch_filtered_city_data.php',
                    type: 'POST',
                    data: {
                        'request': selectedCityValue
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log("API response:", response.data);
                        var responseData = response.data;

                        if (responseData) {
                            $.each(responseData, function(index, entry) {
                                filteredRows = '<tr id="row-' + entry.p_no + '">' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + entry.p_username + '</td>' +
                                    '<td>' + entry.p_tmg + '</td>' +
                                    '<td>' + entry.p_title + '</td>' +
                                    '<td>' + entry.p_status + '</td>' +
                                    '<td>' + entry.state + '</td>' +
                                    '<td>' + entry.city + '</td>' +
                                    '</tr>';
                                $('tbody').append(filteredRows);
                            });
                        } else {
                            // Display a message if no data is available
                            var noDataMessage = '<tr><td colspan="7">Data is not available for this city</td></tr>';
                            $('tbody').append(noDataMessage);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                    }
                });
            });
        }

        fetchFilteredCitiesData();
    </script>

    <!-- clear table data -->
    <script>
        function clearTableRows(tableId) {
            $('#' + tableId + ' tbody').empty();
        }
    </script>

    <!-- clear filter -->
    <script>
        function clearFilter() {
            clearTableRows('example');
            loadStates();
            $('#fetchCity').empty().append('<option value="" hidden disabled selected>Select City</option>');
            fetchData();
        }
    </script>
</body>

</html>