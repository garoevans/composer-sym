<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym;

use Garoevans\ComposerSym\Exception\BadProjectDirectoryException;
use Garoevans\ComposerSym\Exception\ComposerException;

class ComposerJson
{
  const COMPOSE_JSON_FILE = "composer.json";

  /**
   * @var string
   */
  private $composerJsonFileData;

  /**
   * @var mixed
   */
  private $parsedComposerJsonFile;

  /**
   * Lazy load and run.
   *
   * @param string $projectDir
   *
   * @return ComposerJson
   */
  public static function get($projectDir)
  {
    $composer = new ComposerJson();
    $composer->getComposerJsonFile($projectDir);
    $composer->parseComposerJsonData();
    $composer->validateComposerJsonObject();

    return $composer;
  }

  /**
   * @param string $projectDir
   *
   * @return string
   * @throws Exception\BadProjectDirectoryException
   */
  public function getComposerJsonFile($projectDir)
  {
    $composerJsonLocation = build_path($projectDir, self::COMPOSE_JSON_FILE);

    if (!file_exists($composerJsonLocation)) {
      throw new BadProjectDirectoryException(
        sprintf(
          "There was no %s file found in '%s'. Perhaps you need " .
          "set the project directory with -d, or maybe you don't have a " .
          "%s file. We tried to locate the file at the following " .
          "location: '%s'",
          self::COMPOSE_JSON_FILE,
          $projectDir,
          self::COMPOSE_JSON_FILE,
          $composerJsonLocation
        )
      );
    }

    $this->composerJsonFileData = file_get_contents($composerJsonLocation);

    return $this->composerJsonFileData;
  }

  /**
   * Throws an exception if the composer json data is invalid or does not have
   * any required packages.
   *
   * @throws Exception\ComposerException
   */
  public function validateComposerJsonObject()
  {
    if (!is_object($this->parsedComposerJsonFile)) {
      throw new ComposerException(
        sprintf("Failed reading %s.", self::COMPOSE_JSON_FILE)
      );
    }

    if(!isset($this->parsedComposerJsonFile->require)) {
      throw new ComposerException("You have no required packages to link.");
    }
  }

  /**
   * Simples. Just make sure `getComposerJsonFile` has been called first.
   */
  public function parseComposerJsonData()
  {
    $this->parsedComposerJsonFile = json_decode($this->composerJsonFileData);
  }

  /**
   * @return string
   */
  public function getComposerJsonFileData()
  {
    return $this->composerJsonFileData;
  }

  /**
   * @return mixed
   */
  public function getParsedComposerJsonFile()
  {
    return $this->parsedComposerJsonFile;
  }
}
