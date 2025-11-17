<?php

namespace App\Http\Controllers;

use App\Models\ApiIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $integrations = ApiIntegration::where('user_id', Auth::id())->get();
        return view('api-integrations.index', compact('integrations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $providers = [
            'credit_score' => 'Credit Score',
            'investment_data' => 'Investment Data',
            'news' => 'Financial News',
            'weather' => 'Weather Data',
        ];

        return view('api-integrations.create', compact('providers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:credit_score,investment_data,news,weather',
            'api_key' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        ApiIntegration::create([
            'user_id' => Auth::id(),
            'provider' => $request->provider,
            'api_key' => $request->api_key,
            'settings' => $request->settings,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('api-integrations.index')->with('success', 'API integration created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);
        return view('api-integrations.show', compact('apiIntegration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);

        $providers = [
            'credit_score' => 'Credit Score',
            'investment_data' => 'Investment Data',
            'news' => 'Financial News',
            'weather' => 'Weather Data',
        ];

        return view('api-integrations.edit', compact('apiIntegration', 'providers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);

        $request->validate([
            'provider' => 'required|in:credit_score,investment_data,news,weather',
            'api_key' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $apiIntegration->update($request->only([
            'provider', 'api_key', 'settings', 'is_active'
        ]));

        return redirect()->route('api-integrations.index')->with('success', 'API integration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);
        $apiIntegration->delete();

        return redirect()->route('api-integrations.index')->with('success', 'API integration deleted successfully.');
    }

    /**
     * Sync data from API provider
     */
    public function sync(ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);

        if (!$apiIntegration->checkRateLimit()) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.'
            ], 429);
        }

        $result = $apiIntegration->syncData();

        if ($result['success']) {
            $apiIntegration->consumeRateLimit();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }
    }

    /**
     * Get integration status
     */
    public function status(ApiIntegration $apiIntegration)
    {
        $this->ensureOwner($apiIntegration);

        return response()->json($apiIntegration->getStatus());
    }

    /**
     * Get data from specific provider
     */
    public function getData(Request $request, $provider)
    {
        $integration = ApiIntegration::where('user_id', Auth::id())
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (!$integration) {
            return response()->json([
                'success' => false,
                'message' => 'Integration not found or not active'
            ], 404);
        }

        $result = $integration->syncData();

        return response()->json([
            'success' => $result['success'],
            'data' => $result['data'] ?? null,
            'message' => $result['message'] ?? null
        ]);
    }

    private function ensureOwner(ApiIntegration $apiIntegration): void
    {
        if ($apiIntegration->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
