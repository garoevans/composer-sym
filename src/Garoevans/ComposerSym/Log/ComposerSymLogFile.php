<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Log;

class ComposerSymLogFile implements \JsonSerializable
{
  /**
   * @var ComposerSymLogFileObject[]
   */
  private $objects = array();

  /**
   * @param ComposerSymLogFileObject[] $data
   */
  public function __construct($data = array())
  {
    if (is_array($data) || is_object($data)) {
      foreach ($data as $logFileObject) {
        $this->addObject(
          new ComposerSymLogFileObject(
            $logFileObject->package,
            $logFileObject->location,
            $logFileObject->tempLocation
          )
        );
      }
    }
  }

  /**
   * @param $string
   *
   * @return ComposerSymLogFile
   */
  public static function fromJson($string)
  {
    return new self(json_decode($string));
  }

  /**
   * @param ComposerSymLogFileObject $logFileObject
   *
   * @return $this
   */
  public function addObject(ComposerSymLogFileObject $logFileObject)
  {
    $this->objects[$logFileObject->package] = $logFileObject;

    return $this;
  }

  /**
   * @param string $package
   *
   * @return $this
   */
  public function removeObject($package)
  {
    if ($this->objectIsSet($package)) {
      unset($this->objects[$package]);
    }

    return $this;
  }

  /**
   * @param string $package
   *
   * @return bool
   */
  public function objectIsSet($package)
  {
    return isset($this->objects[$package]);
  }

  public function jsonSerialize()
  {
    return $this->objects;
  }
}
