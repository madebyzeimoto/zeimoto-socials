<?php
// ==========================================================
// Snippets :: Components :: Socials
// ==========================================================
//
// Renders resolved social profile links and icons.
//
// ==========================================================

declare(strict_types=1);

$socials = $kirby->collection("socials");
?>

<?php if ($socials->isNotEmpty()): ?>
  <div class="mds-socials">
    <ul class="mds-socials-list">
      <?php foreach ($socials as $social): ?>
        <li class="mds-socials-item">
          <a
            class="mds-socials-button"
            href="<?= html($social->url()) ?>"
            target="_blank"
            rel="noopener"
            title="<?= html(t("follow-us") . " " . $social->name()) ?>"
          >
            <?php if ($social->icon()): ?>
              <i class="mds-icon" aria-hidden="true"><?= $social->icon() ?></i>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
