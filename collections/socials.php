<?php
// ==========================================================
// Collections :: Socials
// ==========================================================
//
// Resolves configured social profiles into display names,
// profile URLs, and inline icons for frontend consumers.
//
// ==========================================================

declare(strict_types=1);

use Kirby\Cms\Site;
use Kirby\Cms\Structure;
use Kirby\Cms\StructureObject;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Obj;

$baseUrls = [
  "behance" => "https://behance.net/",
  "bluesky" => "https://bsky.app/profile/",
  "discord" => "https://discord.gg/",
  "dribbble" => "https://dribbble.com/",
  "facebook" => "https://facebook.com/",
  "github" => "https://github.com/",
  "gitlab" => "https://gitlab.com/",
  "instagram" => "https://instagram.com/",
  "linkedin" => "https://linkedin.com/in/",
  "pinterest" => "https://pinterest.com/",
  "reddit" => "https://reddit.com/user/",
  "snapchat" => "https://snapchat.com/add/",
  "soundcloud" => "https://soundcloud.com/",
  "spotify" => "https://open.spotify.com/user/",
  "telegram" => "https://t.me/",
  "threads" => "https://threads.net/@",
  "tiktok" => "https://tiktok.com/@",
  "twitch" => "https://twitch.tv/",
  "vimeo" => "https://vimeo.com/",
  "whatsapp" => "https://wa.me/p/",
  "x" => "https://x.com/",
  "youtube" => "https://youtube.com/@",
];

return function (Site $site) use ($baseUrls): Structure {
  return $site
    ->socials()
    ->toStructure()
    ->map(function (StructureObject $social) use ($baseUrls, $site): Obj|null {
      $platform = $social->platform()->value();
      $username = ltrim(trim($social->username()->value() ?? ""), "@");

      if ($username === "") {
        return null;
      }

      if ($platform === "substack") {
        if (preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i', $username) !== 1) {
          return null;
        }
        $url = "https://" . $username . ".substack.com";
      } elseif ($platform === "medium") {
        $url = "https://medium.com/@" . rawurlencode($username);
      } elseif ($platform === "mastodon") {
        [$user, $instance] = array_pad(explode("@", $username, 2), 2, "mastodon.social");
        if ($user === "" || preg_match('/^[a-z0-9](?:[a-z0-9.-]*[a-z0-9])?$/i', $instance) !== 1) {
          return null;
        }
        $url = "https://" . $instance . "/@" . rawurlencode($user);
      } else {
        if (!isset($baseUrls[$platform])) {
          return null;
        }
        $url = $baseUrls[$platform] . rawurlencode($username);
      }

      if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return null;
      }

      $iconHtml = "";
      $iconPath = dirname(__DIR__) . "/assets/icons/" . $platform . ".svg";

      if ($uploadedIcon = $social->icon()->toFile()) {
        $iconHtml = $uploadedIcon->read();
      } elseif (F::exists($iconPath)) {
        $iconHtml = F::read($iconPath);
      }

      $displayName = match ($platform) {
        "bluesky" => "BlueSky",
        "github" => "GitHub",
        "gitlab" => "GitLab",
        "linkedin" => "LinkedIn",
        "soundcloud" => "SoundCloud",
        "whatsapp" => "WhatsApp",
        "x" => "X",
        "youtube" => "YouTube",
        default => ucfirst($platform),
      };

      return new Obj([
        "platform" => $platform,
        "name" => $displayName,
        "username" => $username,
        "url" => $url,
        "icon" => $iconHtml,
      ]);
    })
    ->filter(static fn(Obj|null $social): bool => $social !== null);
};
