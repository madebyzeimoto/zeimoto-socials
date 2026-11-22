# zeimoto Socials

`zeimoto/socials` owns social profile configuration, URL resolution, public
rendering, and 22 bundled platform SVGs. It supplies no CSS, SCSS, or JavaScript;
public styling belongs entirely to the mounted MDS components.

## Installation

Requires PHP 8.5 and Kirby 6. The Composer identity is
`zeimoto/socials`, with installation directory `site/plugins/zeimoto-socials`.
Release versions come from Git tags. For a manual installation, copy the plugin
into that directory. Composer installations require the package to be registered
on Packagist or its GitHub VCS repository to be configured by the host.

Mount `socials: tabs/socials/site` in the site blueprint's `tabs` map.
The plugin registers `fields/socials/platform`, `fields/socials/username`, and
`fields/socials/icon`, plus the informational `fields/socials/icon-info`. The editor
uses singular Social field labels and explains the default SVGs below the inputs. Saved `socials` entries retain `platform`, `username`, and
`icon`; existing content needs no migration.

## Public integration

`$kirby->collection("socials")` returns resolved entries exposing `platform()`,
`name()`, `username()`, `url()`, and `icon()`. Empty or invalid profiles are omitted.
The `icon()` value is rendered SVG markup. An uploaded icon takes precedence over
`assets/icons/<platform>.svg` inside this plugin. Missing defaults produce an
empty icon; Medium, Mastodon, and Substack currently have no bundled defaults.
Uploaded icons are trusted author-managed SVGs, as in the existing kit.

Render with `snippet("components/socials")`. It keeps the existing `mds-socials-*`
classes and supplies its own `zeimoto-socials-follow-us` translation. MDS owns
their presentation.
Compute's `sameAs()` and `twitterHandle()` consume the registered collection.
Host collection, snippet, and blueprint files override plugin registrations;
remove former host copies when migrating so there is only one implementation.

## Host requirements

- Provide a `files/icon` upload blueprint accepting SVG and its referenced fields.
  The kit supplies this blueprint for uploaded social icons.
- Load the `zeimoto-icons` Panel registry for the Socials tab icon.
- Mount the MDS socials component styles for the public snippet.

The plugin has no options, environment variables, credentials, or build step.
It remains harmless with no configured profiles. Default icons are read on the
server and embedded inline; no public asset URL or Vite import is needed.

## Development environment

Open `kirby.code-workspace` while this plugin is installed in the kit. Shared
editor settings, Kirby IDE stubs, Prettier configuration, and the PHP formatter
launcher follow the other first-party plugins. The launcher uses Herd's PHP 8.5
and the host kit's PHP-CS-Fixer configuration and installation.

`tools/` contains that formatter launcher; `tests/` and `phpunit.xml` own the
isolated PHP suite. The GitHub workflow installs Composer dependencies and runs
that suite. No npm manifest or frontend build is required.

## Verification

Run `composer install && composer test` in a standalone checkout. In the kit,
`composer test:plugins` discovers this suite automatically. The suite uses
disposable Kirby content and runtime directories. Run plugin lint from the kit:

```sh
node tools/check-style.mjs --plugin site/plugins/zeimoto-socials
```

Before release, run the kit integration/browser checks and verify the Socials tab
manually. The host must require the published plugin release and include it in its
Composer lockfile so a clean installation restores the extracted files.

## Follow-up

- Add bundled default SVG icons for Medium, Mastodon, and Substack. Until then,
  these platforms support uploaded icons; update the Panel information box and
  icon tests when their defaults are added.
