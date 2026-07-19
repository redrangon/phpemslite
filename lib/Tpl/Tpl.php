<?php
/**
 * PHPEMS 简化版模板引擎
 *
 * 特性：
 * - 支持 Smarty 风格模板语法
 * - 支持现代数组数据传递
 * - 支持自定义定界符
 * - 支持模板编译检查
 * - 支持模板继承/包含
 * - 支持插件系统
 * - 支持变量修饰符
 * - 无缓存功能
 * - 兼容 PHP 7.1+
 *
 * @author PHPEMS Team
 * @version 2.0.0-lite
 * @license MIT
 */

namespace PHPEMS\Lib\Tpl ;

class Tpl {

	/**
	 * 模板变量存储
	 *
	 * @var array
	 */
	protected $tpl_vars = [];

	/**
	 * 模板目录
	 *
	 * @var string
	 */
	protected $template_dir = '';

	/**
	 * 编译目录
	 *
	 * @var string
	 */
	protected $compile_dir = '';

	/**
	 * 左定界符
	 *
	 * @var string
	 */
	protected $left_delimiter = '{';

	/**
	 * 右定界符
	 *
	 * @var string
	 */
	protected $right_delimiter = '}';

	/**
	 * 是否启用编译检查
	 *
	 * @var bool
	 */
	protected $compile_check = true;

	/**
	 * 自定义函数注册
	 *
	 * @var array
	 */
	protected $plugins = [];

	/**
	 * 块内容存储（用于模板继承）
	 *
	 * @var array
	 */
	protected $block_content = [];

	/**
	 * 获取所有块内容（用于调试）
	 *
	 * @return array
	 */
	public function getBlockContentData(): array
	{
		return $this->block_content;
	}

	/**
	 * PHP 代码块存储
	 *
	 * @var array
	 */
	protected $php_blocks = [];

	/**
	 * 当前块名称
	 *
	 * @var string|null
	 */
	protected $current_block = null;

    /**
     * Literal 代码块存储（用于保护 JavaScript 等）
     *
     * @var array
     */
    protected $literal_blocks = [];

    protected static $instance = null;

    public static function getInstance($config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

	/**
	 * 构造函数
	 *
	 * @param array $config 配置数组
	 */
	private function __construct(array $config = []) {
		// 默认配置
		$defaults = [
            'template_dir' => '',
            'compile_dir' => '',
            'left_delimiter' => '{',
            'right_delimiter' => '}',
            'compile_check' => true
		];

		// 合并配置
		$config = array_merge($defaults, $config);

		// 设置属性
		foreach ($config as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}

		// 确保目录存在
		$this->ensureDirectoryExists($this->compile_dir);
	}

	/**
	 * 确保目录存在
	 *
	 * @param string $dir 目录路径
	 * @return void
	 */
	protected function ensureDirectoryExists(string $dir): void
    {
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
	}

	// ============================================
	// 数据传递方法
	// ============================================

	/**
	 * 分配模板变量
	 *
	 * 支持两种方式：
	 * 1. assign('key', 'value') - 传统方式
	 * 2. assign(['key' => 'value']) - 现代方式
	 *
	 * @param mixed $var 变量名或变量数组
	 * @param mixed|null $value 变量值
	 * @return self 支持链式调用
	 */
	public function assign(mixed $var, mixed $value = null): static
    {
		if (is_array($var) && $value === null) {
			foreach ($var as $key => $val) {
				$this->tpl_vars[$key] = $val;
			}
		} else {
			$this->tpl_vars[$var] = $value;
		}
		return $this;
	}

	/**
	 * 分配模板变量（别名）
	 *
	 * @param mixed $var 变量名或变量数组
	 * @param mixed|null $value 变量值
	 * @return self
	 */
	public function with(mixed $var, mixed $value = null): static
    {
		return $this->assign($var, $value);
	}

	/**
	 * 移除模板变量
	 *
	 * @param array|string $keys 变量名或数组
	 * @return self
	 */
	public function forget(array|string $keys): static
    {
		foreach ((array)$keys as $key) {
			unset($this->tpl_vars[$key]);
		}
		return $this;
	}

	/**
	 * 获取所有模板变量
	 *
	 * @return array
	 */
	public function getVars(): array
    {
		return $this->tpl_vars;
	}

	/**
	 * 获取单个模板变量
	 *
	 * @param string $key 变量名
	 * @param mixed|null $default 默认值
	 * @return mixed
	 */
	public function getVar(string $key, mixed $default = null): mixed
    {
		return $this->tpl_vars[$key] ?? $default;
	}

	// ============================================
	// 渲染方法
	// ============================================

	/**
	 * 渲染模板并输出
	 *
	 * @param string $template 模板名称
	 * @param array $data 模板数据
	 * @return void
	 */
	public function display(string $template, array $data = []): void
    {
		echo $this->fetch($template, $data);
	}

	/**
	 * 渲染模板并返回内容
	 *
	 * @param string $template 模板名称
	 * @param array $data 模板数据
	 * @return string
	 */
	public function fetch(string $template, array $data = []): string
    {
		// 合并数据
		if (!empty($data)) {
			$this->assign($data);
		}

		// 获取文件路径
		$templateFile = $this->getTemplateFile($template);
		$compileFile = $this->getCompileFile($template);

		// 检查是否需要编译
		if ($this->compile_check && $this->needsCompile($templateFile, $compileFile)) {
			$this->compileTemplate($templateFile, $compileFile);
		}

		// 渲染模板
		return $this->renderTemplate($compileFile);
	}

	/**
	 * 渲染模板（快捷方法）
	 *
	 * @param string $template 模板名称
	 * @param array $data 模板数据
	 * @return string
	 */
	public function render(string $template, array $data = []): string
    {
		return $this->fetch($template, $data);
	}

	// ============================================
	// 文件路径方法
	// ============================================

	/**
	 * 获取模板文件路径
	 *
	 * @param string $template 模板名称
	 * @return string
	 */
	protected function getTemplateFile(string $template): string
    {
		return $this->template_dir . $template . '.tpl';
	}

	/**
	 * 获取编译文件路径（hash + 原名）
	 *
	 * @param string $template 模板名称
	 * @return string
	 */
	protected function getCompileFile(string $template): string
    {
		$hash = md5($template);
		$safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $template);
		return $this->compile_dir . $hash . '_' . $safeName . '.php';
	}

