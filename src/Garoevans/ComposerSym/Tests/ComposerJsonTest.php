<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Tests;


use Garoevans\ComposerSym\Lib\ComposerJson;

class ComposerJsonTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @expectedException \Garoevans\ComposerSym\Exception\BadProjectDirectoryException
   */
  public function testFailedGetComposerJsonFile()
  {
    $composerJson = new ComposerJson();

    $composerJson->getComposerJsonFile(
      build_path(__DIR__, "data", "composer.doesntexist.json")
    );
  }

  public function testGetComposerJsonFile()
  {
    $composerJson = new ComposerJson();

    $fileData = $composerJson->getComposerJsonFile(build_path(__DIR__, "data"));

    $this->assertJsonStringEqualsJsonString(
      json_encode($this->getComposerJson()),
      $fileData
    );
  }

  /*public function testParseComposerJson()
  {

  }*/

  public function testValidateComposerJsonObject()
  {
    $composerJson = new ComposerJson();
    $composerJson->getComposerJsonFile(build_path(__DIR__, "data"));
    $composerJson->parseComposerJsonData();
    $composerJson->validateComposerJsonObject();

    $this->assertJsonStringEqualsJsonString(
      json_encode($this->getComposerJson()),
      json_encode($composerJson->getParsedComposerJsonFile())
    );
  }

  /**
   * @expectedException        \Garoevans\ComposerSym\Exception\ComposerException
   * @expectedExceptionMessage Failed reading your json composer file.
   */
  public function testBadJsonFile()
  {
    $composerJson = new ComposerJson();
    $composerJson->getComposerJsonFile(
      build_path(__DIR__, "data"),
      "composer.nojson.json"
    );
    $composerJson->parseComposerJsonData();
    $composerJson->validateComposerJsonObject();
  }

  /**
   * @expectedException        \Garoevans\ComposerSym\Exception\ComposerException
   * @expectedExceptionMessage You have no required packages to link.
   */
  public function testMissingRequireFromJsonFile()
  {
    $composerJson = new ComposerJson();
    $composerJson->getComposerJsonFile(
      build_path(__DIR__, "data"),
      "composer.norequire.json"
    );
    $composerJson->parseComposerJsonData();
    $composerJson->validateComposerJsonObject();
  }

  public function testStaticGet()
  {
    $composerJsonStatic = ComposerJson::get(build_path(__DIR__, "data"));

    $composerJson = new ComposerJson();
    $composerJson->getComposerJsonFile(build_path(__DIR__, "data"));
    $composerJson->parseComposerJsonData();
    $composerJson->validateComposerJsonObject();

    $this->assertTrue($composerJsonStatic == $composerJson);
  }

  private function getComposerJson()
  {
    $package  = "foo/bar";
    $expected = new \stdClass();
    $expected->require = new \stdClass();
    $expected->require->{$package} = "*";

    return $expected;
  }
}
