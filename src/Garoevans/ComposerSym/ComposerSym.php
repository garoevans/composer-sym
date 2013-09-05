<?php
/**
 * @author gareth.evans
 */
namespace Garoevans\ComposerSym;

use Cubex\Cli\CliCommand;
use Garoevans\ComposerSym\Lib\ComposerJson;
use Garoevans\ComposerSym\Lib\ComposerSymCore;
use Garoevans\ComposerSym\Lib\LinkWorker;
use Garoevans\ComposerSym\Lib\UnlinkWorker;

class ComposerSym extends CliCommand
{
  /**
   * The base directory of the project you want to use ComposerSym with.
   *
   * @short p
   * @valuerequired
   * @example /home/foo/bar/
   */
  public $projectDir = "";

  /**
   * We use the home directory to try and locate the existence of the package so
   * that you don't have to tell us where it is. This doesn't usually need to be
   * set.
   *
   * @short d
   * @valuerequired
   * @example /home/
   */
  public $homeDir = "";

  /**
   * If you've decided to change the name of composer's vendor directory use
   * this param to set it.
   *
   * @short v
   * @valuerequired
   * @example vendor
   */
  public $vendor = "vendor";

  /**
   * @var ComposerJson
   */
  private $composerJson;

  /**
   * @var ComposerSymCore
   */
  private $core;

  public function execute()
  {
    $_REQUEST['__path__'] = "ComposerSym";
    $this->_help();
  }

  public function help()
  {
    $this->execute();
  }

  /**
   * Run to search packages and link
   */
  public function link()
  {
    $this->composerSymInit();
    $log = $this->core->getComposerSymLog($this->projectDir);

    printf("\n> Starting to process composer packages.\n");
    $composerJsonObj = $this->composerJson->getParsedComposerJsonFile();
    LinkWorker::run($log, $this, $this->core, (array)$composerJsonObj->require);

    $log->writeLog();
  }

  /**
   * Iterate over linked packages and allow you to revert them
   */
  public function unlink()
  {
    $this->composerSymInit();
    $log = $this->core->getComposerSymLog($this->projectDir);

    printf("\n> Starting to process linked packages.\n");
    UnlinkWorker::run($log);

    $log->writeLog();
  }

  /**
   * Helper method to see what ComposerSym thinks your project directory is.
   */
  public function getGuessedProjectDir()
  {
    $this->setProjectDir("");

    echo $this->projectDir;
  }

  public function getGuessedHomeDir()
  {
    $this->setHomeDir("");

    echo $this->homeDir;
  }

  /**
   * Common initiation logic
   */
  private function composerSymInit()
  {
    $this->setProjectDir($this->projectDir);
    $this->setHomeDir($this->homeDir);
    $this->composerJson = ComposerJson::get($this->projectDir);
    $this->core         = new ComposerSymCore();
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
   * @param string $homeDir
   */
  private function setHomeDir($homeDir)
  {
    if ($homeDir === "") {
      $this->homeDir = dirname(
        dirname(dirname(dirname(dirname(CUBEX_PROJECT_ROOT))))
      );
    } else {
      $this->homeDir = $homeDir;
    }
  }
}
