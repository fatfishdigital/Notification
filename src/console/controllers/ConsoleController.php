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

use fatfish\notification\Notification;

use Craft;
use fatfish\notification\records\NotificationServerRecord;
use yii\console\Controller;
use yii\helpers\Console;
use \Cron\Job\ShellJob;

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
        $result = 'something';

        echo "Welcome to the console ConsoleController actionIndex() method\n";

        return $result;
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

            $AllServer = NotificationServerRecord::find()->all();
            var_dump($AllServer);
        }
        echo "Sorry Not authorize to perform";
    }

    public static function setcronjob()
    {
        $job2 = new \Cron\Job\ShellJob();
        $job2->setCommand('mkdir Hellworld');
        $job2->setSchedule(new \Cron\Schedule\CrontabSchedule('*/1 * * * *'));
        $resolver = new \Cron\Resolver\ArrayResolver();
        $resolver->addJob($job2);
        $cron = new \Cron\Cron();
        $cron->setExecutor(new \Cron\Executor\Executor());
        $cron->setResolver($resolver);
var_dump($cron->run());

    }
}
