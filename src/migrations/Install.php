<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\migrations;

use craft\db\Migration;
use putyourlightson\blitzrecommendations\records\RecommendationRecord;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        if (!$this->db->tableExists(RecommendationRecord::tableName())) {
            $this->createTable(RecommendationRecord::tableName(), [
                'id' => $this->primaryKey(),
                'key' => $this->string()->notNull(),
                'template' => $this->string(),
                'line' => $this->integer(),
                'message' => $this->text(),
                'info' => $this->text(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, RecommendationRecord::tableName(), ['key', 'template'], true);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(RecommendationRecord::tableName());

        return true;
    }
}
