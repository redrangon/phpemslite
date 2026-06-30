<?php

namespace PHPEMS\Lib\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use PHPEMS\Lib\Config\Site\AI;
use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Rules\Message;
use PHPEMS\Lib\Utils\FileLogger;
class AIClient
{
    private $httpClient;
    private $apiKey;
    private $baseUrl;
    private $defaultOptions;
    private $logger;
    private $context = [];

    public function __construct(
        array $config = [],
        $logger = null
    ) {
        if(empty($config))
        {
            $config = DI(AI::class)->getRaw();
        }
        if($logger === null)
        {
            $logger = new FileLogger(DI(Site::class)->AILogPath);
        }
        $this->apiKey = $config['apiKey'];
        $this->baseUrl = rtrim($config['baseUrl'], '/').'/';
        $this->defaultOptions = $config['options'];
        $this->logger = $logger;

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * 添加消息到上下文
     */
    public function addContext(Message $message): self
    {
        $this->context[] = $message;
        return $this;
    }

    /**
     * 清空上下文
     */
    public function clearContext(): self
    {
        $this->context = [];
        return $this;
    }

    /**
     * 非流式聊天完成（返回完整响应）
     */
    public function chat(array $messages, array $options = []): array
    {
        $payload = $this->buildPayload($messages, $options, false);
        return $this->sendRequest('POST', 'chat/completions', $payload);
    }

    /**
     * 流式聊天完成（SSE，逐块返回）
     * @param callable $onChunk 回调函数：function(string $content, bool $isFinal)
     */
    public function chatStream(array $messages, callable $onChunk, array $options = []): void
    {
        $payload = $this->buildPayload($messages, $options, true);
        $this->sendStreamRequest('chat/completions', $payload, $onChunk);
    }

    /**
     * 快速问答（自动管理上下文）
     */
    public function ask(string $question, array $options = []): string
    {
        $this->addContext(Message::user($question));
        $response = $this->chat($this->context, $options);
        $answer = $response['choices'][0]['message']['content'] ?? '';
        $this->addContext(Message::assistant($answer));
        return $answer;
    }

    /**
     * 生成 JSON 结构化输出
     */
    public function generateJson(string $prompt, array $options = []): ?array
    {
        $options = array_merge($options, [
            'response_format' => ['type' => 'json_object']
        ]);
        $response = $this->chat([Message::user($prompt)], $options);
        $content = $response['choices'][0]['message']['content'] ?? '';
        $json = json_decode($content, true);
        return is_array($json) ? $json : null;
    }

    // ================== 内部方法 ==================

    private function buildPayload(array $messages, array $options, bool $stream): array
    {
        // 合并默认选项
        $opts = array_merge($this->defaultOptions, $options);

        // 构建消息列表（含上下文）
        $allMessages = array_map(function($m){
            return $m->toArray();
        }, $this->context);
        foreach ($messages as $msg) {
            if ($msg instanceof Message) {
                $allMessages[] = $msg->toArray();
            } else {
                $allMessages[] = $msg; // 兼容数组格式
            }
        }

        $payload = [
            'messages' => $allMessages,
            'stream' => $stream,
        ];

        // 支持 model、temperature、max_tokens 等
        $supportKeys = ['model', 'temperature', 'max_tokens', 'top_p', 'frequency_penalty', 'presence_penalty', 'response_format'];
        foreach ($supportKeys as $key) {
            if (isset($opts[$key])) {
                $payload[$key] = $opts[$key];
            }
        }

        return $payload;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    private function sendRequest(string $method, string $uri, array $payload): array
    {
        $retries = 3;
        $delay = 1;

        for ($i = 0; $i <= $retries; $i++) {
            try {
                $response = $this->httpClient->request($method, $uri, [
                    'json' => $payload,
                    'timeout' => 180,
                ]);

                $body = (string) $response->getBody();
                $data = json_decode($body, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON response');
                }

                return $data;
            } catch (RequestException $e) {
                $this->logError("AI request failed (attempt " . ($i + 1) . "): " . $e->getMessage());

                if ($i === $retries) {
                    throw new \Exception('AI request failed after retries: ' . $e->getMessage(), 0, $e);
                }

                // 指数退避
                sleep($delay);
                $delay *= 2;
            }
        }

        throw new \Exception('Unexpected error in AI request');
    }

    private function sendStreamRequest(string $uri, array $payload, callable $onChunk): void
    {
        try {
            $response = $this->httpClient->request('POST', $uri, [
                'json' => $payload,
                'stream' => true,
                'timeout' => 600, // 流式请求可能很长
            ]);

            $stream = $response->getBody();
            while (!$stream->eof()) {
                $line = $stream->readLine();
                if (str_starts_with($line, 'data: ') && !str_contains($line, '[DONE]')) {
                    $jsonStr = substr($line, 6);
                    $data = json_decode($jsonStr, true);
                    if ($data && isset($data['choices'][0]['delta']['content'])) {
                        $content = $data['choices'][0]['delta']['content'];
                        $onChunk($content, false);
                    }
                }
            }
            $onChunk('', true); // 标记结束
        } catch (\Exception $e) {
            $this->logError("AI stream request failed: " . $e->getMessage());
            throw new \Exception('Stream request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function logError(string $message): void
    {
        if ($this->logger) {
            $this->logger->error($message);
        }
    }
}