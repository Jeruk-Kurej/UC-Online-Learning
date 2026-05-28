<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiModerationService
{
    /*
    |--------------------------------------------------------------------------
    | Main Analysis Method
    |--------------------------------------------------------------------------
    */

    public function analyze(string $content, int $rating = 0, string $customerName = ''): array
    {
        if (config('gemini.skip_moderation', false)) {
            return $this->fallbackAnalysis($content, $rating);
        }

        try {
            $prompt = $this->buildPrompt($content, $rating, $customerName);
            $model = config('gemini.model', 'gemini-2.5-flash');

            $response = Gemini::generativeModel(model: $model)->generateContent($prompt);
            $rawText = $response->text();

            if (config('gemini.debug', false) || config('app.debug')) {
                Log::debug('Gemini raw response', [
                    'model' => $model,
                    'rating' => $rating,
                    'response' => $rawText,
                ]);
            }

            $result = $this->parseGeminiResponse($rawText, $rating);

            Log::info('Gemini AI Analysis', [
                'model' => $model,
                'content' => substr($content, 0, 100),
                'rating' => $rating,
                'result' => $result,
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Gemini AI Error', [
                'error' => $e->getMessage(),
                'content' => substr($content, 0, 100)
            ]);
            
            return $this->fallbackAnalysis($content, $rating);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prompt Building
    |--------------------------------------------------------------------------
    */

    private function buildPrompt(string $content, int $rating, string $customerName): string
    {
        return <<<PROMPT
You are an AI content moderator for Universitas Ciputra's student & alumni business platform.

Analyze this testimony and respond with ONLY a JSON object (no markdown, no explanation):

{
  "sentiment_score": 0-100,
  "sentiment": "Positive" | "Neutral" | "Negative",
  "is_approved": true/false,
  "rejection_reason": "string or null"
}

**APPROVAL RULES:**
1. ✅ APPROVE (sentiment ≥ 60%) if: Positive/neutral sentiment, constructive feedback, genuine experience
2. ❌ REJECT if: Hate speech, profanity, spam, fake reviews, irrelevant content, personal attacks, suspicious names
3. Consider both content quality and rating coherence (5 stars should have positive content, 1 star can be negative if constructive)

**Testimony Details:**
- Customer: {$customerName}
- Rating: {$rating}/5 stars
- Content: "{$content}"

**Context:**
- Language: Indonesian & English both accepted
- Platform: UCO student/alumni business testimonies
- Threshold: Approve if sentiment_score >= 60

Respond with valid JSON only:
PROMPT;
    }

    /*
    |--------------------------------------------------------------------------
    | Response Parsing
    |--------------------------------------------------------------------------
    */

    private function parseGeminiResponse(string $text, int $rating): array
    {
        $text = str_replace(['```json', '```'], '', $text);
        $text = trim($text);
        
        $json = json_decode($text, true);
        
        if (!is_array($json)) {
            Log::warning('Failed to parse Gemini JSON', ['response' => $text]);
            return $this->fallbackAnalysis('', $rating);
        }
        
        $sentimentScore = isset($json['sentiment_score']) 
            ? (float) max(0, min(100, $json['sentiment_score'])) 
            : 0.0;
            
        $sentimentLabel = $json['sentiment'] ?? ($sentimentScore >= 60 ? 'Positive' : 'Negative');
            
        $isApproved = isset($json['is_approved']) 
            ? (bool) $json['is_approved'] 
            : false;
            
        $rejectionReason = $isApproved 
            ? null 
            : ($json['rejection_reason'] ?? 'Content requires review by administrators');
        
        return [
            'sentiment_score' => $sentimentScore,
            'sentiment' => $sentimentLabel,
            'is_approved' => $isApproved,
            'rejection_reason' => $rejectionReason,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Fallback Analysis
    |--------------------------------------------------------------------------
    */

    private function fallbackAnalysis(string $content, int $rating): array
    {
        $negativeKeywords = ['buruk', 'jelek', 'mengecewakan', 'bad', 'terrible', 'worst', 'horrible', 'scam', 'palsu'];
        $positiveKeywords = ['bagus', 'baik', 'memuaskan', 'recommended', 'good', 'great', 'excellent', 'amazing'];
        
        $lowerContent = strtolower($content);
        $negativeCount = 0;
        $positiveCount = 0;
        
        foreach ($negativeKeywords as $word) {
            if (stripos($lowerContent, $word) !== false) $negativeCount++;
        }
        
        foreach ($positiveKeywords as $word) {
            if (stripos($lowerContent, $word) !== false) $positiveCount++;
        }
        
        $baseScore = $rating * 20;
        $keywordAdjustment = ($positiveCount - $negativeCount) * 10;
        $sentimentScore = max(0, min(100, $baseScore + $keywordAdjustment));
        
        $isApproved = $sentimentScore >= 60;
        $sentimentLabel = $sentimentScore >= 60 ? 'Positive' : 'Negative';
        
        return [
            'sentiment_score' => round($sentimentScore, 2),
            'sentiment' => $sentimentLabel,
            'is_approved' => $isApproved,
            'rejection_reason' => $isApproved ? null : 'AI service unavailable - requires manual review',
        ];
    }
}

