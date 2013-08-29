<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;
use Garoevans\ComposerSym\Exception\BadProjectDirectoryException;
use Garoevans\ComposerSym\Exception\ComposerException;

class ComposerSym extends CliCommand
{
  /**
   * @short d
   * @valuerequired
   * @example /home/foo/bar/
   */
  public $projectDir = "";

  private $composerJsonFileData;
  private $parsedComposerJsonFile;

  public function execute()
  {
    $this->setProjectDir($this->projectDir);
    $this->composerJsonFileData   = $this->getComposerJsonFile();
    $this->parsedComposerJsonFile = json_decode($this->composerJsonFileData);
    $this->validateComposerJsonObject($this->parsedComposerJsonFile);
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
   * @return string
   * @throws Exception\BadProjectDirectoryException
   */
  private function getComposerJsonFile()
  {
    $composerJsonLocation = build_path($this->projectDir, "composer.json");

    if (!file_exists(build_path($this->projectDir, "composer.json"))) {
      throw new BadProjectDirectoryException(
        sprintf(
          "There was no composer.json file found in '%s'. Perhaps you need " .
          "set the project directory with -d, or maybe you don't have a " .
          "composer.json file. We tried to locate the file at the following " .
          "location: '%s'",
          $this->projectDir,
          $composerJsonLocation
        )
      );
    }

    return file_get_contents($composerJsonLocation);
  }

  /**
   * @param mixed $composerJsonObject
   *
   * @throws Exception\ComposerException
   */
  private function validateComposerJsonObject($composerJsonObject)
  {
    if (!is_object($composerJsonObject)) {
      throw new ComposerException("Failed reading composer.json");
    }

    if(!isset($composerJsonObject->require)) {
      throw new ComposerException("You have no required packages to link.");
    }
  }
}
