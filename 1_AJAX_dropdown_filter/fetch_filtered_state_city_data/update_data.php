<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data in Table Using AJAX</title>

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
            <div class="row justify-content-center ">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h3>Update Data</h3>
                            <div class="form-group">
                                <label for="p_username">Username<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="p_username" id="p_username">
                                <input type="text" class="form-control" name="p_no" id="p_no" hidden>
                            </div>

                            <div class="form-group mt-3">
                                <label for="p_tmg">Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="p_tmg" id="p_tmg">
                            </div>

                            <div class="form-group mt-3">
                                <label for="p_title">Post Title<span class="text-danger">*</span></label>
                                <select class="form-select" name="p_title" id="p_title">
                                    <option hidden disabled selected>Select Option</option>
                                    <option value="Advertisement">Advertisement</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Education">Education</option>
                                    <option value="Fashion">Fashion</option>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label for="p_status">Status<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="p_status" id="p_status">
                            </div>

                            <div class="form-group mt-3">
                                <label for="state">State<span class="text-danger">*</span></label>
                                <select class="form-select state" name="state" id="state">
                                    <option hidden disabled selected>Select State</option>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label for="city">City<span class="text-danger">*</span></label>
                                <select class="form-select city" name="city" id="city">
                                    <option hidden disabled selected>Select City</option>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" name="submit" id="submit" class="btn btn-primary">UPDATE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        function statesLoadedCallback() {
            fetchData();
            stateSelect.addEventListener('change', loadCities);
        }

        var stateCodeMap = {};

        function loadStates(callback) {
            const selectedCountryCode = config.defaultCountryCode;

            stateSelect.innerHTML = '<option value="" hidden disabled selected>Select State</option>';

            fetch(`${config.cUrl}/${selectedCountryCode}/states`, {
                    headers: {
                        "X-CSCAPI-KEY": config.ckey
                    }
                })
                .then(Response => Response.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));

                    data.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.name;
                        option.textContent = state.name;
                        stateSelect.appendChild(option);

                        stateCodeMap[state.name] = state.iso2;
                    });

                    if (typeof callback === 'function') {
                        callback();
                    }

                })
                .catch(error => console.error('Error loading States: ', error));
        }
        loadStates(statesLoadedCallback);

        var cityCodeMap = {}; // Add this mapping
        function loadCities() {
            const selectedCountryCode = config.defaultCountryCode;
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
                    console.log('loadcity Data: ', data);
                    data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.name;
                        option.textContent = city.name;
                        citySelect.appendChild(option);

                        // Add the city name and ISO2 code to the mapping
                        cityCodeMap[city.name] = city.iso2;
                    });
                })
                .catch(error => console.error('Error loading Cities: ', error));
        }
    </script>

    <!-- Fetch and Display Data -->
    <script>
        function fetchData() {
            $.ajax({
                type: "GET",
                url: "api/fetch_data.php",
                success: function(data) {
                    let res = JSON.parse(data);

                    if (res.error_flag === 0) {
                        displayData(res.data);
                    } else {
                        alert('Failed to fetch data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Request Failed:", status, error);
                    alert('Failed to fetch data');
                }
            });
        }

        function displayData(data) {
            var response = data[0];

            $("#p_username").val(response.p_username);
            $("#p_tmg").val(response.p_tmg);
            $("#p_title").val(response.p_title);
            $("#p_status").val(response.p_status);
            $("#state").val(response.state);
            $("#city").val(response.city);
            console.log('response data', response.city)
        }

        $(document).ready(function() {
            fetchData();
        });
    </script>

    <!-- Update data -->
    <script>
        $(document).ready(function() {
            $("button#submit").on("click", function(e) {
                e.preventDefault();

                var p_no = $("input[name='p_no']").val();
                var p_username = $("input[name='p_username']").val();
                var p_tmg = $("input[name='p_tmg']").val();
                var p_title = $("#p_title").val();
                var p_status = $("input[name='p_status']").val();
                var state = $("#state").val();
                var city = $("#city").val();

                // console.log(p_username + p_tmg + p_title + p_status + state + city);

                // Call the updateData function
                updateData();

                function updateData() {
                    $.ajax({
                        type: "POST",
                        url: "api/update_data.php",
                        data: {
                            p_no: p_no,
                            p_username: p_username,
                            p_tmg: p_tmg,
                            p_title: p_title,
                            p_status: p_status,
                            state: state,
                            city: city
                        },
                        success: function(data) {
                            let res = JSON.parse(data);
                            console.log(res);
                            alert('Data updated successfully')
                            window.location.href = 'index.php'
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Request Failed:", status, error);
                            alert('Failed to update data');
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>