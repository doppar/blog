<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AIAssistRequest;
use Doppar\AI\Agent;
use Doppar\AI\AgentFactory\Agent\Claude;
use Doppar\AI\AgentFactory\Agent\Gemini;
use Doppar\AI\AgentFactory\Agent\OpenAI;
use Doppar\AI\AgentFactory\Agent\OpenRouter;
use Doppar\AI\AgentFactory\Agent\SelfHost;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin/ai')]
#[Middleware(['auth', 'admin'])]
class AIAssistantController extends Controller
{
    private const PROVIDER_CLASSES = [
        'openai' => OpenAI::class,
        'gemini' => Gemini::class,
        'claude' => Claude::class,
        'openrouter' => OpenRouter::class,
        'selfhost' => SelfHost::class,
    ];

    #[Route(uri: '/generate', methods: ['POST'], name: 'admin.ai.generate')]
    public function generate(AIAssistRequest $request): Response
    {
        $data = $request->passed();

        $provider = $data['provider'];
        $model = (string) $data['model'];
        $target = $data['target'];
        $temperature = $data['temperature'];
        $maxTokens = $data['max_tokens'];
        $instructions = (string) ($data['instructions'] ?? '');
        $context = (array) ($data['context'] ?? []);

        $apiKey = $this->resolveApiKey($provider);

        if ($apiKey === null) {
            return response()->json([
                'message' => 'The selected AI provider is not configured. Add the API key to your .env file.',
            ], 422);
        }

        try {
            $agent = Agent::using($this->resolveProviderClass($provider))
                ->withKey($apiKey)
                ->model($model)
                ->temperature($temperature)
                ->maxTokens($maxTokens)
                ->system($this->systemPrompt($target))
                ->prompt($this->userPrompt($target, $instructions, $context));

            if ($provider === 'selfhost') {
                $host = env('AI_SELF_HOST_URL', '');

                if ($host === '') {
                    return response()->json([
                        'message' => 'Self-hosted model URL is not configured. Set AI_SELF_HOST_URL in your .env file.',
                    ], 422);
                }

                $agent = Agent::using(SelfHost::class)
                    ->withHost($host)
                    ->withKey($apiKey)
                    ->model($model)
                    ->temperature($temperature)
                    ->maxTokens($maxTokens)
                    ->system($this->systemPrompt($target))
                    ->prompt($this->userPrompt($target, $instructions, $context));
            }

            $raw = (string) $agent->send();
            $cleaned = $this->cleanResult($raw, $target);
            $debug = [
                'provider' => $provider,
                'model' => $model,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
                'target' => $target,
                'raw_length' => strlen($raw),
                'cleaned_length' => strlen($cleaned),
                'raw_preview' => mb_strimwidth($raw, 0, 500, '…'),
                'system_prompt_preview' => mb_strimwidth($this->systemPrompt($target), 0, 200, '…'),
                'user_prompt_preview' => mb_strimwidth($this->userPrompt($target, $instructions, $context), 0, 500, '…'),
            ];

            if ($cleaned === '') {
                return response()->json([
                    'message' => 'AI returned an empty response after cleaning.',
                    'content' => '',
                    'debug' => $debug,
                ], 422);
            }

            return response()->json([
                'content' => $cleaned,
                'debug' => $debug,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'AI generation failed. Please try again.',
                'error' => $exception->getMessage(),
                'debug' => [
                    'provider' => $provider,
                    'model' => $model,
                    'target' => $target,
                ],
            ], 500);
        }
    }

    protected function resolveProviderClass(string $provider): string
    {
        return self::PROVIDER_CLASSES[$provider] ?? OpenAI::class;
    }

    protected function resolveApiKey(string $provider): ?string
    {
        $value = match ($provider) {
            'openai' => env('OPENAI_API_KEY'),
            'gemini' => env('GEMINI_API_KEY'),
            'claude' => env('CLAUDE_API_KEY'),
            'openrouter' => env('OPENROUTER_API_KEY'),
            'selfhost' => env('AI_SELF_HOST_KEY', ''),
            default => null,
        };

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function systemPrompt(string $target): string
    {
        return match ($target) {
            'title' => 'You are an expert editorial assistant. Generate a concise, compelling, and SEO-friendly blog post title. Return only the title text with no quotes, commentary, or formatting.',
            'excerpt' => 'You are an expert editorial assistant. Write a short, engaging summary excerpt of one or two sentences for a blog post. Return only the excerpt text with no commentary.',
            'body' => 'You are an expert editorial assistant. Write a well-structured, engaging blog post body in semantic HTML. Use <h2> for headings, <p> for paragraphs, <ul>/<li> for lists, and <blockquote> for quotes. Do not wrap the output in a code block and do not include <html> or <body> tags. Return only the content HTML.',
            'seo_title' => 'You are an SEO specialist. Generate a concise, keyword-rich meta title suitable for search engines. Return only the title text, maximum 60 characters when possible, with no commentary.',
            'seo_description' => 'You are an SEO specialist. Generate a compelling meta description for a blog post. Return only the description text, ideally under 160 characters, with no commentary.',
            'tags' => 'You are a taxonomy specialist. Suggest relevant tags for a blog post. Return only a comma-separated list of lowercase tags with no commentary, numbers, or explanations.',
            default => 'You are a helpful writing assistant.',
        };
    }

    protected function userPrompt(string $target, string $instructions, array $context): string
    {
        $contextText = '';

        if ($context !== []) {
            foreach ($context as $key => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }

                $label = str_replace('_', ' ', (string) $key);
                $contextText .= "\n" . ucfirst($label) . ": " . (string) $value;
            }
        }

        $base = match ($target) {
            'title' => 'Create a catchy blog post title based on the following context.',
            'excerpt' => 'Write a short excerpt for the following blog post.',
            'body' => 'Write the main content of the blog post using the context below.',
            'seo_title' => 'Create an SEO-friendly meta title based on the context below.',
            'seo_description' => 'Create an SEO meta description based on the context below.',
            'tags' => 'Suggest relevant tags for the following blog post.',
            default => 'Assist with the following request.',
        };

        $prompt = $base;

        if ($instructions !== '') {
            $prompt .= "\n\nAdditional instructions: {$instructions}";
        }

        if ($contextText !== '') {
            $prompt .= "\n\nContext:" . $contextText;
        }

        return $prompt;
    }

    protected function cleanResult(string $result, string $target): string
    {
        if (str_starts_with($result, '```') && str_ends_with($result, '```')) {
            $result = preg_replace('/^```[a-z]*\n?|\n?```$/i', '', $result) ?? $result;
        }

        if ($target === 'tags') {
            $tags = array_map(
                fn($tag) => trim($tag, '#"\' '),
                explode(',', $result)
            );

            $tags = array_filter($tags, fn($tag) => $tag !== '');
            $tags = array_unique(array_map('strtolower', $tags));

            return implode(', ', $tags);
        }

        return trim($result);
    }
}
