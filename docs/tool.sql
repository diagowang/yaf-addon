-- 获取列 字符串 数组
SELECT GROUP_CONCAT(CONCAT("':",COLUMN_NAME,"' => ") SEPARATOR ",\n")
FROM information_schema.COLUMNS
WHERE table_schema='database' AND table_name = 'table_name';

SELECT GROUP_CONCAT(CONCAT("'",COLUMN_NAME,"'") SEPARATOR ",\n")
FROM information_schema.COLUMNS
WHERE table_schema='database' AND table_name = 'table_name';
-- 获取列 字符串 字符串
SELECT GROUP_CONCAT(CONCAT("`",COLUMN_NAME,"`") SEPARATOR ", ")
FROM information_schema.COLUMNS
WHERE table_schema='database' AND table_name = 'table_name';

SELECT GROUP_CONCAT(CONCAT(":",COLUMN_NAME,"") SEPARATOR ", ")
FROM information_schema.COLUMNS
WHERE table_schema='database' AND table_name = 'table_name';
-- 获取列 字符串 字符串
SELECT GROUP_CONCAT(CONCAT(":",COLUMN_NAME,"") SEPARATOR ", ")
FROM information_schema.COLUMNS
WHERE table_schema='database' AND table_name = 'table_name';