@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
				
                <div class="card-body">
                    <form id="loginForm" method="POST" action="{{ url('user-login') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" autofocus>
                                @error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
                                @enderror
							</div>
						</div>
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password">
                                @error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
                                @enderror
							</div>
						</div>
                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
									</label>
								</div>
							</div>
						</div>
                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
								</button>
								
                                @if (Route::has('password.request'))
								<a class="btn btn-link" href="{{ route('password.request') }}">
									{{ __('Forgot Your Password?') }}
								</a>
                                @endif
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
    $(document).ready(function () {
        // Initialize jQuery Validation for the form
        $('#loginForm').validate({
            rules: {
                email: {
                    required: true,
                    email: true // Ensures the email format is valid
				},
                password: {
                    required: true,
                    minlength: 6
				}
			},
            messages: {
                email: {
                    required: "Please enter your email.",
                    email: "Please enter a valid email address."
				},
                password: {
                    required: "Please enter your password.",
                    minlength: "Password must be at least 6 characters long."
				}
			},
            errorElement: "div", // Use a div for error messages
            errorPlacement: function (error, element) {
                // Add invalid-feedback class and place the error message correctly
                error.addClass("invalid-feedback");
                if (element.hasClass('form-check-input')) {
                    // For checkbox, place error message after the label
                    error.insertAfter(element.closest('.form-check'));
					} else {
                    // For all other inputs, place error message after the element
                    error.insertAfter(element);
				}
			},
            highlight: function (element) {
                // Add invalid class and remove valid class
                $(element).addClass("is-invalid").removeClass("is-valid");
			},
            unhighlight: function (element) {
                // Add valid class and remove invalid class
                $(element).addClass("is-valid").removeClass("is-invalid");
			},
            submitHandler: function (form) {
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
								window.location.href = "{{ url('home') }}";
							}, 1000); // 1000 milliseconds = 1 second
						}
					}
				});
			}
		});
	});
</script>
@endpush
