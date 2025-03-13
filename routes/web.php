<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'loginPage']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/dashboard', [AuthController::class, 'dashboard']);

    Route::get('/zoom/auth', function () {
        $zoomAuthUrl = "https://zoom.us/oauth/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id' => env('ZOOM_CLIENT_KEY'),
            'redirect_uri' => env('ZOOM_REDIRECT_URI'),
        ]);

        return redirect($zoomAuthUrl);
    })->name('zoom.auth');

    Route::get('/zoom/callback', function () {
        $code = request('code');

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('ZOOM_REDIRECT_URI'),
            'client_id' => env('ZOOM_CLIENT_KEY'),
            'client_secret' => env('ZOOM_CLIENT_SECRET'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            $creator = Auth::user();
            $creator->zoom_access_token = $data['access_token'];
            $creator->zoom_refresh_token = $data['refresh_token'];
            $creator->zoom_token_expires_at = now()->addSeconds($data['expires_in']);
            $creator->save();

            return redirect()->route('dashboard')->with('success', 'Zoom account connected successfully!');
        }

        return redirect()->route('dashboard')->with('error', 'Failed to connect Zoom.');
    });
});