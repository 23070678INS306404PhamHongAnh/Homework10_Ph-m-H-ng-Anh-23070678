<?php
/**
 * form.php
 * Contact form shared by add.php and edit.php (sticky + error messages).
 * Expects: $values, $errors, $formAction, $submitLabel.
 */
?>
<form method="post" action="<?= e($formAction) ?>" class="card form" novalidate>
    <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
        <label for="name">Name <span class="required">(required)</span></label>
        <input type="text" id="name" name="name" maxlength="100" value="<?= e($values['name']) ?>">
        <?php if (isset($errors['name'])): ?><p class="error-text"><?= e($errors['name']) ?></p><?php endif; ?>
    </div>

    <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="150" value="<?= e($values['email']) ?>">
        <?php if (isset($errors['email'])): ?><p class="error-text"><?= e($errors['email']) ?></p><?php endif; ?>
    </div>

    <div class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" maxlength="20" value="<?= e($values['phone']) ?>">
        <?php if (isset($errors['phone'])): ?><p class="error-text"><?= e($errors['phone']) ?></p><?php endif; ?>
    </div>

    <div class="field <?= isset($errors['group_name']) ? 'has-error' : '' ?>">
        <label for="group_name">Group</label>
        <select id="group_name" name="group_name">
            <?php foreach (CONTACT_GROUPS as $group): ?>
                <option value="<?= e($group) ?>" <?= $values['group_name'] === $group ? 'selected' : '' ?>>
                    <?= e($group) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['group_name'])): ?><p class="error-text"><?= e($errors['group_name']) ?></p><?php endif; ?>
    </div>

    <div class="field <?= isset($errors['notes']) ? 'has-error' : '' ?>">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4"><?= e($values['notes']) ?></textarea>
        <?php if (isset($errors['notes'])): ?><p class="error-text"><?= e($errors['notes']) ?></p><?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
        <a href="index.php" class="btn btn-ghost">Cancel</a>
    </div>
</form>
