<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Lib;

use Cubex\Cli\UserPrompt;
use Garoevans\ComposerSym\Log\ComposerSymLog;

class ComposerSymCore
{
  const TEMPORARY_PACKAGE_PREFIX = "___";

  /**
   * @param string $package
   *
   * @return string
   */
  public function getTemporaryPackageName($package)
  {
    $packageParts = explode("/", $package);
    end($packageParts);
    $packageParts[key($packageParts)] = self::TEMPORARY_PACKAGE_PREFIX .
      current($packageParts);

    return implode("/", $packageParts);
  }

  /**
   * @param string $package
   *
   * @return string
   */
  public function getLinkLocation($package)
  {
    do {
      if (isset($linkTo)) {
        printf("> Directory '%s' does not exist.\n", $linkTo);
      }
      $linkTo = UserPrompt::prompt(
        sprintf(
          "> Enter full path to base directory for '%s': ",
          $package
        )
      );
    } while (!file_exists($linkTo));

    return $linkTo;
  }

  /**
   * @param string $package
   * @param string $homeDir
   *
   * @return string
   */
  public function getPotentialLinkLocation($package, $homeDir)
  {
    $linkToPotentialLocation = false;
    $potentialLocation       = build_path($homeDir, $package);

    if (file_exists($potentialLocation)) {
      $linkToPotentialLocation = UserPrompt::confirm(
        sprintf("> Link to '%s'?", $potentialLocation),
        'y'
      );
    }

    return $linkToPotentialLocation ? $potentialLocation : "";
  }

  /**
   * @param string $projectDir
   *
   * @return ComposerSymLog
   */
  public function getComposerSymLog($projectDir)
  {
    return new ComposerSymLog(getenv('HOME'), $projectDir);
  }
}
