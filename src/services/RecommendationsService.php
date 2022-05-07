<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

/**
 * @noinspection PhpInternalEntityUsedInspection
 */

namespace putyourlightson\blitzrecommendations\services;

use Craft;
use craft\base\Component;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\MatrixBlockQuery;
use craft\services\Deprecator;
use putyourlightson\blitzrecommendations\models\RecommendationModel;
use putyourlightson\blitzrecommendations\records\RecommendationRecord;
use ReflectionClass as ReflectionClassAlias;
use Twig\Template;

/**
 * @property int $total
 * @property RecommendationModel[] $all
 */
class RecommendationsService extends Component
{
    /**
     * @var RecommendationModel[] The recommendations to be saved for the current request.
     */
    private array $_recommendations = [];

    /**
     * Gets total recommendations.
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
    public function clearAll(): void
    {
        RecommendationRecord::deleteAll();
    }

    /**
     * Clears a recommendation.
     */
    public function clear(int $id): void
    {
        RecommendationRecord::deleteAll([
            'id' => $id,
        ]);
    }

    /**
     * Checks for opportunities to eager-loading elements.
     */
    public function checkElementQuery(ElementQuery $elementQuery): void
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
     */
    public function add(string $key, string $message, string $info = ''): void
    {
        [$path, $line] = $this->_getTemplatePathLine();

        $this->_recommendations[$key.'-'.$path] = new RecommendationModel([
            'key' => $key,
            'template' => $path,
            'line' => $line,
            'message' => $message,
            'info' => $info,
        ]);
    }

    /**
     * Saves recommendations.
     *
     * @noinspection MissedFieldInspection
     */
    public function save(): void
    {
        $db = Craft::$app->getDb();

        foreach ($this->_recommendations as $recommendation) {
            $db->createCommand()
                ->upsert(
                    RecommendationRecord::tableName(),
                    [
                        'key' => $recommendation->key,
                        'template' => $recommendation->template,
                        'line' => $recommendation->line,
                        'message' => $recommendation->message,
                        'info' => $recommendation->info,
                    ],
                    [
                        'template' => $recommendation->template,
                        'line' => $recommendation->line,
                        'message' => $recommendation->message,
                        'info' => $recommendation->info,
                    ])
                ->execute();
        }
    }

    /**
     * Returns the path and line number of the rendered template.
     */
    private function _getTemplatePathLine(): array
    {
        // Get the debug backtrace
        $traces = debug_backtrace();

        // Get template class filename
        $reflector = new ReflectionClassAlias(Template::class);
        $filename = $reflector->getFileName();

        foreach ($traces as $key => $trace) {
            if (!empty($trace['file']) && $trace['file'] == $filename) {
                $template = $trace['object'] ?? null;

                if ($template instanceof Template) {
                    $path = $template->getSourceContext()->getPath();
                    $templateCodeLine = $traces[$key - 1]['line'] ?? null;
                    $line = $this->_findTemplateLine($template, $templateCodeLine);

                    return [$path, $line];
                }
            }
        }

        return ['', null];
    }

    /**
     * Checks base relations.
     * @see \craft\fields\BaseRelationField::normalizeValue
     */
    private function _checkBaseRelations(ElementQuery $elementQuery): void
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
     */
    private function _checkMatrixRelations(MatrixBlockQuery $elementQuery): void
    {
        if (empty($elementQuery->fieldId) || empty($elementQuery->ownerId)) {
            return;
        }

        $fieldId = is_array($elementQuery->fieldId) ? $elementQuery->fieldId[0] : $elementQuery->fieldId;

        $this->_addField($fieldId);
    }

    /**
     * Adds a field recommendation.
     */
    private function _addField(int $fieldId): void
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
            'link' => '<a href="https://craftcms.com/docs/4.x/dev/eager-loading-elements.html" class="go" target="_blank">Docs</a>',
        ]);

        $this->add($fieldId, $message, $info);
    }

    /**
     * Returns the template line number.
     *
     * @see Deprecator::_findTemplateLine()
     */
    private function _findTemplateLine(Template $template, int $actualCodeLine = null)
    {
        if ($actualCodeLine === null) {
            return null;
        }

        // getDebugInfo() goes upward, so the first code line that's <= the trace line will be the match
        foreach ($template->getDebugInfo() as $codeLine => $templateLine) {
            if ($codeLine <= $actualCodeLine) {
                return $templateLine;
            }
        }

        return null;
    }
}
