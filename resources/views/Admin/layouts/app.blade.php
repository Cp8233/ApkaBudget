<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="./img/converted_image.png">
    <title>Apka budget</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('Admin.includes.sidebar')
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('Admin.includes.header')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2025 Apka budget All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('admin.logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    <!-- Page level plugins -->
    {{-- <script src="{{ asset('admin/vendor/chart.js/Chart.mina.js') }}"></script> --}}
    <!-- Page level custom scripts -->
    {{-- <script src="{{ asset('admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('admin/js/demo/chart-pie-demo.js') }}"></script> --}}
    <!-- Page level plugins -->
    <script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/js/demo/datatables-demo.js') }}"></script>
    {{-- <script>
        $(document).ready(function () {
            // ✅ CSRF Token Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            /** ============================
             * ✅ Form Submission (Add Data)
             * ============================ */
            $('#addForm').on('submit', function (e) {
                e.preventDefault();
                $('.error-message, .text-danger').empty();
    
                let formData = new FormData(this);
    
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 1) {
                            alert(response.message);
                            window.location.href = response.route; // ✅ Redirect after success
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        $('.text-danger').empty();
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function (field, messages) {
                                $('#' + field + '_error').html(messages.join('<br>'));
                            });
                        }
                    }
                });
            });
    
           
//              $(document).on("click", ".delete-btn", function () {
//     let rowElement = $(this).closest("tr");
//     let url = $(this).data("url");

//     if (confirm("Are you sure you want to delete this record?")) {
//         $.ajax({
//             url: url,
//             type: "DELETE",
//             headers: {
//                 "X-CSRF-TOKEN": "{{ csrf_token() }}" 
//             },
//             success: function (response) {
//                 if (response.success) {  
//                     alert(response.message);
//                     rowElement.fadeOut(500, function () {
//                         $(this).remove();
//                     });
//                 } else {
//                     alert(response.message);
//                 }
//             },
//             error: function (xhr) {
//                 alert("Failed to delete record. Please try again.");
//                 console.log(xhr.responseText);
//             }
//         });
//     }
// });


