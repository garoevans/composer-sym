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
      foreach ($data as $key => $logFileObject) {
        $this->addObject(
          new ComposerSymLogFileObject(
            $logFileObject->package,
            $logFileObject->location,
            $logFileObject->tempLocation
          ),
          $key
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
   * @param string $key
   *
   * @return $this
   */
  public function addObject(ComposerSymLogFileObject $logFileObject, $key)
  {
    $this->objects[$key] = $logFileObject;

    return $this;
  }

  /**
   * @param string $key
   *
   * @return $this
   */
  public function removeObject($key)
  {
    if ($this->objectIsSet($key)) {
      unset($this->objects[$key]);
    }

    return $this;
  }

  /**
   * @param string $key
   *
   * @return bool
   */
  public function objectIsSet($key)
  {
    return isset($this->objects[$key]);
  }

  public function jsonSerialize()
  {
    return $this->objects;
  }
}
