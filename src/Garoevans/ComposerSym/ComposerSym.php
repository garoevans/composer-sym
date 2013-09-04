<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;
use Cubex\Cli\UserPrompt;

class ComposerSym extends CliCommand
{
  /**
   * @short p
   * @valuerequired
   * @example /home/foo/bar/
   */
  public $projectDir = "";

  /**
   * @short d
   * @valuerequired
   * @example /home/
   */
  public $homeDir = "";

  /**
   * @short v
   * @valuerequired
   * @example vendor
   */
  public $vendor = "vendor";

  /**
   * @var ComposerJson
   */
  private $composerJson;

  public function execute()
  {
    $this->setProjectDir($this->projectDir);
    $this->composerJson = ComposerJson::get($this->projectDir);
    $this->setHomeDir($this->homeDir);

    printf("\n> Starting to process composer packages.\n");

    $composerJsonObj = $this->composerJson->getParsedComposerJsonFile();
    foreach ($composerJsonObj->require as $package => $version) {

      // TODO: move into ComposerVendor class
      if (file_exists(build_path($this->projectDir, "vendor", $package))) {
        $doSymlink = UserPrompt::confirm(
          sprintf("\n> Would you like to symlink %s?", $package),
          'y'
        );

        if ($doSymlink) {
          // TODO: move to method
          $linkToPotentialLocation = false;
          $potentialLocation = build_path($this->homeDir, $package);
          if (file_exists($potentialLocation)) {
            $linkToPotentialLocation = UserPrompt::confirm(
              sprintf("> Link to '%s'?", $potentialLocation)
            );
          }

          // TODO: move to method
          if (!$linkToPotentialLocation) {
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
          } else {
            $linkTo = $linkToPotentialLocation;
          }

          $packageParts = explode("/", $package);
          end($packageParts);
          $packageParts[key($packageParts)] = "__" . current($packageParts);
          $newPackage = implode("/", $packageParts);

          $oldPath = build_path($this->projectDir, $this->vendor, $package);
          $newPath = build_path($this->projectDir, $this->vendor, $newPackage);


          rename($oldPath, $newPath);
          symlink($linkTo, $oldPath);

          sprintf("> %s symlinked:", $package);
          sprintf(">> link: %s", $oldPath);
          sprintf(">> target: %s", $linkTo);

          // TODO: Write to log file in user dir ~


          unset($linkTo);
          echo "\n";
        }
      }
    }
  }

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
}