	/**
	 * 检查是否需要重新编译
	 *
	 * @param string $templateFile 模板文件路径
	 * @param string $compileFile 编译文件路径
	 * @return bool
	 */
	protected function needsCompile(string $templateFile, string $compileFile): bool
    {
		if (!file_exists($compileFile)) {
			return true;
		}

		if (!file_exists($templateFile)) {
			throw new \RuntimeException("Template file not found: {$templateFile}");
		}

		return filemtime($templateFile) > filemtime($compileFile);
	}

	// ============================================
	// 编译方法
	// ============================================

	/**
	 * 编译模板
	 *
	 * @param string $templateFile 模板文件路径
	 * @param string $compileFile 编译文件路径
	 * @return void
	 */
	protected function compileTemplate(string $templateFile, string $compileFile): void
    {
		$content = file_get_contents($templateFile);
		$compiled = $this->compileContent($content);
		file_put_contents($compileFile, $compiled);
	}

	/**
	 * 编译模板内容
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileContent(string $content): string
    {
        // 处理 literal 标签，保护 JavaScript 等代码
        $content = $this->compileLiteral($content);

        // 保存 PHP 代码块（永久禁止，仅占位）
		$content = $this->preservePhpCode($content);

		// 处理模板继承
		$content = $this->compileExtends($content);

		// 处理块定义
		$content = $this->compileBlocks($content);

		// 处理注释
		$content = $this->compileComments($content);

		// 处理变量输出
		$content = $this->compileVariables($content);

		// 处理控制结构
		$content = $this->compileControlStructures($content);

		// 处理函数调用
		$content = $this->compileFunctions($content);

        // 恢复所有代码
        $content = $this->restoreProtectCode($content);

		// 添加文件头部
		$compiled = "<?php\n";
		$compiled .= "/**\n";
		$compiled .= " * 编译时间：" . date('Y-m-d H:i:s') . "\n";
		$compiled .= " * 请勿手动修改此文件\n";
		$compiled .= " */\n\n";
        $compiled .= "?>\n";
		$compiled .= $content;

