<?php
/**
 * @author gareth.evans
 */

namespace Garoevans\ComposerSym\Lib;

use Cubex\Cli\UserPrompt;
use Garoevans\ComposerSym\Log\ComposerSymLog;

class UnlinkWorker
{
  /**
   * @param ComposerSymLog $log
   */
  public static function run(ComposerSymLog $log)
  {
    foreach($log->getLinkedPackages() as $linkedPackage) {
      $doUnlink = UserPrompt::confirm(
        sprintf("\n> Would you like to unlink %s?", $linkedPackage->package),
        'n'
      );

      if ($doUnlink) {
        rmdir($linkedPackage->location);
        rename($linkedPackage->tempLocation, $linkedPackage->location);

        printf("> %s unlinked", $linkedPackage->package);

        $log->removePackage($linkedPackage->package);
      }
    }
  }
}
