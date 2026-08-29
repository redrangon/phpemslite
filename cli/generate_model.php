<?php
/**
 * Model 生成脚本
 * 功能：根据数据库表结构生成 Model 类文件
 * 用法：php generate_model.php --module=模块名 --table=表名
 *       支持多表生成，用英文逗号分隔：--table=user,user_group
 */

use PHPEMS\Lib\DataBase\DBProvider;
use PHPEMS\Lib\DI\DI;

// 定义项目根目录
define("PEPATH", dirname(__DIR__));

// 加载依赖
require_once(PEPATH."/vendor/autoload.php");
require_once(PEPATH."/boot/helpers.php");

// 绑定数据库连接
DI::bind('mysql.default', function(){
    return DBProvider::Create();
});

/**
 * 生成单个表的 Model 类
 * @param string $module 模块名
 * @param string $table 表名（不含前缀）
 * @param object $db 数据库实例
 * @return bool 是否生成成功
 */
function generateModel($module, $table, $db)
{
    // 获取表前缀
    $tablePrefix = $db->getConfig('tablePrefix');
    $fullTableName = $tablePrefix . $table;

    // 检查表是否存在
    $sql = "SHOW TABLES LIKE '{$fullTableName}'";
    $checkTable = $db->fetch($sql);
    if (!$checkTable) {
        echo "错误：表 {$fullTableName} 不存在\n";
        return false;
    }

    echo "正在分析表 {$fullTableName}...\n";

    // 获取表结构
    $sql = "SHOW COLUMNS FROM {$fullTableName}";
    $columns = $db->fetchAll($sql);

    if (empty($columns)) {
        echo "错误：无法获取表结构\n";
        return false;
    }

    // 分析字段信息
    $primaryKeys = [];
    $fieldInfo = [];
    $nonPrimaryFields = [];

    foreach ($columns as $column) {
        $fieldName = $column['Field'];
        $fieldType = $column['Type'];
        $isNullable = $column['Null'] === 'YES';
        $default = $column['Default'];
        $isPrimary = $column['Key'] === 'PRI';

        if ($isPrimary) {
            $primaryKeys[] = $fieldName;
        } else {
            $nonPrimaryFields[] = $fieldName;
        }

        // 确定字段类型
        $typeCategory = 'string';
        if (preg_match('/^(int|tinyint|smallint|mediumint|bigint|bit|bool|boolean)/i', $fieldType)) {
            $typeCategory = 'integer';
        } elseif (preg_match('/^(decimal|double|float|real)/i', $fieldType)) {
            $typeCategory = 'float';
        } elseif (preg_match('/^(date|datetime|timestamp|time|year)/i', $fieldType)) {
            $typeCategory = 'date';
        } elseif (preg_match('/^(text|longtext|mediumtext|tinytext|char|varchar)/i', $fieldType)) {
            $typeCategory = 'string';
        } elseif (preg_match('/^(json)/i', $fieldType)) {
            $typeCategory = 'json';
        }

        $fieldInfo[$fieldName] = [
            'type' => $typeCategory,
            'nullable' => $isNullable,
            'default' => $default,
            'isPrimary' => $isPrimary
        ];
    }

    if (empty($primaryKeys)) {
        echo "警告：表 {$fullTableName} 没有主键\n";
    }

    // 生成 Model 类名（驼峰命名）
    $modelName = '';
    $parts = explode('_', $table);
    foreach ($parts as $part) {
        $modelName .= ucfirst($part);
    }

    // 生成 defaultValues
    $defaultValues = [];
    foreach ($nonPrimaryFields as $field) {
        $info = $fieldInfo[$field];
        switch ($info['type']) {
            case 'integer':
                $defaultValues[$field] = 0;
                break;
            case 'float':
                $defaultValues[$field] = 0.0;
                break;
            case 'string':
            case 'date':
            case 'json':
                $defaultValues[$field] = '';
                break;
            default:
                $defaultValues[$field] = null;
        }
    }

    // 生成 rules
    $rules = [];
    $ruleMessages = [];
    foreach ($nonPrimaryFields as $field) {
        $info = $fieldInfo[$field];
        $ruleList = [];

        // 检查是否为必填字段（NOT NULL 且无默认值）
        if (!$info['nullable'] && $info['default'] === null && $info['type'] !== 'date') {
            $ruleList[] = 'required';
        }

        // 添加类型规则
        switch ($info['type']) {
            case 'integer':
                $ruleList[] = 'integer';
                break;
            case 'float':
                $ruleList[] = 'float';
                break;
            case 'string':
                $ruleList[] = 'string';
                break;
            case 'date':
                $ruleList[] = 'date';
                break;
        }

        if (!empty($ruleList)) {
            $rules[$field] = implode('|', $ruleList);

            // 为 required 规则生成错误消息
            if (in_array('required', $ruleList)) {
                $ruleMessages[$field] = "请填写{$field}";
            }
        }
    }

    // 生成 Model 类内容
    $classContent = "<?php\n\n";
    $classContent .= "namespace PHPEMS\\App\\{$module}\\Service\\Model;\n\n";
    $classContent .= "use PHPEMS\\Lib\\Rules\\Model;\n\n";
    $classContent .= "class {$modelName} extends Model\n";
    $classContent .= "{\n";
    $classContent .= "    protected static \$dbName = 'mysql.default'; // 连接名\n";
    $classContent .= "    protected static \$dbTable = '{$table}'; // 表名\n";
    $classContent .= "    protected static \$primaryKeys = ['" . implode("', '", $primaryKeys) . "']; // 主键名\n\n";
    $classContent .= "    /**\n";
    $classContent .= "     * 默认值（不含主键字段）\n";
    $classContent .= "     * 字符串类型默认值为空字符串 \"\"\n";
    $classContent .= "     * 整数类型默认值为 0\n";
    $classContent .= "     * 可空字段默认值为 null\n";
    $classContent .= "     */\n";
    $classContent .= "    protected static \$defaultValues = [\n";
    foreach ($defaultValues as $field => $value) {
        if ($value === null) {
            $classContent .= "        '{$field}' => null,\n";
        } elseif (is_string($value)) {
            $classContent .= "        '{$field}' => \"\",\n";
        } elseif (is_int($value)) {
            $classContent .= "        '{$field}' => {$value},\n";
        } elseif (is_float($value)) {
            $classContent .= "        '{$field}' => {$value},\n";
        }
    }
    $classContent .= "    ];\n\n";
    $classContent .= "    /**\n";
    $classContent .= "     * 字段校验规则（不含主键字段）\n";
    $classContent .= "     * required: 必填（NOT NULL 且无默认值的字段）\n";
    $classContent .= "     * integer: 整数类型\n";
    $classContent .= "     * float: 浮点数类型\n";
    $classContent .= "     * string: 字符串类型\n";
    $classContent .= "     * date: 日期类型\n";
    $classContent .= "     */\n";
    $classContent .= "    protected static \$rules = [\n";
    foreach ($rules as $field => $rule) {
        $classContent .= "        '{$field}' => '{$rule}',\n";
    }
    $classContent .= "    ];\n\n";
    $classContent .= "    /**\n";
    $classContent .= "     * 字段校验规则失败后的提示信息\n";
    $classContent .= "     */\n";
    $classContent .= "    protected static \$ruleMessages = [\n";
    foreach ($ruleMessages as $field => $message) {
        $classContent .= "        '{$field}' => '{$message}',\n";
    }
    $classContent .= "    ];\n";
    $classContent .= "}\n";

    // 确定目标文件路径
    $targetDir = PEPATH . "/app/{$module}/Service/Model";
    $targetFile = $targetDir . "/{$modelName}.php";

    // 检查目录是否存在，不存在则创建
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            echo "错误：无法创建目录 {$targetDir}\n";
            return false;
        }
    }

    // 写入文件
    if (file_put_contents($targetFile, $classContent)) {
        echo "成功生成 Model 类文件：{$targetFile}\n";
        echo "  模块：{$module}\n";
        echo "  表名：{$fullTableName}\n";
        echo "  类名：{$modelName}\n";
        echo "  主键：" . implode(', ', $primaryKeys) . "\n";
        echo "  字段数：" . count($columns) . "\n";
        echo "  非主键字段：" . count($nonPrimaryFields) . "\n";
        echo "  必填字段：" . count($rules) . "\n";
        echo "\n";
        return true;
    } else {
        echo "错误：无法写入文件 {$targetFile}\n";
        return false;
    }
}

