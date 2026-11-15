<?php
/**
 * delete.php
 * GET  -> show a confirmation page with the contact details.
 * POST -> actually delete (so crawlers following links cannot delete data).
 */
require_once __DIR__ . '/config.php';

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id     = filter_input($isPost ? INPUT_POST : INPUT_GET, 'id', FILTER_VALIDATE_INT);
$contact = $id ? fetch_contact($pdo, $id) : false;

$pageTitle = 'Delete contact';

if (!$contact) {
    http_response_code(404);
    include __DIR__ . '/header.php';
    ?>
    <div class="card empty">
        <h1>Contact not found</h1>
        <p>This contact does not exist or was already deleted.</p>
        <a href="index.php" class="btn btn-primary">Back to contacts</a>
    </div>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

if ($isPost) {
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = :id');
    $stmt->execute(['id' => (int) $contact['id']]);
    set_flash('Contact deleted successfully!');
    redirect('index.php');
}

include __DIR__ . '/header.php';
?>

<div class="card confirm-box">
    <h1>Delete this contact?</h1>
    <p>Are you sure you want to delete this contact? This cannot be undone.</p>

    <dl class="details">
        <dt>Name</dt>  <dd><?= e($contact['name']) ?></dd>
        <dt>Email</dt> <dd><?= e($contact['email']) ?: '&ndash;' ?></dd>
        <dt>Phone</dt> <dd><?= e($contact['phone']) ?: '&ndash;' ?></dd>
        <dt>Group</dt> <dd><?= e($contact['group_name']) ?></dd>
        <dt>Notes</dt> <dd><?= nl2br(e($contact['notes'])) ?: '&ndash;' ?></dd>
    </dl>

    <form method="post" action="delete.php" class="form-actions">
        <input type="hidden" name="id" value="<?= (int) $contact['id'] ?>">
        <button type="submit" class="btn btn-danger">Yes, delete contact</button>
        <a href="index.php" class="btn btn-ghost">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
