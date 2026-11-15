<?php
/**
 * edit.php
 * Update an existing contact. The id comes from the query string.
 */
require_once __DIR__ . '/config.php';

$id      = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$contact = $id ? fetch_contact($pdo, $id) : false;

$pageTitle = 'Edit contact';

// --- Contact does not exist -------------------------------------------
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

$errors = [];
$values = [
    'name'       => $contact['name'],
    'email'      => (string) $contact['email'],
    'phone'      => (string) $contact['phone'],
    'group_name' => (string) $contact['group_name'],
    'notes'      => (string) $contact['notes'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = read_contact_input();
    $errors = validate_contact($pdo, $values, (int) $contact['id']);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE contacts
                 SET name = :name, email = :email, phone = :phone,
                     group_name = :group_name, notes = :notes
                 WHERE id = :id'
            );
            $stmt->execute([
                'name'       => $values['name'],
                'email'      => null_if_empty($values['email']),
                'phone'      => null_if_empty($values['phone']),
                'group_name' => $values['group_name'],
                'notes'      => null_if_empty($values['notes']),
                'id'         => (int) $contact['id'],
            ]);
            set_flash('Contact updated successfully!');
            redirect('index.php');
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors['email'] = 'This email is already used by another contact.';
            } else {
                throw $ex;
            }
        }
    }
}

$formAction  = 'edit.php?id=' . (int) $contact['id'];
$submitLabel = 'Save changes';
include __DIR__ . '/header.php';
?>

<div class="page-head"><h1>Edit Contact</h1></div>

<?php if (!empty($errors)): ?>
    <div class="flash flash-error" role="alert">Please fix the highlighted fields and try again.</div>
<?php endif; ?>

<?php include __DIR__ . '/form.php'; ?>

<?php include __DIR__ . '/footer.php'; ?>
