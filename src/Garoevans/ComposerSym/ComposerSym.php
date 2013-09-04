<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;
use Cubex\Cli\UserPrompt;
use Garoevans\ComposerSym\Log\ComposerSymLog;

class ComposerSym extends CliCommand
{
  /**
   * The base directory of the project you want to use ComposerSym with.
   *
   * @short p
   * @valuerequired
   * @example /home/foo/bar/
   */
  public $projectDir = "";

  /**
   * We use the home directory to try and locate the existence of the package so
   * that you don't have to tell us where it is. This doesn't usually need to be
   * set.
   *
   * @short d
   * @valuerequired
   * @example /home/
   */
  public $homeDir = "";

  /**
   * If you've decided to change the name of composer's vendor directory use
   * this param to set it.
   *
   * @short v
   * @valuerequired
   * @example vendor
   */
  public $vendor = "vendor";

  const TEMPORARY_PACKAGE_PREFIX = "___";

  /**
   * @var ComposerJson
   */
  private $composerJson;

  public function execute()
  {
    $this->_help();
  }

  public function help()
  {
    $this->execute();
  }

  /**
   * Run to search packages and link
   */
  public function link()
  {
    $this->setProjectDir($this->projectDir);
    $this->composerJson = ComposerJson::get($this->projectDir);
    $this->setHomeDir($this->homeDir);

    $log = new ComposerSymLog(getenv('HOME'), $this->projectDir);

    printf("\n> Starting to process composer packages.\n");

    $composerJsonObj = $this->composerJson->getParsedComposerJsonFile();
    foreach ($composerJsonObj->require as $package => $version) {

      $packageLocation = build_path($this->projectDir, $this->vendor, $package);
      if (! file_exists($packageLocation)) {
        continue;
      }

      if ($log->isPackageLinked($package)) {
        continue;
      }

      $doSymlink = UserPrompt::confirm(
        sprintf("\n> Would you like to symlink %s?", $package),
        'n'
      );

      if ($doSymlink) {
        $potentialLocation = $this->getPotentialLinkLocation($package);

        if (strlen($potentialLocation) === 0) {
          $linkTo = $this->getLinkLocation($package);
        } else {
          $linkTo = $potentialLocation;
        }

        // Move the existing package to a new temporary directory and symlink
        // to the local copy
        $tempLocation = build_path(
          $this->projectDir,
          $this->vendor,
          $this->getTemporaryPackageName($package)
        );

        rename($packageLocation, $tempLocation);
        symlink($linkTo, $packageLocation);

        sprintf("> %s symlinked:", $package);
        sprintf(">> link: %s", $packageLocation);
        sprintf(">> target: %s", $linkTo);

        $log->addPackage($package, $packageLocation, $tempLocation);

        unset($linkTo);
        echo "\n";
      }
    }

    $log->writeLog();
  }

  /**
   * Helper method to see what ComposerSym thinks your project directory is.
   */
  public function getGuessedProjectDir()
  {
    $this->setProjectDir("");

    echo $this->projectDir;
  }

  public function getGuessedHomeDir()
  {
    $this->setHomeDir("");

    echo $this->homeDir;
  }

  /**
   * @param string $projectDir
   */
  private function setProjectDir($projectDir)
  {
    if ($projectDir === "") {
      $this->projectDir = dirname(dirname(dirname(CUBEX_PROJECT_ROOT)));
    } else {
      $this->projectDir = $projectDir;
    }
  }

  /**
   * @param string $homeDir
   */
  private function setHomeDir($homeDir)
  {
    if ($homeDir === "") {
      $this->homeDir = dirname(
        dirname(dirname(dirname(dirname(CUBEX_PROJECT_ROOT))))
      );
    } else {
      $this->homeDir = $homeDir;
    }
  }

  /**
   * @param string $package
   *
   * @return string
   */
  private function getPotentialLinkLocation($package)
  {
    $linkToPotentialLocation = false;
    $potentialLocation       = build_path($this->homeDir, $package);

    if (file_exists($potentialLocation)) {
      $linkToPotentialLocation = UserPrompt::confirm(
        sprintf("> Link to '%s'?", $potentialLocation)
      );
    }

    return $linkToPotentialLocation ? $potentialLocation : "";
  }

  /**
   * @param string $package
   *
   * @return string
   */
  private function getLinkLocation($package)
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
   *
   * @return string
   */
  private function getTemporaryPackageName($package)
  {
    $packageParts = explode("/", $package);
    end($packageParts);
    $packageParts[key($packageParts)] = self::TEMPORARY_PACKAGE_PREFIX .
      current($packageParts);

    return  implode("/", $packageParts);
  }
}
