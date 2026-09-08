<?php

namespace Kanboard\Plugin\Bigboard\Schema;

use PDO;

const VERSION = 5;

function version_4(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bigboard_selected (
          id SERIAL PRIMARY KEY,
          user_id INTEGER NOT NULL,
          project_id INTEGER NOT NULL,
          UNIQUE(user_id, project_id)
        )
    ");	
	$pdo->exec("
        CREATE TABLE IF NOT EXISTS bigboard_collapsed (
          id SERIAL PRIMARY KEY,
          user_id INTEGER NOT NULL,
          project_id INTEGER NOT NULL,
          UNIQUE(user_id, project_id)
        )
    ");
}

function version_5(PDO $pdo)
{
    $pdo->exec('ALTER TABLE bigboard_selected ADD COLUMN position INT DEFAULT 0');
    $pdo->exec('UPDATE bigboard_selected SET position = (SELECT COUNT(*) FROM bigboard_selected AS b WHERE b.user_id = bigboard_selected.user_id AND b.id < bigboard_selected.id) + 1');
}
