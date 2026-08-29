<?php
/**
 * 控制器生成脚本
 * 功能：根据参数生成控制器类文件
 * 用法：php generate_controller.php --module=模块名 --type=类型名 --controller=控制器名
 */

// 定义项目根目录
define("PEPATH", dirname(__DIR__));

// 获取命令行参数
$module = '';
$type = '';
$controller = '';

for ($i = 1; $i < $argc; $i++) {
    $arg = $argv[$i];
    if (str_starts_with($arg, '--module=')) {
        $value = substr($arg, 9);
        $value = ltrim($value, '=');
        $module = $value;
    } elseif (str_starts_with($arg, '--type=')) {
        $value = substr($arg, 7);
        $value = ltrim($value, '=');
        $type = $value;
    } elseif (str_starts_with($arg, '--controller=')) {
        $value = substr($arg, 12);
        $value = ltrim($value, '=');
        $controller = $value;
    }
}

// 验证参数
if (empty($module) || empty($type) || empty($controller)) {
    echo "错误：缺少必需参数\n\n";
    echo "用法：php generate_controller.php --module=模块名 --type=类型名 --controller=控制器名\n\n";
    echo "参数说明：\n";
    echo "  --module      模块名称（首字母大写，如：User, Course, Exam）\n";
    echo "  --type        类型名称（首字母大写，如：Master, App）\n";
    echo "  --controller  控制器名称（首字母大写，驼峰命名，如：User, Course, Order）\n\n";
    echo "示例：\n";
    echo "  php generate_controller.php --module=User --type=Master --controller=User\n";
    echo "  php generate_controller.php --module=Course --type=App --controller=Course\n";
    echo "  php generate_controller.php --module=Exam --type=Master --controller=Exam\n";
    exit(1);
}

// 首字母大写转换
$module = ucfirst($module);
$type = ucfirst($type);
$controller = ucfirst($controller);

// 生成控制器内容
$namespace = "PHPEMS\\App\\{$module}\\Controller\\{$type}";
$controllerClass = $controller;

// 生成默认的 use 语句
$uses = [
    "use PHPEMS\\App\\{$module}\\Service\\Model\\{$controller};",
    "use PHPEMS\\Lib\\Core\\Request\\Json;",
    "use PHPEMS\\Lib\\Rules\\Controller;",
    "use PHPEMS\\Lib\\Rules\\ControllerInterface;",
    "use PHPEMS\\Lib\\Rules\\Error;",
];

// 检查是否存在对应的 Model 类
$modelFilePath = PEPATH . "/app/{$module}/Service/Model/{$controller}.php";
if (file_exists($modelFilePath)) {
    // Model 存在，保留 use 语句
} else {
    // Model 不存在，移除 use 语句
    $uses = array_filter($uses, function($use) use ($controller) {
        return !strpos($use, "Service\\Model\\{$controller}");
    });
}

