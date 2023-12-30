<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Data in Table Using AJAX</title>

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
                            <h3>Insert Data</h3>
                            <div class="form-group">
                                <label for="p_username">Username<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="p_username" id="p_username" required>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <label for="p_tmg">Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="p_tmg" id="p_tmg" required>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <label for="p_title">Post Title<span class="text-danger">*</span></label>
                                <select class="form-select" name="p_title" id="p_title" required>
                                    <option hidden disabled selected>Select Option</option>
                                    <option value="Advertisement">Advertisement</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Education">Education</option>
                                    <option value="Fashion">Fashion</option>
                                </select>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <label for="p_status">Status<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="p_status" id="p_status" required>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <label for="state">State<span class="text-danger">*</span></label>
                                <select class="form-select state" name="state" id="state" required>
                                    <option hidden disabled selected>Select State</option>
                                </select>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <label for="city">City<span class="text-danger">*</span></label>
                                <select class="form-select city" name="city" id="city" required>
                                    <option hidden disabled selected>Select City</option>
                                </select>
                            </div>
                            <p class="error-message text-danger"></p>

                            <div class="form-group mt-3">
                                <button type="submit" name="submit" id="submit" class="btn btn-primary">SUBMIT</button>
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

        stateSelect.addEventListener('change', function() {
            loadCities();
        });

        window.onload = loadStates;
    </script>

    <!-- Insert data -->
    <script>
        $(document).ready(function() {
            $("button#submit").on("click", function(e) {
                e.preventDefault();

                if (validateForm()) {

                    var p_username = $("input[name='p_username']").val();
                    var p_tmg = $("input[name='p_tmg']").val();
                    var p_title = $("#p_title").val();
                    var p_status = $("input[name='p_status']").val();
                    var state = $("#state").val();
                    var city = $("#city").val();

                    // console.log(p_username + p_tmg + p_title + p_status + state + city);

                    $.ajax({
                        type: "POST",
                        url: "api/add_data.php",
                        data: {
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
                            if (res['error_flag'] == 0) {
                                alert('Data inserted successfully')
                                window.location.href = 'index.php'
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Request Failed:", status, error);
                            alert('Failed to add data');
                        }
                    });
                }
            });

            function validateForm() {
                // Validate required fields
                var isValid = true;
                $('input[required]').each(function() {
                    if ($(this).val().trim() === "") {
                        $(this).addClass("is-invalid");
                        isValid = false;
                        $(this).next('.error-message').text('*This field is required.');
                    } else {
                        $(this).removeClass("is-invalid");
                        $(this).next('.error-message').text('');
                    }
                });

                // Validate dropdowns
                $('select').each(function() {
                    if ($(this).val() === null) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                        $(this).next('.error-message').text('*Please select an option.');
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').text('');
                    }
                });

                return isValid;
            }
        });
    </script>
</body>

</html>