<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Tests;

use Garoevans\ComposerSym\Lib\ComposerSymCore;

class ComposerSymCoreTest extends \PHPUnit_Framework_TestCase
{
  public function testGetTemporaryPackageName()
  {
    $package = "foo/bar";

    $composerSymCore = new ComposerSymCore();

    $this->assertEquals(
      "foo/___bar",
      $composerSymCore->getTemporaryPackageName($package)
    );
  }
}
