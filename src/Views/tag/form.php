<h2 class="text-2xl font-semibold mb-4"><?php echo isset($tag) ? 'Edit' : 'Add'; ?> Tag</h2>
<a href="/tags" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to list</a>
<form method="post" action="/tags/<?php echo isset($tag) ? 'edit/' . $tag['id'] : 'create'; ?>" class="bg-white p-6 rounded shadow-sm space-y-4">
    <div>
        <label class="block text-lg font-medium mb-2">Tag Name:
            <input type="text" name="name" value="<?php echo isset($tag) ? htmlspecialchars($tag['name']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        <?php echo isset($tag) ? 'Update' : 'Create'; ?>
    </button>
</form>
