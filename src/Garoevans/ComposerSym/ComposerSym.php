<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;

class ComposerSym extends CliCommand
{
  /**
   * @short d
   * @valuerequired
   * @example /home/foo/bar/
   */
  public $projectDir = "";

  /**
   * @var ComposerJson
   */
  private $composerJson;

  public function execute()
  {
    $this->setProjectDir($this->projectDir);
    $this->composerJson = ComposerJson::get($this->projectDir);
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
}
