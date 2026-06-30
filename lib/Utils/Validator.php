<?php

namespace PHPEMS\Lib\Utils;
use Exception;
class Validator
{
    protected $data = [];
    protected $rules = [];
    protected $messages = [];
    protected $errors = [];
    protected static $customRules = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $this->dotNotationToArray($data);
        $this->rules = $rules;
        $this->messages = $messages;
    }

    /**
     * 将点表示法（如 user.name）的数组展平为一维数组
     */
    protected function dotNotationToArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->dotNotationToArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    /**
     * 执行验证
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $rulesList = is_string($rules) ? explode('|', $rules) : $rules;

            foreach ($rulesList as $rule) {
                $rule = trim($rule);
                if (!$rule) continue;

                // 解析规则：如 "min:6" -> name="min", params=["6"]
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = str_getcsv($paramStr, ',', '"'); // 支持带引号的参数
                } else {
                    $ruleName = $rule;
                    $params = [];
                }

                $value = $this->getValue($field);

                // 检查是否需要验证（例如 required 未通过时跳过其他规则）
                if (!$this->shouldBeValidated($field, $ruleName, $value)) {
                    continue;
                }

                if (!$this->passes($ruleName, $value, $params, $field)) {
                    $message = $this->getMessage($field, $ruleName, $params);
                    $this->errors[$field][] = $this->formatMessage($message, $field, $value, $params);
                    break; // 同一字段一个规则失败即可
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * 获取字段值（支持点表示法）
     */
    protected function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * 判断是否应执行验证（例如 required 未满足时，其他规则跳过）
     */
    protected function shouldBeValidated(string $field, string $rule, mixed $value): bool
    {
        // 如果字段不存在且不是 required，跳过非-required 规则
        if (!array_key_exists($field, $this->data) && $rule !== 'required') {
            return false;
        }

        // 如果是 nullable 且值为 null，跳过后续验证
        if ($value === null && in_array('nullable', $this->getRulesForField($field))) {
            return false;
        }

        return true;
    }

    protected function getRulesForField(string $field): array
    {
        $rules = $this->rules[$field] ?? [];
        return is_string($rules) ? explode('|', $rules) : $rules;
    }

    /**
     * 执行单个规则验证
     */
    /**
     * 执行单个规则验证
     */
    protected function passes(string $rule, $value, array $params, string $field): bool
    {
        return match ($rule) {
            'required' => $this->validateRequired($value),
            'nullable' => true,
            'email' => $this->validateEmail($value),
            'numeric' => $this->validateNumeric($value),
            'integer' => $this->validateInteger($value),
            'string' => $this->validateString($value),
            'boolean' => $this->validateBoolean($value),
            'array' => $this->validateArray($value),
            'min' => $this->validateMin($value, $params[0] ?? 0),
            'max' => $this->validateMax($value, $params[0] ?? PHP_INT_MAX),
            'in' => $this->validateIn($value, $params),
            'not_in' => $this->validateNotIn($value, $params),
            'regex' => $this->validateRegex($value, $params[0] ?? ''),
            default => $this->runCustomRule($rule, $value, $params, $field),
        };
    }

    // ========== 内置验证规则 ==========
    protected function validateRequired($value): bool
    {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && empty($value)) return false;
        return true;
    }

    protected function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    protected function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateString($value): bool
    {
        return is_string($value);
    }

    protected function validateBoolean($value): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    protected function validateArray($value): bool
    {
        return is_array($value);
    }

    protected function validateMin($value, string $min): bool
    {
        if (is_string($value)) {
            return mb_strlen($value) >= (int)$min;
        }
        if (is_numeric($value)) {
            return $value >= (float)$min;
        }
        if (is_array($value)) {
            return count($value) >= (int)$min;
        }
        return false;
    }

    protected function validateMax($value, string $max): bool
    {
        if (is_string($value)) {
            return mb_strlen($value) <= (int)$max;
        }
        if (is_numeric($value)) {
            return $value <= (float)$max;
        }
        if (is_array($value)) {
            return count($value) <= (int)$max;
        }
        return false;
    }

    protected function validateIn($value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    protected function validateNotIn($value, array $disallowed): bool
    {
        return !in_array($value, $disallowed, true);
    }

    protected function validateRegex($value, string $pattern): bool
    {
        if (!is_string($value)) return false;
        return @preg_match($pattern, $value) === 1;
    }

    // ========== 自定义规则 ==========
    protected function runCustomRule(string $rule, $value, array $params, string $field): bool
    {
        if (isset(static::$customRules[$rule])) {
            $callback = static::$customRules[$rule];
            return $callback($value, $params, $field, $this->data);
        }
        throw new Exception("Undefined validation rule: {$rule}");
    }

    public static function extend(string $rule, callable $callback): void
    {
        static::$customRules[$rule] = $callback;
    }

    // ========== 错误处理 ==========
    public function fails(): bool
    {
        return !$this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(?string $field = null): ?string
    {
        if ($field && isset($this->errors[$field])) {
            return $this->errors[$field][0] ?? null;
        }
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }

    // ========== 消息处理 ==========
    protected function getMessage(string $field, string $rule, array $params): string
    {
        $key = "{$field}.{$rule}";
        if (isset($this->messages[$key])) {
            return $this->messages[$key];
        }

        $defaultMessages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field must be a valid email address.',
            'numeric' => 'The :field must be a number.',
            'integer' => 'The :field must be an integer.',
            'string' => 'The :field must be a string.',
            'boolean' => 'The :field must be true or false.',
            'array' => 'The :field must be an array.',
            'min' => 'The :field must be at least :min characters.',
            'max' => 'The :field may not be greater than :max characters.',
            'in' => 'The selected :field is invalid.',
            'not_in' => 'The selected :field is invalid.',
            'regex' => 'The :field format is invalid.',
        ];

        return $defaultMessages[$rule] ?? 'Validation failed for :field.';
    }

    protected function formatMessage(string $message, string $field, $value, array $params): string
    {
        $replacements = [
            ':field' => str_replace('.', ' ', $field),
            ':min' => $params[0] ?? '',
            ':max' => $params[0] ?? '',
        ];

        // 支持更多动态替换
        foreach ($params as $i => $param) {
            $replacements[":param{$i}"] = $param;
        }

        return strtr($message, $replacements);
    }
}