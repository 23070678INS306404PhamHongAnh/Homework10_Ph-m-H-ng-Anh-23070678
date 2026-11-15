<?php
/**
 * helpers.php
 * Small reusable functions shared by all pages:
 * output escaping, flash messages, input reading and validation.
 */

const CONTACT_GROUPS = ['Family', 'Friends', 'Work', 'Other', 'General'];

/** Escape a value for safe HTML output (prevents XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Store a one-time message in the session. $type: success | error | warning */
function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['message']      = $message;
    $_SESSION['message_type'] = $type;
}

/** Return the flash message and clear it so it is only shown once. */
function pull_flash(): ?array
{
    if (!isset($_SESSION['message'])) {
        return null;
    }
    $flash = [
        'message' => $_SESSION['message'],
        'type'    => $_SESSION['message_type'] ?? 'success',
    ];
    unset($_SESSION['message'], $_SESSION['message_type']);
    return $flash;
}

/** Redirect and stop the script. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Read and trim the contact form fields from $_POST. */
function read_contact_input(): array
{
    $input = [];
    foreach (['name', 'email', 'phone', 'group_name', 'notes'] as $field) {
        $input[$field] = trim((string) filter_input(INPUT_POST, $field, FILTER_UNSAFE_RAW));
    }
    if ($input['group_name'] === '') {
        $input['group_name'] = 'General';
    }
    return $input;
}

/**
 * Validate contact data. Returns an array of errors keyed by field name
 * (empty array = valid). $ignoreId lets Edit keep the contact's own email.
 */
function validate_contact(PDO $pdo, array $values, int $ignoreId = 0): array
{
    $errors = [];

    if ($values['name'] === '') {
        $errors['name'] = 'Name is required.';
    } elseif (mb_strlen($values['name']) > 100) {
        $errors['name'] = 'Name must be 100 characters or fewer.';
    }

    if ($values['email'] !== '') {
        if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address, for example name@example.com.';
        } elseif (mb_strlen($values['email']) > 150) {
            $errors['email'] = 'Email must be 150 characters or fewer.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM contacts WHERE email = :email AND id <> :id');
            $stmt->execute(['email' => $values['email'], 'id' => $ignoreId]);
            if ($stmt->fetch()) {
                $errors['email'] = 'This email is already used by another contact.';
            }
        }
    }

    if ($values['phone'] !== '' && !preg_match('/^[0-9+\-\s().]{6,20}$/', $values['phone'])) {
        $errors['phone'] = 'Phone can only contain digits, spaces, + - ( ) and be 6-20 characters long.';
    }

    if (!in_array($values['group_name'], CONTACT_GROUPS, true)) {
        $errors['group_name'] = 'Choose a group from the list.';
    }

    if (mb_strlen($values['notes']) > 2000) {
        $errors['notes'] = 'Notes must be 2000 characters or fewer.';
    }

    return $errors;
}

/** Load one contact by id, or false when it does not exist. */
function fetch_contact(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Empty strings become NULL so several contacts may have no email/phone. */
function null_if_empty(string $value): ?string
{
    return $value === '' ? null : $value;
}
