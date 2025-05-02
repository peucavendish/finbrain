<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AIAnalysisService;
use Illuminate\Http\Request;
use App\Models\AIAnalysis;

class AIAnalysisController extends Controller
{
    protected $aiService;

    public function __construct(AIAnalysisService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'age' => 'required|integer|min:18|max:100',
                'health_conditions' => 'required|array',
                'lifestyle_factors' => 'required|array',
                'family_history' => 'required|array',
                'occupation' => 'required|string',
                'income' => 'required|numeric|min:0'
            ]);

            $analysis = $this->aiService->performAnalysis($validatedData);
            
            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Análise de seguro de vida realizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar análise: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnalysisHistory()
    {
        $analyses = AIAnalysis::with('recommendations')->latest()->get();
        return response()->json($analyses);
    }
} 