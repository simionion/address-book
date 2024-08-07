<h2 class="text-2xl font-semibold mb-4">Groups</h2>
<ul class="pl-5 space-y-2">
    <?php foreach ($groups as $group): ?>
		<li class="bg-white rounded shadow-sm p-4 flex justify-between items-center">
			<a href="/contacts?group=<?php echo $group['id']; ?>" class="text-blue-500 hover:text-blue-700"><?php echo htmlspecialchars($group['name']); ?></a>
			<div>
				<a href="/groups/edit/<?php echo $group['id']; ?>" class="text-yellow-600 hover:text-yellow-800 mr-2">Edit</a>
				<a href="/groups/delete/<?php echo $group['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
			</div>
		</li>
    <?php endforeach; ?>
</ul>
<a href="/groups/create" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">Add Group</a>
