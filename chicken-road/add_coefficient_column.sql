-- SQL скрипт для добавления колонки коэффициента ловушки в таблицу users

-- Проверяем существует ли колонка
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'chicken_road' 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'chicken_trap_coefficient';

-- Добавляем колонку если её нет
ALTER TABLE users ADD COLUMN IF NOT EXISTS chicken_trap_coefficient DECIMAL(5,2) DEFAULT NULL COMMENT 'Коэффициент ловушки для hack bot системы';

-- Проверяем структуру таблицы
DESCRIBE users;
