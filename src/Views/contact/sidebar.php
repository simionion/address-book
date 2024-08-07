<aside class="w-1/4 bg-gray-100 p-4 border-r border-gray-200">
	<section>
		<h3 class="text-xl font-semibold mb-4">Groups</h3>
		<ul>
			<li class="mb-2">
				<a href="/contacts" class="text-blue-500 hover:text-blue-700 <?php echo !isset($_GET['group']) && !isset($_GET['tag']) ? 'font-bold' : ''; ?>">All Contacts</a>
			</li>
            <?php foreach ($groupsInUse as $group): ?>
				<li class="mb-2">
					<a href="/contacts?group=<?php echo $group->id; ?>" class="text-blue-500 hover:text-blue-700 <?php echo (isset($_GET['group']) && $_GET['group'] == $group->id) ? 'font-bold' : ''; ?>"><?php echo htmlspecialchars($group->name); ?></a>
				</li>
            <?php endforeach; ?>
		</ul>
		<a href="/groups/create" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">Add Group</a>
	</section>
	<section class="mt-8">
		<h3 class="text-xl font-semibold mb-4">Tags</h3>
		<ul>
            <?php foreach ($tagsInUse as $tag): ?>
				<li class="mb-2">
					<a href="/contacts?tag=<?php echo $tag->id; ?>" class="text-blue-500 hover:text-blue-700 <?php echo (isset($_GET['tag']) && $_GET['tag'] == $tag->id) ? 'font-bold' : ''; ?>"><?php echo htmlspecialchars($tag->name); ?></a>
				</li>
            <?php endforeach; ?>
		</ul>
		<a href="/tags/create" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">Add Tag</a>
	</section>
</aside>
