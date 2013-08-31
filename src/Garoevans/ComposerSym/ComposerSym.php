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
   * @var ComposerJson
   */
  private $composerJson;

  public function execute()
  {
    $this->setProjectDir($this->projectDir);
    $this->composerJson = ComposerJson::get($this->projectDir);
    $this->setHomeDir($this->homeDir);

    printf("\n> Starting to process composer packages.\n\n");

    $composerJsonObj = $this->composerJson->getParsedComposerJsonFile();
    foreach ($composerJsonObj->require as $package => $version) {
      $doSymlink = UserPrompt::confirm(
        sprintf("> Would you like to symlink %s?", $package),
        'y'
      );

      if ($doSymlink) {
        $linkToPotentialLocation = false;
        $potentialLocation = build_path($this->homeDir, $package);
        if (file_exists($potentialLocation)) {
          $linkToPotentialLocation = UserPrompt::confirm(
            sprintf("> Link to '%s'?", $potentialLocation)
          );
        }

        if (!$linkToPotentialLocation) {
          do {
            if (isset($linkTo)) {
              printf("> Directory '%s' does not exist.\n", $linkTo);
            }
            $linkTo = UserPrompt::prompt(
              sprintf("> Enter full path to base directory for '%s'", $package)
            );
          } while (!file_exists($linkTo));
        } else {
          $linkTo = $linkToPotentialLocation;
        }

        // TODO: Symlink location
        // TODO: Write to log file in user dir ~

        echo "\n";
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
