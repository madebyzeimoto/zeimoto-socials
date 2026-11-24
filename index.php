<?php
// ==========================================================
// Plugins :: zeimoto :: Socials
// ==========================================================
//
// Owns social profiles, their Panel fields, public rendering, and default icons.
//
// ==========================================================

declare(strict_types=1);

$flatten = require __DIR__ . "/languages/flatten-blueprint-translations.php";

Kirby::plugin("zeimoto/socials", [
  "collections" => [
    "socials" => require __DIR__ . "/collections/socials.php",
  ],
  "snippets" => [
    "components/socials" => __DIR__ . "/snippets/components/socials.php",
  ],
  "translations" => [
    "en" => [
      "zeimoto-socials-follow-us" => "Follow us on",
      ...$flatten(require __DIR__ . "/languages/blueprints/en.php"),
    ],
  ],
  "blueprints" => [
    "tabs/socials/site" => __DIR__ . "/blueprints/tabs/socials/site.yml",
    "fields/socials/platform" => __DIR__ . "/blueprints/fields/socials/platform.yml",
    "fields/socials/username" => __DIR__ . "/blueprints/fields/socials/username.yml",
    "fields/socials/icon" => __DIR__ . "/blueprints/fields/socials/icon.yml",
    "fields/socials/icon-info" => __DIR__ . "/blueprints/fields/socials/icon-info.yml",
  ],
]);
