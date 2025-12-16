<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;

class OAuthAuthorizationController extends Controller
{
    /**
     * Handle OAuth authorization with auto-approval for trusted clients
     */
    public function authorize(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Redirect to login with return URL
            return redirect()->route('login')->with('url.intended', $request->fullUrl());
        }

        $clientRepository = new ClientRepository();
        
        // Validate client exists
        $client = $clientRepository->find($request->client_id);
        
        if (!$client) {
            return response()->json(['error' => 'Invalid client'], 400);
        }

        // Validate redirect URI
        if (!$this->validateRedirectUri($client, $request->redirect_uri)) {
            return response()->json(['error' => 'Invalid redirect URI'], 400);
        }

        // Auto-approve authorization for all clients
        // This creates SSO experience - user doesn't see approval screen
        return $this->approveRequest($request, $client);
    }

    /**
     * Validate redirect URI matches client's registered URIs
     */
    protected function validateRedirectUri($client, $redirectUri)
    {
        $registeredUris = is_array($client->redirect_uris) 
            ? $client->redirect_uris 
            : json_decode($client->redirect_uris, true);

        return in_array($redirectUri, $registeredUris);
    }

    /**
     * Approve the authorization request and redirect with code
     */
    protected function approveRequest(Request $request, $client)
    {
        // Generate authorization code
        $authCode = Str::random(40);
        
        // Store authorization code in session/cache with user info
        cache()->put(
            "oauth_auth_code:{$authCode}",
            [
                'user_id' => auth()->id(),
                'client_id' => $client->id,
                'redirect_uri' => $request->redirect_uri,
                'scope' => $request->scope ?? '',
                'code_challenge' => $request->code_challenge,
                'code_challenge_method' => $request->code_challenge_method ?? 'plain',
            ],
            now()->addMinutes(5) // Code expires in 5 minutes
        );

        // Build redirect URL with authorization code
        $redirectUri = $request->redirect_uri;
        $separator = str_contains($redirectUri, '?') ? '&' : '?';
        
        $redirectUrl = $redirectUri . $separator . http_build_query([
            'code' => $authCode,
            'state' => $request->state,
        ]);

        return redirect($redirectUrl);
    }

    /**
     * Exchange authorization code for access token
     */
    public function token(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|in:authorization_code,refresh_token',
            'client_id' => 'required',
            'client_secret' => 'required',
            'code' => 'required_if:grant_type,authorization_code',
            'redirect_uri' => 'required_if:grant_type,authorization_code',
            'code_verifier' => 'nullable', // For PKCE
            'refresh_token' => 'required_if:grant_type,refresh_token',
        ]);

        if ($request->grant_type === 'authorization_code') {
            return $this->issueTokenFromCode($request);
        }

        if ($request->grant_type === 'refresh_token') {
            return $this->issueTokenFromRefresh($request);
        }

        return response()->json(['error' => 'Unsupported grant type'], 400);
    }

    /**
     * Issue access token from authorization code
     */
    protected function issueTokenFromCode(Request $request)
    {
        // Get authorization code data
        $authData = cache()->get("oauth_auth_code:{$request->code}");
        
        if (!$authData) {
            return response()->json(['error' => 'Invalid or expired authorization code'], 400);
        }

        // Validate client credentials
        $clientRepository = new ClientRepository();
        $client = $clientRepository->find($request->client_id);
        
        if (!$client) {
            return response()->json(['error' => 'Invalid client credentials'], 401);
        }
        
        // Check secret - support both hashed and plain text
        $secretValid = $client->secret === $request->client_secret 
            || \Illuminate\Support\Facades\Hash::check($request->client_secret, $client->secret);
        
        if (!$secretValid) {
            return response()->json(['error' => 'Invalid client credentials'], 401);
        }

        // Validate PKCE if present
        if (isset($authData['code_challenge'])) {
            if (!$this->validatePKCE($request->code_verifier, $authData['code_challenge'], $authData['code_challenge_method'])) {
                return response()->json(['error' => 'Invalid code verifier'], 400);
            }
        }

        // Delete authorization code (one-time use)
        cache()->forget("oauth_auth_code:{$request->code}");

        // Generate access token
        $user = \App\Models\User::find($authData['user_id']);
        $token = $user->createToken('oauth-token', ['*'])->accessToken;

        // Generate refresh token and store mapping (rotate on use)
        $refreshToken = Str::random(64);
        $refreshData = [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'scopes' => $authData['scope'] ?? '',
        ];
        // Store refresh token for 30 days
        cache()->put("oauth_refresh_token:{$refreshToken}", $refreshData, now()->addDays(30));

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 31536000, // 1 year
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * Issue access token using a refresh token
     */
    protected function issueTokenFromRefresh(Request $request)
    {
        $refresh = cache()->get("oauth_refresh_token:{$request->refresh_token}");
        if (!$refresh) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 400);
        }

        // Validate client credentials
        $clientRepository = new ClientRepository();
        $client = $clientRepository->find($request->client_id);
        
        if (!$client) {
            return response()->json(['error' => 'Invalid client credentials'], 401);
        }
        
        // Check secret - support both hashed and plain text
        $secretValid = $client->secret === $request->client_secret 
            || \Illuminate\Support\Facades\Hash::check($request->client_secret, $client->secret);
        
        if (!$secretValid) {
            return response()->json(['error' => 'Invalid client credentials'], 401);
        }

        // Ensure refresh belongs to the same client
        if ($refresh['client_id'] != $client->id) {
            return response()->json(['error' => 'Refresh token does not belong to client'], 401);
        }

        // Rotate refresh token: delete old one and issue new
        cache()->forget("oauth_refresh_token:{$request->refresh_token}");

        $user = \App\Models\User::find($refresh['user_id']);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 400);
        }

        $newAccess = $user->createToken('oauth-token', ['*'])->accessToken;
        $newRefresh = Str::random(64);
        $refreshData = [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'scopes' => $refresh['scopes'] ?? '',
        ];
        cache()->put("oauth_refresh_token:{$newRefresh}", $refreshData, now()->addDays(30));

        return response()->json([
            'access_token' => $newAccess,
            'token_type' => 'Bearer',
            'expires_in' => 31536000,
            'refresh_token' => $newRefresh,
        ]);
    }

    /**
     * Validate PKCE code verifier
     */
    protected function validatePKCE($verifier, $challenge, $method)
    {
        if ($method === 'S256') {
            $hash = hash('sha256', $verifier, true);
            $computed = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
            return $computed === $challenge;
        }

        // Plain method
        return $verifier === $challenge;
    }

    /**
     * Get user info endpoint (for third-party app to fetch user data)
     */
    public function userInfo(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'created_at' => $user->created_at->toIso8601String(),
        ]);
    }
}
