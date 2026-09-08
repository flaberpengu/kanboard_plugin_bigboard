<?php

namespace Kanboard\Plugin\Bigboard\Schema;

use PDO;

const VERSION = 5;

function version_4(PDO $pdo)
{
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS bigboard_selected (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT(11) NOT NULL,
          project_id INT(11) NOT NULL,
          UNIQUE(user_id, project_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ');
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS bigboard_collapsed (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT(11) NOT NULL,
          project_id INT(11) NOT NULL,
          UNIQUE(user_id, project_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ');
}

function version_5(PDO $pdo)
{
    $pdo->exec('ALTER TABLE bigboard_selected ADD position INT DEFAULT 0');
    $pdo->exec('UPDATE bigboard_selected SET position = (SELECT COUNT(*) FROM bigboard_selected AS b WHERE b.user_id = bigboard_selected.user_id AND b.id < bigboard_selected.id) + 1');
}
