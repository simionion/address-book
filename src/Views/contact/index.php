<h2 class="text-2xl font-semibold mb-4">Contact List</h2>

<?php if (empty($contacts)) { ?>
    <p>No contacts found.</p>
<?php } else { ?>
    <ul class="pl-5 space-y-2">
        <?php foreach ($contacts as $contact) { ?>
            <?php include __DIR__ . '/../components/contactItem.php'; ?>
        <?php } ?>
    </ul>
<?php } ?>


<div class="flex justify-between mt-4">
    <a href="/contacts/create" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New Contact</a>
    <div>
        <a href="/contacts/export?format=json" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mr-2" download="contacts.json">Export JSON</a>
        <a href="/contacts/export?format=xml" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" download="contacts.xml">Export XML</a>
    </div>
</div>
<script>
    document.querySelectorAll('.toggle-contact').forEach(button => {
        button.addEventListener('click', () => {
            const contactId = button.getAttribute('data-contact-id');
            const contactDetails = document.getElementById(`contact-${contactId}`);
            contactDetails.classList.toggle('hidden');
        });
    });
</script>
