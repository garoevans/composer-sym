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
  public $projectDir = null;

  public function execute()
  {
    $this->setProjectDir($this->projectDir);


  }

  private function setProjectDir($projectDir)
  {
    if ($projectDir === null) {
      $this->projectDir = dirname(dirname(dirname(CUBEX_PROJECT_ROOT)));
    } else {
      $this->projectDir = $projectDir;
    }
  }
}
