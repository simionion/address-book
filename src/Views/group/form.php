<h2 class="text-2xl font-semibold mb-4"><?php echo isset($group) ? 'Edit' : 'Add'; ?> Group</h2>
<a href="/groups" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to list</a>
<form method="post" action="/groups/<?php echo isset($group) ? 'edit/' . $group['id'] : 'create'; ?>" class="bg-white p-6 rounded shadow-sm space-y-4">
	<div>
        <?php if (!empty($groups)) { ?>
			<label class="block text-lg font-medium mb-2">Parent Groups:
				<select name="parent_group_ids[]" class="w-full p-2 border rounded" multiple>
                    <?php foreach ($groups as $parentGroup): ?>
						<option value="<?php echo $parentGroup['id']; ?>" <?php echo (isset($group) && in_array($parentGroup['id'], array_column($group['parent_groups'], 'id'))) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parentGroup['name']); ?>
						</option>
                    <?php endforeach; ?>
				</select>
			</label>
        <?php } ?>
	</div>

	<div>
		<label class="block text-lg font-medium mb-2">Group Name:
			<input type="text" name="name" value="<?php echo isset($group) ? htmlspecialchars($group['name']) : ''; ?>" class="w-full p-2 border rounded" required>
		</label>
	</div>

    <?php if (isset($group)) { ?>
		<div>
			<label class="block text-lg font-medium mb-2">Child Groups:
				<select name="child_group_ids[]" class="w-full p-2 border rounded" multiple>
                    <?php foreach ($groups as $childGroup): ?>
						<option value="<?php echo $childGroup['id']; ?>" <?php echo (isset($group) && in_array($childGroup['id'], array_column($group['child_groups'], 'id'))) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($childGroup['name']); ?>
						</option>
                    <?php endforeach; ?>
				</select>
			</label>
		</div>
    <?php } ?>

	<button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        <?php echo isset($group) ? 'Update' : 'Create'; ?>
	</button>
</form>
