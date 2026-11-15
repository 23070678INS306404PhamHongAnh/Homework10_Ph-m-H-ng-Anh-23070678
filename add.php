<?php
/**
 * add.php
 * Create a new contact: show the form, validate server-side, insert with PDO.
 */
require_once __DIR__ . '/config.php';

$errors = [];
$values = ['name' => '', 'email' => '', 'phone' => '', 'group_name' => 'General', 'notes' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = read_contact_input();
    $errors = validate_contact($pdo, $values);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO contacts (name, email, phone, group_name, notes)
                 VALUES (:name, :email, :phone, :group_name, :notes)'
            );
            $stmt->execute([
                'name'       => $values['name'],
                'email'      => null_if_empty($values['email']),
                'phone'      => null_if_empty($values['phone']),
                'group_name' => $values['group_name'],
                'notes'      => null_if_empty($values['notes']),
            ]);
            set_flash('Contact added successfully!');
            redirect('index.php');
        } catch (PDOException $ex) {
            // 23000 = duplicate key (someone saved the same email a moment ago)
            if ($ex->getCode() === '23000') {
                $errors['email'] = 'This email is already used by another contact.';
            } else {
                throw $ex;
            }
        }
    }
}

$pageTitle  = 'Add contact';
$formAction = 'add.php';
$submitLabel = 'Add contact';
include __DIR__ . '/header.php';
?>

<div class="page-head"><h1>Add New Contact</h1></div>

<?php if (!empty($errors)): ?>
    <div class="flash flash-error" role="alert">Please fix the highlighted fields and try again.</div>
<?php endif; ?>

<?php include __DIR__ . '/form.php'; ?>

<?php include __DIR__ . '/footer.php'; ?>
