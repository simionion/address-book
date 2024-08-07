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
		<a href="/contacts" class="text-lg hover:text-blue-300 mr-4">Contacts</a>
		<a href="/groups" class="text-lg hover:text-blue-300 mr-4">Groups</a>
		<a href="/tags" class="text-lg hover:text-blue-300 mr-4">Tags</a>
	</nav>
</header>
<div class="flex ">
	<!-- Sidebar -->
    <?php isset($__sidebar) && include $__sidebar; ?>

	<!-- Main Content -->
	<main class="w-3/4 p-4">
        <?php isset($__content) && include $__content; ?>
	</main>
</div>
</body>
</html>
