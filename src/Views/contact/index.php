<h2 class="text-2xl font-semibold mb-4">Contact List</h2>
<ul class="pl-5 space-y-2">
    <?php foreach ($contacts as $contact): ?>
        <li class="bg-white rounded shadow-sm">
            <button class="w-full text-left focus:outline-none toggle-contact p-4" data-contact-id="<?php echo $contact['id']; ?>">
                <span class="font-semibold"><?php echo htmlspecialchars($contact['name']) . ' ' . htmlspecialchars($contact['first_name']); ?></span>
                <div id="contact-<?php echo $contact['id']; ?>" class="hidden mt-2">
                    <p>Email: <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></p>
                    <p>Street: <?php echo htmlspecialchars($contact['street']); ?></p>
                    <p>Zip Code: <?php echo htmlspecialchars($contact['zip_code']); ?></p>
                    <p>City: <?php echo htmlspecialchars($cities[$contact['city_id'] - 1]['name']); ?></p>
                    <div class="mt-2 space-x-2">
                        <a href="/contacts/edit/<?php echo $contact['id']; ?>" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                        <a href="/contacts/delete/<?php echo $contact['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
                    </div>
                </div>
            </button>
        </li>
    <?php endforeach; ?>
</ul>
<a href="/contacts/create" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New Contact</a>

<script>
document.querySelectorAll('.toggle-contact').forEach(button => {
    button.addEventListener('click', () => {
        const contactId = button.getAttribute('data-contact-id');
        const contactDetails = document.getElementById(`contact-${contactId}`);
        contactDetails.classList.toggle('hidden');
    });
});
</script>