$(document).ready(function () {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    Delete Bank Detail
    $('.delete-btn').on('click', function () {
        let url = $(this).data('url');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 1) {
                        alert(response.message);
                        location.reload(); // Refresh the page after successful deletion
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    alert('Failed to delete record. Please try again.');
                }
            });
        }
    });
});
});
    </script>
     --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeXtzgRc95cYuOaZD0fjyHsnqVg9Imf30&libraries=places&callback=initAutocomplete"
        async defer></script>
    <script>
        function initAutocomplete() {
            console.log("Google Maps API Loaded!"); // Debugging

            // ✅ Get the input field
            var input = document.getElementById('address');

            // ✅ Initialize Google Places Autocomplete
            var autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'], // Only show address results
                componentRestrictions: {
                    country: "IN"
                } // Restrict to India
            });

            // ✅ Listener for when user selects a location
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    alert("No details available for this location!");
                    return;
                }

                // ✅ Set Latitude & Longitude in Hidden Fields
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }
    </script>
     <script>
        $(document).ready(function () {
            // ✅ CSRF Token Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            /** ============================
             * ✅ Add Data Form
             * ============================ */
            $('#addForm').on('submit', function (e) {
                e.preventDefault();
                $('.error-message, .text-danger').empty();
                let formData = new FormData(this);
    
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 1) {
                            alert(response.message);
                            window.location.href = response.route;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function (field, messages) {
                                $('#' + field + '_error').html(messages.join('<br>'));
                            });
                        }
                    }
                });
            });
    
            /** ============================
             * ✅ Delete Function
             * ============================ */
            $(document).on('click', '.delete-btn', function () {
                let rowElement = $(this).closest('tr');
                let url = $(this).data('url');
    
                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function (response) {
                            if (response.status === 1) {
                                alert(response.message);
                                rowElement.fadeOut(500, function () {
                                    $(this).remove();
                                });
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function () {
                            alert('Failed to delete record. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
    
<script>
    $(document).ready(function() {
        // ✅ Country Select -> Load States
        $('#country').change(function() {
            var countryID = $(this).val();

            if (countryID) {
                let url = '/admin/get-states/' + countryID;
                console.log("Fetching states from:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        $('#state').html('<option>Loading...</option>'); // Loading indicator
                    },
                    success: function(data) {
                        console.log("States received:", data);
                        $('#state').empty().append('<option value="">-- Select State --</option>');
                        $('#city').empty().append('<option value="">-- Select City --</option>');

                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#state').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('#state').append('<option value="">No states available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching states:", xhr.responseText);
                        alert("Error fetching states. Please try again!");
                    }
                });
            } else {
                $('#state, #city').empty().append('<option value="">-- Select --</option>');
            }
        });

        // ✅ State Select -> Load Cities
        $('#state').change(function() {
            var stateID = $(this).val();
            console.log("Selected state ID:", stateID); // Debugging ke liye

            if (stateID) {
                let url = '/admin/get-cities/' + stateID;
                console.log("Fetching cities from:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        $('#city').html('<option>Loading...</option>'); // Loading indicator
                    },
                    success: function(data) {
                        console.log("Cities received:", data);
                        $('#city').empty().append('<option value="">-- Select City --</option>');

                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#city').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            $('#city').append('<option value="">No cities available</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching cities:", xhr.responseText);
                        alert("Error fetching cities. Please try again!");
                    }
                });
            } else {
                $('#city').empty().append('<option value="">-- Select City --</option>');
            }
        });
        
                    $(document).ready(function() {
                $(document).on('click', '.security-status', function() {
                    var providerId = $(this).data('id');
                    var planType = $(this).data('type');
                    var clickedElement = $(this);

                    if (clickedElement.hasClass('badge-success')) {
                        alert("Security plan is already active!");
                        return;
                    }

                    $('#providerId').val(providerId);
                    $('#planType').val(planType);

                    $.ajax({
                        url: '/admin/get-plans/' + planType,
                        type: 'GET',
                        success: function(response) {
                            var dropdown = $('#planDropdown');
                            dropdown.empty();

                            if (response.plans && response.plans.length > 0) {
                                $.each(response.plans, function(index, plan) {
                                    dropdown.append('<option value="' + plan
                                        .id + '">' +
                                        plan.name + ' - ₹' + plan.price +
                                        '</option>');
                                });
                            } else {
                                dropdown.append(
                                    '<option value="">No plans available</option>');
                            }

                            $('#planModal').modal('show');

                            $('#activatePlanBtn').off('click').on('click', function() {
                                var selectedPlanId = $('#planDropdown').val();

                                if (!selectedPlanId) {
                                    alert("Please select a plan to activate.");
                                    return;
                                }

                                $.ajax({
                                    url: '/admin/activate-security/' +
                                        providerId + '/' +
                                        selectedPlanId,
                                    type: 'GET',
                                    success: function(response) {
                                        if (response.status ===
                                            'success') {

                                            clickedElement
                                                .removeClass(
                                                    'badge-danger')
                                                .addClass(
                                                    'badge-success')
                                                .text('Active');

                                            $('#planModal').modal(
                                                'hide');
                                        } else {
                                            alert(response.message);
                                        }
                                    },
                                    error: function(xhr) {
                                        alert(
                                            "Failed to activate the plan. Please try again."
                                        );
                                    }
                                });
                            });
                        },
                        error: function(xhr) {
                            console.error("Error fetching plans:", xhr.responseText);
                            alert("Failed to load plans. Please try again.");
                        }
                    });
                });
            });
    });
</script>
    <script>
        $(document).ready(function() {
            // Get Subcategories
            $('#category_id').change(function() {
                var categoryId = $(this).val();
                $('#subcategory_id').empty().append('<option value="">-- Select SubCategory --</option>');
                $('#sub_subcategory_id').empty().append(
                    '<option value="">-- Select Sub SubCategory --</option>');
                $('#servicesContainer').empty();

                if (categoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-subcategories') }}/${categoryId}`,
                        type: "GET",
                        success: function(data) {
                            data.forEach(function(value) {
                                $('#subcategory_id').append(
                                    `<option value="${value.id}">${value.name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            // Get Sub Subcategories
            $('#subcategory_id').change(function() {
                var categoryId = $('#category_id').val();
                var subcategoryId = $(this).val();
                $('#sub_subcategory_id').empty().append(
                    '<option value="">-- Select Sub SubCategory --</option>');
                $('#servicesContainer').empty();

                if (subcategoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-sub-subcategories') }}/${categoryId}/${subcategoryId}`,
                        type: "GET",
                        success: function(data) {
                            data.forEach(function(value) {
                                $('#sub_subcategory_id').append(
                                    `<option value="${value.id}">${value.sub_subcategory_name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            // Get Services with Images and Prices
            $('#sub_subcategory_id').change(function() {
                var categoryId = $('#category_id').val();
                var subcategoryId = $('#subcategory_id').val();
                var subSubcategoryId = $(this).val();

                if (subSubcategoryId) {
                    $.ajax({
                        url: `{{ url('admin/get-services') }}/${categoryId}/${subcategoryId}/${subSubcategoryId}`,
                        type: "GET",
                        success: function(data) {
                            $('#servicesContainer').empty();
                            data.forEach(function(value) {
                                let imageUrl = "{{ url('/') }}" + "/" + value
                                    .image;
                                $('#servicesContainer').append(`
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="${imageUrl}" class="card-img-top" alt="Service Image" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">${value.service_name}</h5>
                                            <p class="card-text">Price: ₹ ${value.price}</p>
                                            <input type="checkbox" name="services[]" value="${value.id}"> Select<br>
                                            <small class="text-danger" id="services_error"></small>
                                        </div>
                                    </div>
                                </div>
                            `);
                            });
                        }
                    });
                }
            });
            // Fetch Slots
            $('#slot_date').change(function() {
                const date = $(this).val();
                $.get(`/admin/get-daily-slots?date=${date}`, function(data) {
                    $('#slot_time').empty().append('<option value="">-- Select Slot --</option>');
                    data.slots.forEach(function(slot) {
                        $('#slot_time').append(
                            `<option value="${slot.start_time}-${slot.end_time}">${slot.slot}</option>`
                            );
                    });
                });
            });
        });
    </script>
</body>

</html>
