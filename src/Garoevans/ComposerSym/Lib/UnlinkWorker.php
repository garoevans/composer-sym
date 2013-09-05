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

        // Sometimes rename leaves a copy of both directories, we double check
        // that it's actually gone here and if not remove it.
        if (file_exists($linkedPackage->tempLocation)) {
          rmdir($linkedPackage->tempLocation);
        }

        printf("> %s unlinked\n", $linkedPackage->package);

        $log->removePackage($linkedPackage->package);
      }
    }
  }
}
