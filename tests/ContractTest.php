<?php
// ==========================================================
// Tests :: Socials Contracts
// ==========================================================
//
// Exercises saved social profiles, bundled icons, and public integration.
//
// ==========================================================

declare(strict_types=1);

namespace Zeimoto\Socials\Tests;

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Data\Yaml;

final class ContractTest extends PluginTestCase
{
  public function testEmptyConfigurationRendersNothing(): void
  {
    $app = $this->app();
    self::assertCount(0, $app->collection("socials"));
    self::assertSame("", trim(snippet("components/socials", ["kirby" => $app], true)));
  }

  public function testPlatformUrlsAndBundledIcons(): void
  {
    $platforms = Yaml::decode(file_get_contents(dirname(__DIR__) . "/blueprints/fields/socials/platform.yml"))["options"];
    $rows = array_map(static fn(string $platform): array => ["platform" => $platform, "username" => "@example"], array_keys($platforms));
    $app = $this->app(site: ["content" => ["socials" => Yaml::encode($rows)]]);
    $profiles = $app->collection("socials");
    self::assertCount(count($platforms), $profiles);
    foreach ($profiles as $profile) {
      self::assertSame("example", $profile->username());
      self::assertNotFalse(filter_var($profile->url(), FILTER_VALIDATE_URL));
      $path = dirname(__DIR__) . "/assets/icons/" . $profile->platform() . ".svg";
      self::assertSame(is_file($path) ? file_get_contents($path) : "", $profile->icon());
    }
    $byPlatform = [];
    foreach ($profiles as $profile) {
      $byPlatform[$profile->platform()] = $profile;
    }
    self::assertSame("https://github.com/example", $byPlatform["github"]->url());
    self::assertSame("GitHub", $byPlatform["github"]->name());
    self::assertSame("https://threads.net/@example", $byPlatform["threads"]->url());
    self::assertSame("https://example.substack.com", $byPlatform["substack"]->url());
    self::assertSame("https://medium.com/@example", $byPlatform["medium"]->url());
    self::assertSame("https://mastodon.social/@example", $byPlatform["mastodon"]->url());
  }

  public function testSpecialCasesAndInvalidEntries(): void
  {
    $app = $this->app(site: ["content" => ["socials" => Yaml::encode([
      ["platform" => "mastodon", "username" => "@alice@example.social"],
      ["platform" => "github", "username" => " a/b "],
      ["platform" => "github", "username" => " @ "],
      ["platform" => "unknown", "username" => "alice"],
      ["platform" => "substack", "username" => "bad/name"],
      ["platform" => "mastodon", "username" => "alice@bad/host"],
    ])]]);
    $profiles = $app->collection("socials");
    self::assertCount(2, $profiles);
    self::assertSame("https://example.social/@alice", $profiles->first()->url());
    self::assertSame("https://github.com/a%2Fb", $profiles->last()->url());
  }

  public function testUploadedIconOverridesDefaultAndSnippetPreservesHooks(): void
  {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><title>Custom</title></svg>';
    $app = $this->app(site: ["content" => ["socials" => Yaml::encode([
      ["platform" => "github", "username" => "example", "icon" => "custom.svg"],
    ])]]);
    file_put_contents($app->site()->root() . "/custom.svg", $svg);
    $app->site()->files()->add(new File(["filename" => "custom.svg", "parent" => $app->site()]));
    self::assertSame($svg, $app->collection("socials")->first()->icon());
    $markup = snippet("components/socials", ["kirby" => $app], true);
    self::assertStringContainsString('class="mds-socials-button"', $markup);
    self::assertStringContainsString('href="https://github.com/example"', $markup);
    self::assertStringContainsString($svg, $markup);
  }

  public function testComputeConsumesPluginCollection(): void
  {
    $computePlugin = dirname(__DIR__, 2) . "/zeimoto-compute";
    if (!is_dir($computePlugin)) {
      self::markTestSkipped("Compute integration is exercised in the kit.");
    }
    symlink($computePlugin, $this->directory . "/plugins/zeimoto-compute");
    $app = $this->app(site: ["content" => ["socials" => Yaml::encode([
      ["platform" => "x", "username" => "@example"],
    ])]]);
    $page = new Page(["slug" => "home"]);
    self::assertSame(["https://x.com/example"], compute($page)->sameAs());
    self::assertSame("@example", compute($page)->twitterHandle());
  }
}
