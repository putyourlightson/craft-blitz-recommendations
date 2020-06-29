<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\services;

use Craft;
use craft\base\Component;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\MatrixBlockQuery;
use putyourlightson\blitzrecommendations\models\RecommendationModel;
use putyourlightson\blitzrecommendations\records\RecommendationRecord;
use ReflectionClass as ReflectionClassAlias;
use Twig\Template;
use Twig\Template as TwigTemplate;
use yii\base\Application;

/**
 * @property int $total
 * @property RecommendationModel[] $all
 */
class RecommendationsService extends Component
{
    /**
     * @var RecommendationModel[] The recommendations to be saved for the current request.
     */
    private $_recommendations = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Craft::$app->on(Application::EVENT_AFTER_REQUEST, [$this, 'save'], null, false);

        parent::init();
    }

    /**
     * Gets total recommendations.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return RecommendationRecord::find()->count();
    }

    /**
     * Gets all recommendations.
     *
     * @return RecommendationModel[]
     */
    public function getAll(): array
    {
        $recommendations = [];

        $recommendationRecords = RecommendationRecord::find()
            ->orderBy(['dateUpdated' => SORT_DESC])
            ->all();

        foreach ($recommendationRecords as $record) {
            $recommendation = new RecommendationModel();
            $recommendation->setAttributes($record->getAttributes(), false);
            $recommendations[] = $recommendation;
        }

        return $recommendations;
    }

    /**
     * Clears all recommendations.
     */
    public function clearAll()
    {
        RecommendationRecord::deleteAll();
    }

    /**
     * Clears a recommendation.
     *
     * @param int $id
     */
    public function clear(int $id)
    {
        RecommendationRecord::deleteAll([
            'id' => $id,
        ]);
    }

    /**
     * Checks for opportunities to eager-loading elements.
     *
     * @param ElementQuery $elementQuery
     */
    public function checkElementQuery(ElementQuery $elementQuery)
    {
        if ($elementQuery instanceof MatrixBlockQuery) {
            $this->_checkMatrixRelations($elementQuery);
        }
        else {
            $this->_checkBaseRelations($elementQuery);
        }
    }

    /**
     * Adds a recommendation.
     *
     * @param string $key
     * @param string $message
     * @param string|null $info
     */
    public function add(string $key, string $message, string $info = '')
    {
        $template = $this->_getTemplate();

        $this->_recommendations[$key.'-'.$template] = new RecommendationModel([
            'key' => $key,
            'template' => $template,
            'message' => $message,
            'info' => $info,
        ]);
    }

    /**
     * Saves recommendations.
     */
    public function save()
    {
        $db = Craft::$app->getDb();

        foreach ($this->_recommendations as $recommendation) {
            $db->createCommand()
                ->upsert(
                    RecommendationRecord::tableName(),
                    [
                        'key' => $recommendation->key,
                        'template' => $recommendation->template,
                    ],
                    [
                        'template' => $recommendation->template,
                        'message' => $recommendation->message,
                        'info' => $recommendation->info,
                    ])
                ->execute();
        }
    }

    /**
     * Returns path to the currently rendered template.
     *
     * @return string
     */
    private function _getTemplate(): string
    {
        // Get the debug backtrace
        $traces = debug_backtrace();

        // Get template class filename
        $reflector = new ReflectionClassAlias(Template::class);
        $filename = $reflector->getFileName();

        foreach ($traces as $key => $trace) {
            if (!empty($trace['file']) && $trace['file'] == $filename) {
                $template = $trace['object'] ?? null;

                if ($template instanceof TwigTemplate) {
                    $templateName = $template->getTemplateName();

                    return Craft::$app->getView()->resolveTemplate($templateName) ?: $templateName;
                }

                return '';
            }
        }

        return '';
    }

    /**
     * Checks base relations.
     * @see \craft\fields\BaseRelationField::normalizeValue
     *
     * @param ElementQuery $elementQuery
     */
    private function _checkBaseRelations(ElementQuery $elementQuery)
    {
        $join = $elementQuery->join[0] ?? null;

        if ($join === null) {
            return;
        }

        $relationTypes = [
            ['relations' => '{{%relations}}'],
            '{{%relations}} relations',
        ];

        if ($join[0] == 'INNER JOIN' && in_array($join[1], $relationTypes)) {
            $fieldId = $join[2][2]['relations.fieldId'] ?? null;

            if (empty($fieldId)) {
                return;
            }

            $this->_addField($fieldId);
        }
    }

    /**
     * Checks matrix relations.
     * @see \craft\elements\db\MatrixBlockQuery::beforePrepare
     *
     * @param MatrixBlockQuery $elementQuery
     */
    private function _checkMatrixRelations(MatrixBlockQuery $elementQuery)
    {
        if (empty($elementQuery->fieldId) || empty($elementQuery->ownerId)) {
            return;
        }

        $fieldId = is_array($elementQuery->fieldId) ? $elementQuery->fieldId[0] : $elementQuery->fieldId;

        $this->_addField($fieldId);
    }

    /**
     * Adds a field recommendation.
     *
     * @param int $fieldId
     */
    private function _addField(int $fieldId)
    {
        /** @var Field $field */
        $field = Craft::$app->getFields()->getFieldById($fieldId);

        if ($field === null) {
            return;
        }

        $message = Craft::t('blitz-recommendations', 'Eager-load the `{fieldName}` field.', ['fieldName' => $field->name]);
        $info = Craft::t('blitz-recommendations', 'Use the `with` parameter to eager-load sub-elements of the `{fieldName}` field.<br>{example}<br>{link}', [
            'fieldName' => $field->name,
            'example' => '`{% set entries = craft.entries.with([\''.$field->handle.'\']).all() %}`',
            'link' => '<a href="https://docs.craftcms.com/v3/dev/eager-loading-elements.html" class="go" target="_blank">Docs</a>',
        ]);

        $this->add($fieldId, $message, $info);
    }
}
