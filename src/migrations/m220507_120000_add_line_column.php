<?php

namespace putyourlightson\blitzrecommendations\migrations;

use craft\db\Migration;
use putyourlightson\blitzrecommendations\records\RecommendationRecord;

class m220507_120000_add_line_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        if (!$this->db->columnExists(RecommendationRecord::tableName(), 'line')) {
            $this->addColumn(
                RecommendationRecord::tableName(),
                'line',
                $this->integer()->after('template'),
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo self::class . " cannot be reverted.\n";

        return false;
    }
}
