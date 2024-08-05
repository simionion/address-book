<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Address Book</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<header class="bg-blue-600 text-white p-4 shadow-md">
	<h1 class="text-3xl font-bold">Address Book</h1>
	<nav class="mt-2">
		<a href="/contacts" class="text-lg hover:text-blue-300 mr-4">List</a>
		<a href="/contacts/create" class="text-lg hover:text-blue-300">Add New Contact</a>
	</nav>
</header>
<main class="p-4">
    <?php include $__content; ?>
</main>

</body>
</html>
