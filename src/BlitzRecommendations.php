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
use craft\web\Response;
use putyourlightson\blitzrecommendations\services\RecommendationsService;
use putyourlightson\blitzrecommendations\utilities\RecommendationsUtility;
use yii\base\Event;
use yii\web\Response as BaseResponse;

/**
 * @property RecommendationsService $recommendations
 */
class BlitzRecommendations extends Plugin
{
    /**
     * @var BlitzRecommendations
     */
    public static BlitzRecommendations $plugin;

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'recommendations' => ['class' => RecommendationsService::class],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public string $schemaVersion = '2.0.0';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerEvents();
        $this->_registerUtilities();
    }

    /**
     * Registers events
     */
    private function _registerEvents()
    {
        // Ignore CP requests
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        // Register element query prepare event
        Event::on(ElementQuery::class, ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                /** @var ElementQuery $elementQuery */
                $elementQuery = $event->sender;
                $this->recommendations->checkElementQuery($elementQuery);
            },
            null,
            false
        );

        // Register element query prepare event
        Event::on(Response::class, BaseResponse::EVENT_AFTER_PREPARE,
            function() {
                $this->recommendations->save();
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