		return $compiled;
	}

	/**
	 * 渲染编译后的模板
	 *
	 * @param string $compileFile 编译文件路径
	 * @return string
	 */
	protected function renderTemplate(string $compileFile): string
    {
		extract($this->tpl_vars, EXTR_SKIP);

		// 临时屏蔽未定义变量的警告
		$oldErrorLevel = error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);

		ob_start();
		include $compileFile;
		$content = ob_get_clean();

		// 恢复原来的错误级别
		error_reporting($oldErrorLevel);

		return $content;
	}

	// ============================================
	// 编译处理器
	// ============================================

    /**
     * 编译 literal 标签（保护 JavaScript/CSS 等代码）
     *
     * @param string $content 模板内容
     * @return string
     */
    protected function compileLiteral(string $content) :string
    {
        $ld = preg_quote($this->left_delimiter, '#');
        $rd = preg_quote($this->right_delimiter, '#');

        return preg_replace_callback(
            "#{$ld}\\s*literal\\s*{$rd}(.*?){$ld}\\s*/literal\\s*{$rd}#s",
            function($matches) {
                // 将 literal 内容保存，替换为占位符
                $index = count($this->literal_blocks);
                $this->literal_blocks[$index] = $matches[1];
                return "%%LITERAL_BLOCK_{$index}%%";
            },
            $content
        );
    }

	/**
	 * 保存 PHP 代码块
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function preservePhpCode(string $content): string
    {
		return preg_replace_callback(
				'#\{php\}(.*?)\{/php\}#s',
				function($matches) {
					$index = count($this->php_blocks);
					$this->php_blocks[$index] = $matches[1];
					return "%%PHP_BLOCK_{$index}%%";
				},
				$content
		);
	}

	/**
	 * 恢复 PHP 代码（永久禁止，替换为注释）
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function restoreProtectCode(string $content): string
    {
        foreach ($this->literal_blocks as $index => $code) {
            $content = str_replace(
                "%%LITERAL_BLOCK_{$index}%%",
                $code,
                $content
            );
        }
        $this->literal_blocks = [];

        foreach ($this->php_blocks as $index => $code) {
			$content = str_replace(
					"%%PHP_BLOCK_{$index}%%",
					"<!-- PHP code is disabled for security -->",
					$content
			);
		}
		$this->php_blocks = [];
		return $content;
	}

	/**
	 * 编译注释
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileComments(string $content): string
    {
		return preg_replace('#\{\*.*?\*\}#s', '', $content);
	}

	/**
	 * 编译变量输出
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileVariables(string $content): string
    {
		$ld = preg_quote($this->left_delimiter, '#');
		$rd = preg_quote($this->right_delimiter, '#');

        // 处理 {$var|modifier1|modifier2|nofilter} - 带修饰符链和 nofilter（优先匹配）
        $content = preg_replace_callback(
            "#{$ld}\\s*\\$([a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s*(?:\\|[a-zA-Z_][a-zA-Z0-9_]*(?:\\s*:\\s*[^}\\s]*)?)+\\s*\\|\\s*nofilter\\s*{$rd}#",
            function($matches) {
                $varAccess = $this->convertVariableWithDollar('$' . $matches[1]);
                $modifiers = $this->parseModifiers($matches[0], $matches[1]);
                $phpVar = $this->buildModifierChain($varAccess, $modifiers);
                return "<?php echo {$phpVar}; ?>";
            },
            $content
        );

        // 处理 {$var|nofilter} - 单独处理 nofilter
        $content = preg_replace_callback(
            "#{$ld}\\s*\\$([a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s*\\|\\s*nofilter\\s*{$rd}#",
            function($matches) {
                $varAccess = $this->convertVariableWithDollar('$' . $matches[1]);
                return "<?php echo isset({$varAccess}) ? {$varAccess} : ''; ?>";
            },
            $content
        );

        // 处理 {$var|modifier1|modifier2} - 带修饰符链
        $content = preg_replace_callback(
            "#{$ld}\\s*\\$([a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s*(?:\\|[a-zA-Z_][a-zA-Z0-9_]*(?:\\s*:\\s*[^}\\s]*)?)+\\s*{$rd}#",
            function($matches) {
                $varAccess = $this->convertVariableWithDollar('$' . $matches[1]);
                $modifiers = $this->parseModifiers($matches[0], $matches[1]);
                $phpVar = $this->buildModifierChain($varAccess, $modifiers);
                return "<?php echo e({$phpVar}); ?>";
            },
            $content
        );

        // 处理 {$var} - 不带修饰符
        $content = preg_replace_callback(
            "#{$ld}\\s*\\$([a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s*{$rd}#",
            function($matches) {
                $varAccess = $this->convertVariableWithDollar('$' . $matches[1]);
                return "<?php echo e({$varAccess}); ?>";
            },
            $content
        );

		return $content;
	}

	/**
	 * 编译控制结构
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileControlStructures(string $content): string
	{
		$ld = preg_quote($this->left_delimiter, '#');
		$rd = preg_quote($this->right_delimiter, '#');

		// foreach ($array as $key => $value)
		$content = preg_replace_callback(
			"#{$ld}\\s*foreach\\s+(\\$[a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s+as\\s+(\\$[a-zA-Z_][a-zA-Z0-9_]*)\\s*=>\\s*(\\$[a-zA-Z_][a-zA-Z0-9_]*)\\s*{$rd}#",
			function($matches) {
				$key = substr($matches[2], 1);
				$value = substr($matches[3], 1);
				
				// 转换点号语法为数组访问语法
				$arrayVar = $this->convertDotToArrow(substr($matches[1], 1));
				return "<?php foreach (\${$arrayVar} as \${$key} => \${$value}): ?>";
			},
			$content
		);

		// foreach ($array as $value)
		$content = preg_replace_callback(
			"#{$ld}\\s*foreach\\s+(\\$[a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s+as\\s+(\\$[a-zA-Z_][a-zA-Z0-9_]*)\\s*{$rd}#",
			function($matches) {
				$value = substr($matches[2], 1);
				
				// 转换点号语法为数组访问语法
				$arrayVar = $this->convertDotToArrow(substr($matches[1], 1));
				return "<?php foreach (\${$arrayVar} as \${$value}): ?>";
			},
			$content
		);

		// /foreach
		$content = preg_replace("#{$ld}\\s*/foreach\\s*{$rd}#", "<?php endforeach; ?>", $content);

		// 编译 if/elseif（合并处理）
		$content = preg_replace_callback(
			"#{$ld}\\s*(if|elseif)\\s+(.+?)\\s*{$rd}#",
			function($matches) {
				$keyword = $matches[1];
				$expression = $this->processExpressionVariables($matches[2]);
				return "<?php {$keyword} ({$expression}): ?>";
			},
			$content
		);

		// 编译 else
		$content = preg_replace("#{$ld}\\s*else\\s*{$rd}#", "<?php else: ?>", $content);

		// 编译 /if
		$content = preg_replace("#{$ld}\\s*/if\\s*{$rd}#", "<?php endif; ?>", $content);

		// 编译 for/while（合并处理）
		$content = preg_replace_callback(
			"#{$ld}\\s*(for|while)\\s+(.+?)\\s*{$rd}#",
			function($matches) {
				$keyword = $matches[1];
				return "<?php {$keyword} ({$matches[2]}): ?>";
			},
			$content
		);

		// 编译 /for
		$content = preg_replace("#{$ld}\\s*/for\\s*{$rd}#", "<?php endfor; ?>", $content);

		// 编译 /while
		$content = preg_replace("#{$ld}\\s*/while\\s*{$rd}#", "<?php endwhile; ?>", $content);

		return $content;
	}

	/**
	 * 编译函数调用
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileFunctions(string $content): string
	{
		$ld = preg_quote($this->left_delimiter, '#');
		$rd = preg_quote($this->right_delimiter, '#');

		$content = preg_replace_callback(
				"#{$ld}\\s*([a-zA-Z_][a-zA-Z0-9_]*)\\s*(.*?)\\s*{$rd}#",
				function($matches) {
					$funcName = $matches[1];
					$args = $matches[2];

					// 检查是否是控制结构，如果是则跳过
					$controlStructures = ['if', 'elseif', 'else', 'foreach', 'for', 'while'];
					if (in_array($funcName, $controlStructures)) {
						// 控制结构已经在 compileControlStructures 中处理
						return $matches[0];
					}

                    return match ($funcName) {
                        'include', 'include_file' => $this->compileIncludeTag($args),
                        'block' => $this->compileBlockTag($args),
                        'extends' => '',
                        default => "<?php echo \$this->callPlugin('{$funcName}', [{$args}]); ?>",
                    };
				},
				$content
		);

		return $content;
	}

	/**
	 * 编译 include 标签
	 *
	 * @param string $args 标签参数
	 * @return string
	 */
	protected function compileIncludeTag(string $args): string
    {
		preg_match('/file\s*=\s*["\']([^"\']+)["\']/', $args, $matches);
		$file = $matches[1] ?? '';

		if (empty($file)) {
			return '<!-- Include file not specified -->';
		}

		return "<?php echo \$this->fetch('{$file}', \$this->tpl_vars); ?>";
	}

	/**
	 * 编译 block 标签
	 *
	 * @param string $args 标签参数
	 * @return string
	 */
	protected function compileBlockTag(string $args): string
	{
		preg_match('/name\s*=\s*["\']([a-zA-Z_][a-zA-Z0-9_]*)["\']/', $args, $matches);
		$blockName = $matches[1] ?? 'unnamed';

		return "<?php \$this->startBlock('{$blockName}'); ?>";
	}

	/**
	 * 编译模板继承
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileExtends(string $content): string
	{
		$ld = preg_quote($this->left_delimiter, '#');
		$rd = preg_quote($this->right_delimiter, '#');

		if (preg_match("#{$ld}\\s*extends\\s+file\\s*=\\s*[\"']([^\"']+)[\"']\\s*{$rd}#", $content, $matches)) {
			$parentTemplate = $matches[1];
			$content = preg_replace("#{$ld}\\s*extends\\s+file\\s*=\\s*[\"'][^\"']+[\"']\\s*{$rd}#", '', $content);

			// 先编译子模板的 blocks，收集到 $this->block_content
			$this->compileBlocks($content);
			// 保存子模板的 block 内容
			$childBlocks = $this->block_content;

			$parentFile = $this->getTemplateFile($parentTemplate);
			if (file_exists($parentFile)) {
				$parentContent = file_get_contents($parentFile);
				$parentContent = $this->compileExtends($parentContent);
				// 编译父模板的 blocks（替换父模板中的 block 标签为 getBlockContent 调用）
				// 注意：这里需要临时清空 block_content，以避免父模板的 block 覆盖子模板的 block
				$tempBlocks = $this->block_content;
				$this->block_content = [];
				$parentContent = $this->compileBlocks($parentContent);
				// 恢复子模板的 block 内容
				$this->block_content = $childBlocks;
				// 合并 blocks（用子模板的内容替换父模板的 getBlockContent 调用）
				$content = $this->mergeBlocks($parentContent, $content);
			}
		}

		return $content;
	}

	/**
	 * 编译 blocks
	 *
	 * @param string $content 模板内容
	 * @return string
	 */
	protected function compileBlocks(string $content): string
	{
		$ld = preg_quote($this->left_delimiter, '#');
		$rd = preg_quote($this->right_delimiter, '#');

		$content = preg_replace_callback(
				"#{$ld}\\s*block\\s+name\\s*=\\s*[\"']([a-zA-Z_][a-zA-Z0-9_]*)[\"']\\s*{$rd}(.*?){$ld}\\s*/block\\s*{$rd}#s",
				function($matches) {
                    $blockName = $matches[1];
					$blockContent = $matches[2];
					$this->block_content[$blockName] = $blockContent;
					return "<?php echo \$this->getBlockContent('{$blockName}'); ?>";
				},
				$content
		);

		return $content;
	}

	/**
	 * 开始块（用于 {block} 标签）
	 *
	 * @param string $name 块名称
	 * @return void
	 */
	public function startBlock(string $name): void
    {
		ob_start();
		$this->current_block = $name;
	}

	/**
	 * 结束块
	 *
	 * @return string
	 */
	public function endBlock(): string
	{
		if ($this->current_block !== null) {
			$content = ob_get_clean();
			$this->block_content[$this->current_block] = $content;
			$this->current_block = null;
		}
		return '';
	}

	/**
	 * 合并块内容
	 *
	 * @param string $parentContent 父模板内容
	 * @param string $childContent 子模板内容
	 * @return string
	 */
	protected function mergeBlocks(string $parentContent, string $childContent): string
	{
		foreach ($this->block_content as $name => $content) {
			$pattern = "/<\?php echo \\\$this->getBlockContent\('{$name}'\); \?>/";
			$replacement = $content;
			$parentContent = preg_replace($pattern, $replacement, $parentContent);
		}
		return $parentContent;
	}

	/**
	 * 获取块内容
	 *
	 * @param string $name 块名称
	 * @return string
	 */
	public function getBlockContent(string $name): string
	{
        return $this->block_content[$name] ?? '';
	}

	/**
	 * 调用插件函数
	 *
	 * @param string $name 插件名称
	 * @param array $args 参数数组
	 * @return string
	 */
	public function callPlugin(string $name, array $args): string
    {
		if (isset($this->plugins[$name]) && is_callable($this->plugins[$name])) {
			return call_user_func_array($this->plugins[$name], $args);
		}
		return "<!-- Plugin '{$name}' not found -->";
	}

	/**
	 * 注册插件函数
	 *
	 * @param string $name 插件名称
	 * @param callable $callback 回调函数
	 * @return self
	 */
	public function registerPlugin(string $name, callable $callback): self
    {
		$this->plugins[$name] = $callback;
		return $this;
	}

	/**
	 * 应用修饰符
	 *
	 * @param mixed $value 变量值
	 * @param string $modifier 修饰符
	 * @return mixed
	 */
	public function applyModifier(mixed $value, string $modifier): mixed
    {
		$parts = explode(':', $modifier, 2);
		$modName = trim($parts[0]);
		$modParams = isset($parts[1]) ? trim($parts[1]) : '';

		switch ($modName) {
			case 'upper':
			case 'uppercase':
				return strtoupper($value ?: '');
			case 'lower':
			case 'lowercase':
				return strtolower($value ?: '');
			case 'capitalize':
				return ucfirst($value ?: '');
			case 'truncate':
				$length = (int)($modParams ?: 30);
				return mb_substr($value ?: '', 0, $length, 'UTF-8') . '...';
			case 'strip':
			case 'strip_tags':
				return strip_tags($value ?: '');
			case 'nl2br':
				return nl2br($value ?: '');
			case 'date':
			case 'date_format':
				$format = $modParams ?: 'Y-m-d H:i:s';
				return date($format, is_numeric($value) ? $value : strtotime($value));
			case 'default':
				return $value ?? $modParams;
			case 'json_encode':
				return json_encode($value, JSON_UNESCAPED_UNICODE);
			case 'count':
			case 'sizeof':
				return is_countable($value) ? count($value) : 0;
			case 'implode':
				return is_array($value) ? implode($modParams ?: ', ', $value) : $value;
			default:
				if (function_exists($modName)) {
					return $modParams ? $modName($value, $modParams) : $modName($value);
				}
				return $value;
		}
	}

	/**
	 * 将点语法转换为箭头语法
	 *
	 * @param string $var 变量名
	 * @return string
	 */
	protected function convertDotToArrow(string $var): string
    {
		$parts = explode('.', $var);
		$result = array_shift($parts);

		foreach ($parts as $part) {
			if (is_numeric($part)) {
				$result .= "[{$part}]";
			} elseif (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $part)) {
				$result .= "['{$part}']";
			} else {
				$result .= "[{$part}]";
			}
		}

		return $result;
	}

	/**
	 * 转换带 $ 符号的变量点号语法为数组访问语法
	 *
	 * @param string $var 带美元符号的变量（如 $var 或 $var.prop）
	 * @return string 转换后的变量（如 $var 或 $var['prop']）
	 */
	protected function convertVariableWithDollar(string $var): string
    {
		$varName = substr($var, 1); // 去掉 $ 符号
		$converted = $this->convertDotToArrow($varName);
		return '$' . $converted;
	}

	/**
	 * 从匹配中提取修饰符列表
	 *
	 * @param string $fullMatch 完整的匹配字符串
	 * @param string $varName 变量名（需要从匹配中移除）
	 * @return array 修饰符数组
	 */
	protected function parseModifiers(string $fullMatch, string $varName): array
    {
		// 提取修饰符部分
		$pattern = "#^\\{\\s*\\$([a-zA-Z_][a-zA-Z0-9_]*(?:\\.[a-zA-Z_][a-zA-Z0-9_]*|\\.\\d+)*)\\s*#";
		$modifierPart = preg_replace($pattern, '', $fullMatch);
		$modifierPart = trim(str_replace(['{', '}'], '', $modifierPart));

		// 提取修饰符列表
		$modifiers = [];
		$parts = explode('|', $modifierPart);
		foreach ($parts as $part) {
			$part = trim($part);
			if (!empty($part) && $part !== 'nofilter') {
				$modifiers[] = $part;
			}
		}

		return $modifiers;
	}

	/**
	 * 构建修饰符链调用代码
	 *
	 * @param string $varAccess 变量访问代码
	 * @param array $modifiers 修饰符数组
	 * @return string 修饰符链调用代码
	 */
	protected function buildModifierChain(string $varAccess, array $modifiers): string
    {
		$phpVar = $varAccess;
		foreach ($modifiers as $modifier) {
			$phpVar = "\$this->applyModifier({$phpVar}, '{$modifier}')";
		}
		return $phpVar;
	}

	/**
	 * 处理表达式中的变量，将点号语法转换为数组访问语法
	 *
	 * @param string $expression 条件表达式
	 * @return string
	 */
	protected function processExpressionVariables(string $expression): string
    {
		// 匹配表达式中的变量 $var 或 $var.prop 或 $var.0.prop
		return preg_replace_callback(
			'/\$[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*|\.\\d+)*/',
			function($matches) {
				return $this->convertVariableWithDollar($matches[0]);
			},
			$expression
		);
	}

	// ============================================
	// 配置方法
	// ============================================

	/**
	 * 设置定界符
	 *
	 * @param string $left 左定界符
	 * @param string $right 右定界符
	 * @return self
	 */
	public function setDelimiters(string $left, string $right): static
    {
		$this->left_delimiter = $left;
		$this->right_delimiter = $right;
		return $this;
	}

	/**
	 * 获取左定界符
	 *
	 * @return string
	 */
	public function getLeftDelimiter(): string
    {
		return $this->left_delimiter;
	}

	/**
	 * 获取右定界符
	 *
	 * @return string
	 */
	public function getRightDelimiter(): string
    {
		return $this->right_delimiter;
	}

	/**
	 * 设置模板目录
	 *
	 * @param string $dir 目录路径
	 * @return self
	 */
	public function setTemplateDir($dir): static
    {
		$this->template_dir = rtrim($dir, '/') . '/';
		$this->ensureDirectoryExists($this->template_dir);
		return $this;
	}

	/**
	 * 设置编译目录
	 *
	 * @param string $dir 目录路径
	 * @return self
	 */
	public function setCompileDir(string $dir): static
    {
		$this->compile_dir = rtrim($dir, '/') . '/';
		$this->ensureDirectoryExists($this->compile_dir);
		return $this;
	}

	/**
	 * 启用编译检查
	 *
	 * @return self
	 */
	public function enableCompileCheck(): static
    {
		$this->compile_check = true;
		return $this;
	}

	/**
	 * 禁用编译检查
	 *
	 * @return self
	 */
	public function disableCompileCheck(): static
    {
		$this->compile_check = false;
		return $this;
	}

	// ============================================
	// 清理方法
	// ============================================

	/**
	 * 清除编译缓存
	 *
	 * @param string|null $template 模板名称
	 * @return int
	 */
	public function clearCompiledCache($template = null): int
    {
		$count = 0;
		$dir = $this->compile_dir;

		if ($template !== null) {
			$file = $this->getCompileFile($template);
			if (file_exists($file)) {
				unlink($file);
				$count++;
			}
		} else {
			$files = glob($dir . '*.php');
			if ($files) {
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
						$count++;
					}
				}
			}
		}

		return $count;
	}

	/**
	 * 清除所有编译缓存
	 *
	 * @return int
	 */
	public function clearAllCache(): int
    {
		return $this->clearCompiledCache();
	}

	// ============================================
	// 魔术方法
	// ============================================

	/**
	 * 克隆时重置状态
	 *
	 * @return void
	 */
	public function __clone() {
		$this->php_blocks = [];
        $this->literal_blocks = [];
		$this->block_content = [];
	}

	/**
	 * 字符串转换
	 *
	 * @return string
	 */
	public function __toString() {
		return 'PHPEMS Template Engine v2.0.0-lite';
	}
}