<?php
/**
 * Notification plugin for Craft CMS 3.x
 *
 * Notification plugin for craft 3.x
 *
 * @link      https://fatfish.com.au
 * @copyright Copyright (c) 2018 Fatfish
 */

namespace fatfish\notification\console\controllers;

use fatfish\notification\controllers\ServerStatusController;
use fatfish\notification\Notification;

use Craft;
use fatfish\notification\records\NotificationServerRecord;
use yii\console\Controller;
use yii\helpers\Console;
use yii2tech\crontab\CronTab;
use yii2tech\crontab\CronJob;

/**
 * Default Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is plugin-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft notification/default
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft notification/default/do-something
 *
 * @author    Fatfish
 * @package   Notification
 * @since     1.0.0
 */
class ConsoleController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle notification/default console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {

       self::actionSetcronjob();
    }

    /**
     * Handle notification/default/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionCheckServerLogs()
    {
        if(Craft::$app->request->isConsoleRequest) {

           ServerStatusController::check_server_status();
            return;

        }
        echo "Sorry unauthorize action detected";
    }

    public static function actionSetcronjob()
    {

        $getcurrent = dirname(dirname( dirname(__FILE__)));
        $scriptdir = $getcurrent."/cron/cron.php";
        $cronJob = new CronJob();
        $cronJob->min = '*/2';
        $cronJob->hour = '*';
        $cronJob->command = 'php '.$scriptdir;
        $cronTab = new CronTab();
        $cronTab->setJobs([
            $cronJob
        ]);
        $cronTab->apply();



    }
}
