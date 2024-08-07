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
