<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Lib;

use Cubex\Cli\UserPrompt;
use Garoevans\ComposerSym\ComposerSym;
use Garoevans\ComposerSym\Exception\ComposerSymException;
use Garoevans\ComposerSym\Log\ComposerSymLog;

class LinkWorker
{
  private static $packageLocation;

  /**
   * @param ComposerSymLog  $log
   * @param ComposerSym     $composerSym
   * @param ComposerSymCore $core
   * @param array           $required
   *
   * @throws \Garoevans\ComposerSym\Exception\ComposerSymException
   */
  public static function run(
    ComposerSymLog $log,
    ComposerSym $composerSym,
    ComposerSymCore $core,
    array $required
  )
  {
    foreach ($required as $package => $version) {
      if (self::shouldSkip($log, $composerSym, $package)) {
        continue;
      }

      $doSymlink = UserPrompt::confirm(
        sprintf("\n> Would you like to symlink %s?", $package),
        'n'
      );

      if ($doSymlink) {
        self::runSymLink($log, $composerSym, $core, $package);
      }
    }
  }

  /**
   * @param ComposerSymLog $log
   * @param ComposerSym    $composerSym
   * @param string         $package
   *
   * @return bool
   */
  private static function shouldSkip(
    ComposerSymLog $log,
    ComposerSym $composerSym,
    $package
  )
  {
    // Don't try and link this project.
    if ($package === "garoevans/composer-sym") {
      return true;
    }

    // Make sure the package actually exists in the vendor directory.
    if (! file_exists(self::getPackageLocation($composerSym, $package))) {
      return true;
    }

    // If it's already linked, skip it.
    if ($log->isPackageLinked($package)) {
      return true;
    }

    return false;
  }

  /**
   * @param ComposerSym $composerSym
   * @param string      $package
   *
   * @return string
   */
  private static function getPackageLocation(ComposerSym $composerSym, $package)
  {
    if (self::$packageLocation === null) {
      self::$packageLocation = build_path(
        $composerSym->projectDir,
        $composerSym->vendor,
        $package
      );
    }

    return self::$packageLocation;
  }

  /**
   * @param ComposerSymLog  $log
   * @param ComposerSym     $composerSym
   * @param ComposerSymCore $core
   * @param string          $package
   *
   * @throws \Garoevans\ComposerSym\Exception\ComposerSymException
   */
  private static function runSymLink(
    ComposerSymLog $log,
    ComposerSym $composerSym,
    ComposerSymCore $core,
    $package
  )
  {
    $potentialLocation = $core->getPotentialLinkLocation(
      $package,
      $composerSym->homeDir
    );

    if (strlen($potentialLocation) === 0) {
      $linkTo = $core->getLinkLocation($package);
    } else {
      $linkTo = $potentialLocation;
    }

    // Move the existing package to a new temporary directory and symlink
    // to the local copy
    $tempLocation = build_path(
      $composerSym->projectDir,
      $composerSym->vendor,
      $core->getTemporaryPackageName($package)
    );

    $packageLocation = self::getPackageLocation($composerSym, $package);
    rename($packageLocation, $tempLocation);

    if (! symlink($linkTo, $packageLocation)) {
      rename($tempLocation, $packageLocation);
      throw new ComposerSymException(
        "Failed creating sym link, this could be a privileges issue. Try" .
        "running the console with raised privileges."
      );
    }

    printf("> %s symlinked:\n", $package);
    printf(">> link: %s\n", $packageLocation);
    printf(">> target: %s\n", $linkTo);

    $log->addPackage($package, $packageLocation, $tempLocation);

    unset($linkTo);
    echo "\n";
  }
}
