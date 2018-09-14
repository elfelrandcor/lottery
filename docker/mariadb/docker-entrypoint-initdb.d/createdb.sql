CREATE DATABASE IF NOT EXISTS `yii2advanced` COLLATE 'utf8mb4_general_ci';
GRANT ALL ON `yii2advanced`.* TO 'root'@'%';

CREATE DATABASE IF NOT EXISTS `yii2advanced_test` COLLATE 'utf8mb4_general_ci';
GRANT ALL ON `yii2advanced_test`.* TO 'root'@'%';

FLUSH PRIVILEGES ;
