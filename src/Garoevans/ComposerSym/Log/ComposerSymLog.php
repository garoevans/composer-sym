<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Log;

use Garoevans\ComposerSym\Exception\ComposerSymLogException;

class ComposerSymLog
{
  private $logFilePath;

  /**
   * @var ComposerSymLogFile
   */
  private $logFile;

  private $key;

  const LOG_FILE_NAME = ".composersym";

  public function __construct($logDir, $projectDir)
  {
    $this->logFilePath = build_path($logDir, self::LOG_FILE_NAME);
    $this->key = substr(md5($projectDir), 0, 6) . "_";

    if (! file_exists($this->logFilePath)) {
      touch($this->logFilePath);
    }

    if (! $this->canReadWrite()) {
      throw new ComposerSymLogException(
        sprintf(
          "ComposerSym is unable to read/write a log file to '%s'",
          $logDir
        )
      );
    }

    $data = file_get_contents($this->logFilePath);

    if (strlen($data) > 0) {
      $this->logFile = ComposerSymLogFile::fromJson($data);
    } else {
      $this->logFile = new ComposerSymLogFile();
    }
  }

  /**
   * @param string $package
   * @param string $location
   * @param string $tempLocation
   *
   * @return $this
   */
  public function addPackage($package, $location, $tempLocation)
  {
    $this->logFile->addObject(
      new ComposerSymLogFileObject(
        $package,
        $location,
        $tempLocation
      ),
      $this->getUniqueKey($package)
    );

    return $this;
  }

  /**
   * @param string $package
   *
   * @return $this
   */
  public function removePackage($package)
  {
    $this->logFile->removeObject($this->getUniqueKey($package));

    return $this;
  }

  public function writeLog()
  {
    file_put_contents($this->logFilePath, json_encode($this->logFile));
  }

  /**
   * @param string $package
   *
   * @return bool
   */
  public function isPackageLinked($package)
  {
    return $this->logFile->objectIsSet($this->getUniqueKey($package));
  }

  /**
   * @return bool
   */
  private function canReadWrite()
  {
    return is_writable($this->logFilePath);
  }

  private function getUniqueKey($package)
  {
    return $this->key . $package;
  }
}
