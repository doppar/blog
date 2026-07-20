<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class AIAssistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'target' => 'required|in:title,excerpt,body,seo_title,seo_description,tags',
            'provider' => 'required|in:openai,gemini,claude,openrouter,selfhost',
            'model' => 'required|string|max:120',
            'temperature' => 'numeric',
            'max_tokens' => 'numeric',
            'instructions' => 'string|max:2000',
            'context' => 'array',
        ];
    }

    public function passed(): array
    {
        $values = parent::passed();

        $values['temperature'] = isset($values['temperature']) ? (float) $values['temperature'] : 0.7;
        $values['max_tokens'] = isset($values['max_tokens']) ? (int) $values['max_tokens'] : 1200;
        $values['context'] = is_array($values['context'] ?? null) ? $values['context'] : [];

        return $values;
    }
}
