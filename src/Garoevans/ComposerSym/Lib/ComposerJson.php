<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Lib;

use Garoevans\ComposerSym\Exception\BadProjectDirectoryException;
use Garoevans\ComposerSym\Exception\ComposerException;

class ComposerJson
{
  const COMPOSER_JSON_FILE = "composer.json";

  /**
   * Once loaded this will hold the encoded json string
   *
   * @var string
   */
  private $composerJsonFileData;

  /**
   * Parsed json object
   *
   * @var object
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
   * @param string $fileName
   *
   * @return string
   * @throws BadProjectDirectoryException
   */
  public function getComposerJsonFile(
    $projectDir,
    $fileName = self::COMPOSER_JSON_FILE
  )
  {
    $composerJsonLocation = build_path($projectDir, $fileName);

    if (!file_exists($composerJsonLocation)) {
      throw new BadProjectDirectoryException(
        sprintf(
          "There was no %s file found in '%s'. Perhaps you need " .
          "set the project directory with -p, or maybe you don't have a " .
          "%s file. We tried to locate the file at the following " .
          "location: '%s'",
          $fileName,
          $projectDir,
          $fileName,
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
   * @throws ComposerException
   */
  public function validateComposerJsonObject()
  {
    if (!is_object($this->parsedComposerJsonFile)) {
      throw new ComposerException(
        sprintf("Failed reading your json composer file.")
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
