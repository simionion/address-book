<h2 class="text-2xl font-semibold mb-4">Contact Details</h2>
<div class="bg-white p-6 rounded shadow-sm space-y-2">
    <p class="text-lg"><span class="font-semibold">Name:</span> <?php echo htmlspecialchars($contact['name']); ?></p>
    <p class="text-lg"><span class="font-semibold">First Name:</span> <?php echo htmlspecialchars($contact['first_name']); ?></p>
    <p class="text-lg"><span class="font-semibold">Email:</span> <?php echo htmlspecialchars($contact['email']); ?></p>
    <p class="text-lg"><span class="font-semibold">Street:</span> <?php echo htmlspecialchars($contact['street']); ?></p>
    <p class="text-lg"><span class="font-semibold">Zip Code:</span> <?php echo htmlspecialchars($contact['zip_code']); ?></p>
    <p class="text-lg"><span class="font-semibold">City:</span> <?php echo htmlspecialchars($city['name']); ?></p>
</div>
<a href="/" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to list</a>
