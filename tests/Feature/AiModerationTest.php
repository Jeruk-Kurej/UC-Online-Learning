<?php

use App\Services\AiModerationService;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

test('ai moderation service returns approved sentiment on positive content', function () {
    $fakeResponse = GenerateContentResponse::fake([
        'candidates' => [
            [
                'content' => [
                    'parts' => [
                        [
                            'text' => '{"sentiment_score": 90, "sentiment": "Positive", "is_approved": true, "rejection_reason": null}'
                        ]
                    ]
                ]
            ]
        ]
    ]);

    Gemini::fake([
        $fakeResponse
    ]);

    $service = new AiModerationService();
    $result = $service->analyze('Amazing product!', 5, 'Alice');

    expect($result['is_approved'])->toBeTrue();
    expect($result['sentiment_score'])->toBe(90.0);
    expect($result['sentiment'])->toBe('Positive');
});

test('ai moderation service falls back to local rules when gemini api fails', function () {
    Gemini::fake([
        new \Exception('Gemini service connection timed out')
    ]);

    $service = new AiModerationService();
    // Rating 5/5 should produce approved (sentiment score 100) on positive keywords
    $result = $service->analyze('Bagus sekali dan memuaskan.', 5, 'Bob');

    expect($result['is_approved'])->toBeTrue();
    expect($result['sentiment_score'])->toBeGreaterThanOrEqual(60);
    expect($result['rejection_reason'])->toBeNull();

    // Rating 1/5 with negative keywords should be rejected
    $resultNegative = $service->analyze('Buruk dan jelek sekali.', 1, 'Charlie');
    expect($resultNegative['is_approved'])->toBeFalse();
    expect($resultNegative['sentiment_score'])->toBeLessThan(60);
    expect($resultNegative['rejection_reason'])->toBe('AI service unavailable - requires manual review');
});
