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
        // TODO: look potential location
        // TODO: Suggest potential location
        // TODO: Fall back to user input
        // TODO: Symlink location
        // TODO: Write to log file in user dir ~
      }
    }
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
