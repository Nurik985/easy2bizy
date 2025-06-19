<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>EasyBizy</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	@endif
	@stack('styles')
</head>
<body class="bg-[#ccc]">
<header class="fixed w-full">
	<nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800">
		<div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
			<a href="{{ route('campaigns') }}" class="flex items-center">
				<img src="{{ asset('images/logo.svg') }}" class="mr-3 h-6 sm:h-9" alt="EasyBizy" />
				<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"><span style="color:#ff705d">Easy</span>Bizy</span>
			</a>
			<div class="flex items-center lg:order-2">
				<a href="{{ route('settings') }}" class="text-gray-300 dark:text-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-md px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 text-1xl">Настройка</a>
				<form method="POST" action="{{ route('logout') }}">
					@csrf <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 text-1xl">
						Выход
					</button>
				</form>
				<button data-collapse-toggle="mobile-menu-2" type="button" class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mobile-menu-2" aria-expanded="false">
					<span class="sr-only">Меню</span>
					<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
					<svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
				</button>
			</div>
			<div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
				<ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0 text-1xl">
					<li>
						<a href="{{ route('campaigns') }}" class="{{ Request::is('campaigns') ? 'block py-2 pr-4 pl-3 text-white rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-[#ff705c]' : 'block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-[#ff705c] dark:hover:bg-gray-700 dark:hover:text-[#ff705c] lg:dark:hover:bg-transparent dark:border-gray-700' }} " aria-current="page">Кампании</a>
					</li>
					<li>
						<a href="{{ route('reports') }}" class="{{ Request::is('reports') ? 'block py-2 pr-4 pl-3 text-white rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-[#ff705c]' : 'block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-[#ff705c] dark:hover:bg-gray-700 dark:hover:text-[#ff705c] lg:dark:hover:bg-transparent dark:border-gray-700' }} " aria-current="page">Отчеты</a>
					</li>
					<li>
						<a href="{{ route('statistics') }}" class="{{ Request::is('statistics') ? 'block py-2 pr-4 pl-3 text-white rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-[#ff705c]' : 'block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-[#ff705c] dark:hover:bg-gray-700 dark:hover:text-[#ff705c] lg:dark:hover:bg-transparent dark:border-gray-700' }} " aria-current="page">Статистика</a>
					</li>
					<li>
						<a href="{{ route('staff') }}" class="{{ Request::is('staff') ? 'block py-2 pr-4 pl-3 text-white rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 dark:text-[#ff705c]' : 'block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-[#ff705c] dark:hover:bg-gray-700 dark:hover:text-[#ff705c] lg:dark:hover:bg-transparent dark:border-gray-700' }} " aria-current="page">Персонал</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</header>
<main class="flex justify-center h-screen pt-16">
	<div class="w-full max-w-7xl bg-white border border-[#ccc] p-5 bg-gray-100 shadow-lg">
		@yield('content')
	</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
@stack('scripts')
</body>
</html>
