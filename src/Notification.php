<?php
/**
 * Notification plugin for Craft CMS 3.x
 *
 * Notification plugin for craft 3.x
 *
 * @link      https://fatfish.com.au
 * @copyright Copyright (c) 2018 Fatfish
 */

namespace fatfish\notification;


use craft\base\Component;
use craft\base\ElementAction;
use craft\controllers\RoutesController;
use craft\controllers\TemplatesController;
use craft\elements\actions\DeleteAssets;
use craft\elements\Tag;
use craft\events\RouteEvent;
use craft\fields\Url;
use craft\records\Route;
use craft\services\Routes;
use craft\web\Response;
use fatfish\notification\console\controllers\ConsoleController;
use fatfish\notification\controllers\ElementsController;
use fatfish\notification\services\ServerNotificationService as NotificationServiceService;
use fatfish\notification\widgets\NotificationWidget as NotificationWidgetWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Elements;
use yii\base\Event;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Fatfish
 * @package   Notification
 * @since     1.0.0
 *
 * @property  NotificationServiceService $notificationService
 */
class Notification extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Notification::$plugin
     *
     * @var Notification
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';



    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Notification::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {

        parent::init();
        self::$plugin = $this;

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'fatfish\notification\console\controllers';
        }

//         Register  site routes for 404 error and 302
        Event::on(
            Response::class,
            Response::EVENT_AFTER_SEND,function ($event)
        {


            $exception = Craft::$app->errorHandler->exception;

            if(!is_null($exception)) {

                $ElementsController = new ElementsController();
                $ElementsController->actionOnResponse($exception->statusCode,
                    $exception->getMessage());
            }
        }
        );





        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['notification/section'] = 'notification/notification/section';
            }
        );

        Event::on(UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event){

                $event->rules['notification/save']='notification/notification/save';
                $event->rules['notification/craft']='notification/notification/craft';
                $event->rules['notification'] = 'notification/notification/index';
                $event->rules['notification/edit/<id:\d+>'] = 'notification/notification/edit';
                $event->rules['notification/delete/<id:\d+>'] = 'notification/notification/delete';
                $event->rules['notification/savenotification']='notification/notification/savenotification';
                $event->rules['notification/settings'] = 'notification/notification/setting';
                $event->rules['notification/craftedit/<id:\d+>'] = 'notification/notification/craftnotificationedit';
                $event->rules['notification/deletenotification/<id:\d+>'] = 'notification/notification/deletecraftnotification';


            });





        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = NotificationWidgetWidget::class;
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    ConsoleController::setcronjob();
                }
            }
        );


        // components events starts from here


            Event::on(Elements::class,Elements::EVENT_AFTER_SAVE_ELEMENT,function ($event){
                $ElementsController = new ElementsController();
                $ElementsController->actionOnSaveElementEvent($event);
            });


            Event::on(Elements::class,Elements::EVENT_AFTER_DELETE_ELEMENT,function ($event){

                $ElementsController = new ElementsController();
                $ElementsController->actionOnDeleteElements($event);
            });



        //components events stops
/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'notification',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
