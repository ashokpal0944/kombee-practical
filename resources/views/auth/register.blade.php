@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>
                <div class="card-body">
					<?php
						$get_countrys = App\Models\Country::all();
						$get_roles = App\Models\Role::where('id','!=',1)->where('status',1)->get();
					?>
                    <form id="registerForm" method="POST" action="{{ route('user-register') }}">
                        @csrf
						<div class="row">
							<div class="col-md-6">
								<label for="first_name" class="form-label">First Name</label>
								<input type="text" class="form-control" id="first_name" name="first_name"  >
							</div>
							<div class="col-md-6">
								<label for="last_name" class="form-label">Last Name</label>
								<input type="text" class="form-control" id="last_name" name="last_name"  >
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-6">
								<label for="email" class="form-label">Email</label>
								<input type="email" class="form-control" id="email" name="email" >
							</div>
							<div class="col-md-6">
								<label for="contact_number	" class="form-label">Contact Number</label>
								<input type="tel" class="form-control" id="contact_number" name="contact_number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" >
							</div>
						</div>
						<div class="row mt-2">
							<!-- Postcode -->
							<div class="col-md-6">
								<label for="postcode" class="form-label">Postcode</label>
								<input type="text" class="form-control" id="postcode" name="postcode"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
							</div>
							<div class="col-md-6">
								<label for="country_id" class="form-label">Country</label>
								<select class="form-control select2" id="country_id" name="country_id" onchange="getStateData()">
									<option value=""> Select Country </option>
									@foreach($get_countrys as $get_country)
									<option value="{{ $get_country->id }}">{{ $get_country->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-6">
								<label for="state_id" class="form-label">State</label>
								<select class="form-control select2" id="state_id" name="state_id" onchange="getCityData()">
									<option value=""> Select Country </option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="city_id" class="form-label">City</label>
								<select class="form-control select2" id="city_id" name="city_id">
									<option value=""> Select Country </option>
								</select>
							</div>
						</div>
						<div class="row mt-2">
							
							<!-- File Upload -->
							<div class="col-md-6">
								<label for="files" class="form-label">Upload Files</label>
								<input type="file" class="form-control" id="files" name="files[]" multiple >
							</div>
							<!-- Hobbies -->
							<div class="col-md-6">
								<label class="form-label">Hobbies</label><br>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="hobbyReading" name="hobbies[]" value="Reading">
									<label class="form-check-label" for="hobbyReading">Reading</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="hobbyTraveling" name="hobbies[]" value="Traveling">
									<label class="form-check-label" for="hobbyTraveling">Traveling</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="hobbyGaming" name="hobbies[]" value="Gaming">
									<label class="form-check-label" for="hobbyGaming">Gaming</label>
								</div>
							</div>
						</div>
						<!-- Gender -->
						<div class="row mt-2">
							<div class="col-md-6">
								<label class="form-label">Gender</label><br>
								<div class="form-check">
									<input class="form-check-input" type="radio" id="genderMale" name="gender" value="Male" checked>
									<label class="form-check-label" for="genderMale">Male</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" id="genderFemale" name="gender" value="Female" >
									<label class="form-check-label" for="genderFemale">Female</label>
								</div>
							</div>
						</div>
						<!-- Role Dropdown -->
						<div class="row mt-2">
							<div class="col-md-6">
								<label for="password" class="form-label">Password</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							<div class="col-md-6">
								<label for="confirm_password" class="form-label">Confirm Password</label>
								<input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-12">
								<label for="role_data" class="form-label">Role</label>
								<select class="form-select" id="role_data" name="role_data[]" multiple style="width: 100%;">
									<option value="">Select Roles</option>
									@foreach($get_roles as $get_role)
									<option value="{{ $get_role->id }}">{{ $get_role->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="row mb-2 mt-2">
						<div class="col-md-6 offset-md-4">
							<button type="submit" class="btn btn-primary">
								{{ __('Register') }}
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
@endsection

@push('js')
<script>
	function getStateData()
	{
		var country_id = $('#country_id').val();
		var _token = $('input[name="_token"]').val();
		
		$.ajax({
			url:"{{ url('get-state-data') }}",
			method:"POST",
			data:{country_id:country_id, _token:_token},
			success:function(result)
			{
				$('#state_id').html(result);
			}
		})
	}
	
	function getCityData()
	{
		var state_id = $('#state_id').val();
		var _token = $('input[name="_token"]').val();
		
		$.ajax({
			url:"{{ url('get-city-data') }}",
			method:"POST",
			data:{state_id:state_id, _token:_token},
			success:function(result)
			{
				$('#city_id').html(result);
			}
		})
	}
	
	
	$(document).ready(function () {
		// Initialize the select2 for roles
		$('#role_data').select2({
			placeholder: "Select roles",
			allowClear: true
		});
		
		$.validator.addMethod("regex", function (value, element, regex) {
			return this.optional(element) || regex.test(value);
		}, "Invalid format.");
		
		// Initialize the form validation
		$('#registerForm').validate({
			rules: {
				first_name: {
					required: true,
					regex: /^[a-zA-Z0-9\s'-]+$/
				},
				last_name: {
					required: true,
					regex: /^[a-zA-Z0-9\s'-]+$/
				},
				email: {
					required: true,
					email: true,
					remote: {
						url: '{{ url('check-email') }}',
						type: 'POST',
						data: {
							email: function () {
								return $('#email').val();
							},
							_token: $('meta[name="csrf-token"]').attr('content')
						}
					}
				},
				contact_number: {
					required: true,
					digits: true,
					minlength: 10,
					maxlength: 15,
					remote: {
						url: '{{ url('check-contact') }}',
						type: 'POST',
						data: {
							contact_number: function () {
								return $('#contact_number').val();
							},
							_token: $('meta[name="csrf-token"]').attr('content')
						}
					}
				},
				postcode: {
					required: true,
					digits: true,
					minlength: 5,
					maxlength: 6
				},
				country_id: {
					required: true
				},
				state_id: {
					required: true
				},
				city_id: {
					required: true
				},
				password: {
					required: true,
					minlength: 6
				},
				confirm_password: {
					required: true,
					equalTo: "#password" // Ensure it matches the password field
				},
				"role_data[]": {
					required: true,  // Check if at least one role is selected
					minlength: 1 // Ensures at least one option is selected
				}
			},
			messages: {
				first_name: {
					required: "Please enter your first name.",
					regex: "First name can only contain letters, numbers, spaces, apostrophes, and hyphens."
				},
				last_name: {
					required: "Please enter your last name.",
					regex: "Last name can only contain letters, spaces, apostrophes, and hyphens."
				},
				email: {
					required: "Please enter a valid email address.",
					email: "Please enter a valid email address.",
					remote: "This email is already in use. Please choose another."
				},
				contact_number: {
					required: "Please enter your contact number.",
					digits: "Contact number must contain only digits.",
					minlength: "Contact number must be at least 10 digits.",
					maxlength: "Contact number can be up to 15 digits.",
					remote: "This contact number is already in use. Please choose another."
				},
				postcode: {
					required: "Please enter your postcode.",
					digits: "Postcode must contain only digits.",
					minlength: "Postcode must be at least 5 digits.",
					maxlength: "Postcode can be up to 6 digits."
				},
				country_id: {
					required: "Please select a country."
				},
				state_id: {
					required: "Please select a state."
				},
				city_id: {
					required: "Please select a city."
				},
				password: {
					required: "Please enter a password.",
					minlength: "Password must be at least 6 characters."
				},
				confirm_password: {
					required: "Please confirm your password.",
					equalTo: "Passwords do not match."
				},
				"role_data[]": {
					required: "Please select at least one role.", // Custom message when no role is selected
					minlength: "Please select at least one role." // Ensures that at least one option is selected
				}
			},
			errorElement: "div",
			errorPlacement: function (error, element) {
				// Place the error message after the element
				error.addClass("invalid-feedback");
				error.insertAfter(element);
			},
			highlight: function (element) {
				$(element).addClass("is-invalid").removeClass("is-valid");
			},
			unhighlight: function (element) {
				$(element).addClass("is-valid").removeClass("is-invalid");
			},
			submitHandler: function (form, event) {
				event.preventDefault(); // Prevent the default form submission
				
				var $form = $(form);
				var $submitButton = $form.find('button');
				var $spinButton = $form.find('button.spin-btn');
				
				$submitButton.prop('disabled', true);
				$spinButton.addClass('loading'); 
				
				var formData = new FormData(form);
				formData.append('_token', "{{ csrf_token() }}");
				
				$.ajax({
					async: true,
					type: $form.attr('method'),
					url: $form.attr('action'),
					data: formData,
					cache: false,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(res) {
						$submitButton.prop('disabled', false);
						$spinButton.removeClass('loading');
						
						if (res.status === "error") {
							toastrMsg(res.status, res.msg);
							} else if (res.status === "validation") {
							$('.error').remove(); // Clear previous error messages
							
							$.each(res.errors, function(key, value) {
								var inputField = $('#' + key);
								var errorSpan = $('<span>')
                                .addClass('error text-danger')
                                .attr('id', key + 'Error')
                                .text(value[0]);
								inputField.parent().append(errorSpan);
							});
							} else {
							toastrMsg(res.status, res.msg);
							$('body').css({'overflow': 'auto'});
							
							// Reset the form after successful submission
							$form[0].reset();
							
							// Reset Select2 or any plugin-dependent fields
							$form.find('select').val('').trigger('change'); 
							$('.error').remove();
							
							// Redirect to the login page
							setTimeout(function() {
								window.location.href = "{{ url('login') }}";
							}, 1000); // 1000 milliseconds = 1 second
						}
					}
				});
			}
		});
		
		// Trigger validation on select change
		$('#role_data').on('change', function () {
			// Trigger validation on change of the role selection
			$(this).valid();
		});
	});
	
	
	
	
	/* $('#registerForm').submit(function(event) {
		event.preventDefault();
		
		var $form = $(this);
		var $submitButton = $form.find('button');
		var $spinButton = $form.find('button.spin-btn');
		
		$submitButton.prop('disabled', true);
		$spinButton.addClass('loading'); 
		
		var formData = new FormData(this);
		formData.append('_token', "{{ csrf_token() }}");
		
		$.ajax({
		async: true,
		type: $form.attr('method'),
		url: $form.attr('action'),
		data: formData,
		cache: false,
		processData: false,
		contentType: false,
		dataType: 'Json',
		success: function(res) {
		$submitButton.prop('disabled', false);
		$spinButton.removeClass('loading');
		
		if (res.status === "error") {
		toastrMsg(res.status, res.msg);
		} else if (res.status === "validation") {
		$('.error').remove(); // Clear previous error messages
		
		$.each(res.errors, function(key, value) {
		var inputField = $('#' + key);
		var errorSpan = $('<span>')
		.addClass('error text-danger')
		.attr('id', key + 'Error')
		.text(value[0]);
		inputField.parent().append(errorSpan);
		});
		} else {
		toastrMsg(res.status, res.msg);
		$('body').css({'overflow': 'auto'});
		
		// Reset the form after successful submission
		$form[0].reset();
		
		// Reset Select2 or any plugin-dependent fields
		$form.find('select').val('').trigger('change'); 
		$('.error').remove();
		// Redirect to the login page
		setTimeout(function() {
		window.location.href = "{{ url('login') }}";
		}, 1000); // 3000 milliseconds = 3 seconds
		}
		}
		});
	}); */
	
	
	
</script>
@endpush