// 生成控制器类内容
$classContent = "<?php\n\n";
$classContent .= "namespace {$namespace};\n\n";
foreach ($uses as $use) {
    $classContent .= $use . "\n";
}
$classContent .= "\n";
$classContent .= "class {$controllerClass} extends Controller implements ControllerInterface\n";
$classContent .= "{\n";
$classContent .= "    protected Json \$request;\n";
$classContent .= "    protected array \$publicFlows = ['Auth','Json'];\n\n";
$classContent .= "    public function getRoutes():array\n";
$classContent .= "    {\n";
$classContent .= "        return [\n";
$classContent .= "            'index' => 'Index',\n";
$classContent .= "            'data' => 'Data',\n";
$classContent .= "            'modify' => 'Modify',\n";
$classContent .= "            'delete' => 'Delete',\n";
$classContent .= "            'add' => 'Add'\n";
$classContent .= "        ];\n";
$classContent .= "    }\n\n";
$classContent .= "    public function withFlows(\$action = 'index'):array\n";
$classContent .= "    {\n";
$classContent .= "        \$flows = [];\n";
$classContent .= "        return \$flows[\$action]??\$this->publicFlows;\n";
$classContent .= "    }\n\n";
$classContent .= "    public function withOutFlows(\$action = 'index'):array\n";
$classContent .= "    {\n";
$classContent .= "        \$outFlows = [];\n";
$classContent .= "        return \$outFlows[\$action]??[];\n";
$classContent .= "    }\n\n";
$classContent .= "    /**\n";
$classContent .= "     * 添加操作\n";
$classContent .= "     */\n";
$classContent .= "    public function Add():array\n";
$classContent .= "    {\n";
$classContent .= "        // 获取请求数据\n";
$classContent .= "        \$data = [];\n\n";
$classContent .= "        // TODO: 实现添加逻辑\n";
$classContent .= "        // 示例：\n";
$classContent .= "        // \$model = {$controller}::fill(\$data);\n";
$classContent .= "        // if(!\$model->save()) return error(['error' => '添加失败']);\n\n";
$classContent .= "        return ['msg' => '添加成功'];\n";
$classContent .= "    }\n\n";
$classContent .= "    /**\n";
$classContent .= "     * 修改操作\n";
$classContent .= "     */\n";
$classContent .= "    public function modify():array|Error\n";
$classContent .= "    {\n";
$classContent .= "        // TODO: 实现修改逻辑\n";
$classContent .= "        // 示例：\n";
$classContent .= "        // \$id = \$this->request->id;\n";
$classContent .= "        // \$model = {$controller}::find(\$id);\n";
$classContent .= "        // if(!\$model) return error(['error' => '记录不存在']);\n";
$classContent .= "        // \$data = [];\n";
$classContent .= "        // foreach (\$data as \$key => \$value) {\n";
$classContent .= "        //     \$model->\$key = \$value;\n";
$classContent .= "        // }\n";
$classContent .= "        // if(!\$model->save()) return error(['error' => '修改失败']);\n\n";
$classContent .= "        return ['msg' => '修改成功'];\n";
$classContent .= "    }\n\n";
$classContent .= "    /**\n";
$classContent .= "     * 删除操作\n";
$classContent .= "     */\n";
$classContent .= "    public function delete():array|Error\n";
$classContent .= "    {\n";
$classContent .= "        // TODO: 实现删除逻辑\n";
$classContent .= "        // 示例：\n";
$classContent .= "        // \$ids = \$this->request->ids;\n";
$classContent .= "        // if(empty(\$ids)) return error(['error' => '未选择要删除的记录']);\n";
$classContent .= "        // {$controller}::getQuery()->whereIn('id', \$ids)->delete();\n\n";
$classContent .= "        return ['msg' => '删除成功'];\n";
$classContent .= "    }\n\n";
$classContent .= "    /**\n";
$classContent .= "     * 获取数据列表\n";
$classContent .= "     */\n";
$classContent .= "    public function Data(): array\n";
$classContent .= "    {\n";
$classContent .= "        // TODO: 实现数据查询逻辑\n";
$classContent .= "        // 示例：\n";
$classContent .= "        // \$query = {$controller}::getQuery()->orderBy('id', 'DESC');\n";
$classContent .= "        // \$page = \$this->request->page ?? 1;\n";
$classContent .= "        // \$limit = \$this->request->limit ?? 20;\n";
$classContent .= "        // \$search = \$this->request->search;\n";
$classContent .= "        // if(\$search) {\n";
$classContent .= "        //     // 处理搜索条件\n";
$classContent .= "        // }\n";
$classContent .= "        // \$data = \$query->paginate(\$page, \$limit);\n";
$classContent .= "        // return \$data;\n\n";
$classContent .= "        return [];\n";
$classContent .= "    }\n\n";
$classContent .= "    public function Index(): Error|array\n";
$classContent .= "    {\n";
$classContent .= "        return [];\n";
$classContent .= "    }\n";
$classContent .= "}\n";

// 确定目标文件路径
$targetDir = PEPATH . "/app/{$module}/Controller/{$type}";
$targetFile = $targetDir . "/{$controller}.php";

// 检查目录是否存在，不存在则创建
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        echo "错误：无法创建目录 {$targetDir}\n";
        exit(1);
    }
}

// 检查文件是否已存在
if (file_exists($targetFile)) {
    echo "警告：文件 {$targetFile} 已存在\n";
    echo "是否覆盖？";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    if (trim(strtolower($line)) !== 'y') {
        echo "操作已取消\n";
        exit(0);
    }
}

// 写入文件
if (file_put_contents($targetFile, $classContent)) {
    echo "成功生成控制器类文件：{$targetFile}\n";
    echo "\n生成的类信息：\n";
    echo "  模块：{$module}\n";
    echo "  类型：{$type}\n";
    echo "  控制器：{$controller}\n";
    echo "  命名空间：{$namespace}\n";
    echo "  文件路径：{$targetFile}\n";

    // 显示可用的路由
    echo "\n可用的路由方法：\n";
    echo "  index  -> Index()      // 默认首页\n";
    echo "  data   -> Data()       // 获取数据列表\n";
    echo "  modify -> modify()     // 修改操作\n";
    echo "  delete -> delete()     // 删除操作\n";
    echo "  add    -> Add()        // 添加操作\n";

    echo "\n路由访问路径：\n";
    echo "  /{$module}/{$type}/{$controller}/action\n";
    echo "\n提示：\n";
    echo "  - 所有方法都标记了 TODO，请根据实际需求实现业务逻辑\n";
    echo "  - 记得修改 getRoutes() 中的路由映射\n";
    echo "  - 如果使用了自定义 Service，记得添加 use 语句\n";

    exit(0);
} else {
    echo "错误：无法写入文件 {$targetFile}\n";
    exit(1);
}
