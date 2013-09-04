<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Log;

class ComposerSymLogFileObject
{
  public $package;
  public $location;
  public $tempLocation;

  public function __construct($package, $location, $tempLocation)
  {
    $this->package      = $package;
    $this->location     = $location;
    $this->tempLocation = $tempLocation;
  }
}
