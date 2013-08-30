<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;

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

    $composerJsonObj = $this->composerJson->getParsedComposerJsonFile();
    foreach ($composerJsonObj->require as $package => $version) {
      printf("> %s: %s\n", $package, $version);
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
