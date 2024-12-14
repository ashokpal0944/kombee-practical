<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		<title>{{ config('app.name', 'Laravel') }}</title>
		
		<!-- Scripts -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		
		<!-- Fonts -->
		<link rel="dns-prefetch" href="//fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
		
		<link href="{{url('sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{url('jquery-toast/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
		
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		
		
		<!-- Styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<style>
			.custom-nav {
			display: flex;
			justify-content: center;
			gap: 15px; /* Space between the items */
			}
			
			.nav-item .nav-link {
			text-align: center;
			font-size: 16px;
			}
			
			.navbar-nav .ms-auto {
			margin-left: auto; /* Aligns user dropdown to the right */
			}
		</style>
	</head>
	<body>
		<div id="app">
			<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
				<div class="container">
					<a class="navbar-brand" href="{{ url('/') }}">
						{{ config('app.name', 'Laravel') }}
					</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
						<span class="navbar-toggler-icon"></span>
					</button>
					
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<!-- Left Side Of Navbar -->
						<ul class="navbar-nav me-auto">
							
						</ul>
						
						<!-- Right Side Of Navbar -->
						<ul class="navbar-nav mx-auto custom-nav">
							<!-- Authentication Links -->
							@guest
							@if (Route::has('login'))
							<li class="nav-item">
								<a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
							</li>
							@endif
							
							@if (Route::has('register'))
							<li class="nav-item">
								<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
							</li>
							@endif
							@else
							<li class="nav-item">
								<a class="nav-link" href="{{ url('user-list') }}">{{ __('User') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('role-list') }}">{{ __('Role') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('supplier-list') }}">{{ __('Suppliers') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ url('customers-list') }}">{{ __('Customers') }}</a>
							</li>
							<li class="nav-item dropdown ms-auto">
								<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
									{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
								</a>
								<div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="{{ route('logout') }}"
									onclick="event.preventDefault();
									document.getElementById('logout-form').submit();">
										{{ __('Logout') }}
									</a>
									<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
										@csrf
									</form>
								</div>
							</li>
							@endguest
						</ul>
						
					</div>
				</div>
			</nav>
			<main class="py-4">
				@yield('content')
			</main>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		 <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
		<script src="{{url('sweetalert2/sweetalert2.min.js') }}"></script>
		<script src="{{url('jquery-toast/jquery.toast.min.js') }}"></script>
		 <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
		<script>
			$('.select2').select2({
				placeholder: 'Select an option',
				allowClear: true
			});
			
			let modalOpen = false;
            function closemodal()
            {
                setTimeout(function()
                {
                    modalOpen = false;
				},1000)
			}
			
			function toastrMsg(type,msg)
			{
				$.toast({
					text: msg, 
					position: "top-right",
					loaderBg: "#da8609",
					icon: type,
					hideAfter: 3e3,
					stack: 1
				})
			}
		</script>
		@stack('js')
		<div id="modal-view-render"> </div> 
	</body>
</html>
