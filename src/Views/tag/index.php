<h2 class="text-2xl font-semibold mb-4">Tags</h2>
<ul class="pl-5 space-y-2">
    <?php foreach ($tags as $tag): ?>
        <li class="bg-white rounded shadow-sm p-4 flex justify-between items-center">
			<a href="/contacts?tag=<?php echo $tag['id']; ?>" class="text-blue-500 hover:text-blue-700"><?php echo htmlspecialchars($tag['name']); ?></a>
            <div>
                <a href="/tags/edit/<?php echo $tag['id']; ?>" class="text-yellow-600 hover:text-yellow-800 mr-2">Edit</a>
                <a href="/tags/delete/<?php echo $tag['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<a href="/tags/create" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">Add Tag</a>