// 获取命令行参数
$module = '';
$table = '';

for ($i = 1; $i < $argc; $i++) {
    $arg = $argv[$i];
    // 处理 --module 参数
    if (strpos($arg, '--module=') === 0) {
        $value = substr($arg, 9);
        // 去除可能的多余等号
        $value = ltrim($value, '=');
        $module = $value;
    }
    // 处理 --table 参数
    elseif (strpos($arg, '--table=') === 0) {
        $value = substr($arg, 8);
        // 去除可能的多余等号
        $value = ltrim($value, '=');
        $table = $value;
    }
}

// 验证参数
if (empty($module) || empty($table)) {
    echo "错误：缺少必需参数\n\n";
    echo "用法：php generate_model.php --module=模块名 --table=表名\n\n";
    echo "参数说明：\n";
    echo "  --module  模块名称（首字母大写，如：User, Course, Exam）\n";
    echo "  --table   数据表表名（不含表前缀，多个表用英文逗号分隔）\n\n";
    echo "示例：\n";
    echo "  php generate_model.php --module=User --table=user\n";
    echo "  php generate_model.php --module=User --table=user,user_group\n";
    echo "  php generate_model.php --module=Course --table=course\n";
    exit(1);
}

// 模块名首字母大写
$module = ucfirst($module);

// 清理表名，去除前导的等号
$table = ltrim($table, '=');

// 获取数据库实例
try {
    $db = DI::getInstance()->get('mysql.default');
    echo "数据库连接成功\n\n";
} catch (Exception $e) {
    echo "错误：数据库连接失败 - " . $e->getMessage() . "\n";
    exit(1);
}

// 解析表名（支持逗号分隔）
$tables = array_map('trim', explode(',', $table));
$tableCount = count($tables);

echo "开始生成 {$tableCount} 个 Model 类文件...\n";
echo str_repeat("=", 50) . "\n\n";

// 生成统计信息
$successCount = 0;
$failedCount = 0;

// 循环生成每个表的 Model
foreach ($tables as $index => $tableName) {
    $currentNum = $index + 1;
    echo "({$currentNum}/{$tableCount}) 处理表：{$tableName}\n";
    if (generateModel($module, $tableName, $db)) {
        $successCount++;
    } else {
        $failedCount++;
    }
    echo str_repeat("-", 50) . "\n";
}

// 输出汇总信息
echo "\n" . str_repeat("=", 50) . "\n";
echo "生成完成！\n";
echo "总计：{$tableCount} 个表\n";
echo "成功：{$successCount} 个\n";
echo "失败：{$failedCount} 个\n";
echo str_repeat("=", 50) . "\n";

// 退出代码
exit($failedCount > 0 ? 1 : 0);