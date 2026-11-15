<?php
/**
 * index.php
 * List page: total count, search by name/email, sortable table.
 */
require_once __DIR__ . '/config.php';

// --- Read search and sort options from the query string ---------------
$search = trim((string) filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW));

// Whitelist: URL value => real column name (user input never reaches the SQL).
$sortColumns = [
    'name'       => 'name',
    'email'      => 'email',
    'phone'      => 'phone',
    'group'      => 'group_name',
    'created_at' => 'created_at',
];
$sortKey = (string) filter_input(INPUT_GET, 'sort', FILTER_UNSAFE_RAW);
if (!isset($sortColumns[$sortKey])) {
    $sortKey = 'name';
}
$sortDir = strtolower((string) filter_input(INPUT_GET, 'dir', FILTER_UNSAFE_RAW)) === 'desc' ? 'DESC' : 'ASC';

// --- Query the database (prepared statements only) --------------------
$totalContacts = (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();

$sql    = 'SELECT id, name, email, phone, group_name, created_at FROM contacts';
$params = [];
if ($search !== '') {
    $sql .= ' WHERE name LIKE :search_name OR email LIKE :search_email';
    $like = '%' . addcslashes($search, '%_\\') . '%';
    $params['search_name']  = $like;
    $params['search_email'] = $like;
}
$sql .= ' ORDER BY ' . $sortColumns[$sortKey] . ' ' . $sortDir . ', id ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

/** Build a header link that sorts by $key and flips direction if already active. */
function sort_link(string $label, string $key, string $currentKey, string $currentDir, string $search): string
{
    $nextDir = ($key === $currentKey && $currentDir === 'ASC') ? 'desc' : 'asc';
    $query   = http_build_query(['search' => $search, 'sort' => $key, 'dir' => $nextDir]);
    $arrow   = $key === $currentKey ? ($currentDir === 'ASC' ? ' &#9650;' : ' &#9660;') : '';
    return '<a href="index.php?' . e($query) . '">' . e($label) . $arrow . '</a>';
}

$pageTitle = 'All contacts';
include __DIR__ . '/header.php';
?>

<div class="page-head">
    <div>
        <h1>Contacts</h1>
        <p class="muted">
            <?= $totalContacts ?> contact<?= $totalContacts === 1 ? '' : 's' ?> in total
            <?php if ($search !== ''): ?>
                &middot; <?= count($contacts) ?> match<?= count($contacts) === 1 ? '' : 'es' ?>
                for &ldquo;<?= e($search) ?>&rdquo;
            <?php endif; ?>
        </p>
    </div>
    <a href="add.php" class="btn btn-primary">Add New Contact</a>
</div>

<form method="get" action="index.php" class="search-bar">
    <input type="hidden" name="sort" value="<?= e($sortKey) ?>">
    <input type="hidden" name="dir" value="<?= e(strtolower($sortDir)) ?>">
    <input type="search" name="search" placeholder="Search by name or email" value="<?= e($search) ?>" aria-label="Search contacts">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search !== ''): ?>
        <a href="index.php" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($contacts)): ?>
    <div class="card empty">
        <?php if ($search !== ''): ?>
            <p>No contacts match &ldquo;<?= e($search) ?>&rdquo;. Try a different name or email.</p>
        <?php else: ?>
            <p>You have no contacts yet. Add your first one to get started.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="contacts-table">
            <thead>
                <tr>
                    <th><?= sort_link('Name', 'name', $sortKey, $sortDir, $search) ?></th>
                    <th><?= sort_link('Email', 'email', $sortKey, $sortDir, $search) ?></th>
                    <th><?= sort_link('Phone', 'phone', $sortKey, $sortDir, $search) ?></th>
                    <th><?= sort_link('Group', 'group', $sortKey, $sortDir, $search) ?></th>
                    <th><?= sort_link('Created At', 'created_at', $sortKey, $sortDir, $search) ?></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td data-label="Name"><?= e($contact['name']) ?></td>
                    <td data-label="Email"><?= e($contact['email']) ?: '&ndash;' ?></td>
                    <td data-label="Phone"><?= e($contact['phone']) ?: '&ndash;' ?></td>
                    <td data-label="Group"><span class="badge badge-<?= e(strtolower((string) $contact['group_name'])) ?>"><?= e($contact['group_name']) ?></span></td>
                    <td data-label="Created At"><?= e(date('d/m/Y H:i', strtotime($contact['created_at']))) ?></td>
                    <td data-label="Actions" class="actions">
                        <a href="edit.php?id=<?= (int) $contact['id'] ?>">Edit</a>
                        <span class="sep">|</span>
                        <a href="delete.php?id=<?= (int) $contact['id'] ?>" class="danger-link">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
