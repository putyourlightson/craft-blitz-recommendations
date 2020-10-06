<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations;

use Craft;
use craft\base\Plugin;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use putyourlightson\blitzrecommendations\services\RecommendationsService;
use putyourlightson\blitzrecommendations\utilities\RecommendationsUtility;
use yii\base\Event;

/**
 * @property RecommendationsService $recommendations
 */
class BlitzRecommendations extends Plugin
{
    /**
     * @var BlitzRecommendations
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->setComponents([
            'recommendations' => RecommendationsService::class,
        ]);

        $this->_registerEvents();
        $this->_registerUtilities();
    }

    /**
     * Registers events
     */
    private function _registerEvents()
    {
        // Register element query prepare event
        Event::on(ElementQuery::class, ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                // Ignore CP requests
                if (Craft::$app->getRequest()->getIsCpRequest()) {
                    return;
                }

                /** @var ElementQuery $elementQuery */
                $elementQuery = $event->sender;
                $this->recommendations->checkElementQuery($elementQuery);
            },
            null,
            false
        );
    }

    /**
     * Registers utilities
     */
    private function _registerUtilities()
    {
        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = RecommendationsUtility::class;
            }
        );
    }
}
