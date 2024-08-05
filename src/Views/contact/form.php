<h2 class="text-2xl font-semibold mb-4"><?php echo isset($contact) ? 'Edit' : 'Add'; ?> Contact</h2>
<form method="post" action="/contacts/<?php echo isset($contact) ? 'edit/' . $contact['id'] : 'create'; ?>" class="bg-white p-6 rounded shadow-sm space-y-4">
    <div>
        <label class="block text-lg font-medium mb-2">Name:
            <input type="text" name="name" value="<?php echo isset($contact) ? htmlspecialchars($contact['name']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <div>
        <label class="block text-lg font-medium mb-2">First Name:
            <input type="text" name="first_name" value="<?php echo isset($contact) ? htmlspecialchars($contact['first_name']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <div>
        <label class="block text-lg font-medium mb-2">Email:
            <input type="email" name="email" value="<?php echo isset($contact) ? htmlspecialchars($contact['email']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <div>
        <label class="block text-lg font-medium mb-2">Street:
            <input type="text" name="street" value="<?php echo isset($contact) ? htmlspecialchars($contact['street']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <div>
        <label class="block text-lg font-medium mb-2">Zip Code:
            <input type="text" name="zip_code" value="<?php echo isset($contact) ? htmlspecialchars($contact['zip_code']) : ''; ?>" class="w-full p-2 border rounded">
        </label>
    </div>
    <div>
        <label class="block text-lg font-medium mb-2">City:
            <select name="city_id" class="w-full p-2 border rounded">
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city['id']; ?>" <?php echo (isset($contact) && $contact['city_id'] == $city['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($city['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        <?php echo isset($contact) ? 'Update' : 'Create'; ?>
    </button>
</form>
<a href="/contacts" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to list</a>
