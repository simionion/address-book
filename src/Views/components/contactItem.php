<li class="bg-white rounded shadow-sm">
	<button class="w-full text-left focus:outline-none toggle-contact p-4" data-contact-id="<?php echo $contact['id']; ?>">
        <span class="font-semibold">
            <?php echo htmlspecialchars($contact['name']) . ' ' . htmlspecialchars($contact['first_name']); ?>
            <?php if (!empty($contact['groups']) || !empty($contact['tags'])): ?>
		        <span class="text-sm text-gray-600">
                    <?php if (!empty($contact['groups'])): ?>
                        <?php foreach ($contact['groups'] as $group): ?>
		                    <span class="inline-block bg-blue-600 text-white text-xs font-semibold ml-3 px-2 py-0.5 rounded"><?php echo htmlspecialchars($group['name']); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($contact['tags'])): ?>
                        <?php foreach ($contact['tags'] as $tag): ?>
					        <span class="inline-block bg-gray-600 text-white text-xs font-semibold ml-3 px-2 py-0.5 rounded"><?php echo htmlspecialchars($tag['name']); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </span>
		<div id="contact-<?php echo $contact['id']; ?>" class="hidden mt-2">
			<p>Email: <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="text-blue-500"><?php echo htmlspecialchars($contact['email']); ?></a></p>
			<p>Street: <?php echo htmlspecialchars($contact['street']); ?></p>
			<p>Zip Code: <?php echo htmlspecialchars($contact['zip_code']); ?></p>
			<p>City: <?php echo htmlspecialchars($contact['city_name']); ?></p>
			<div class="mt-2 space-x-2">
				<a href="/contacts/edit/<?php echo $contact['id']; ?>" class="text-yellow-600 hover:text-yellow-800">Edit</a>
				<a href="/contacts/delete/<?php echo $contact['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
			</div>
		</div>
	</button>
</li>
