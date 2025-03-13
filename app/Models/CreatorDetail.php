<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CreatorDetail extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [ 'user_id','zoom_access_token','zoom_refresh_token','zoom_token_expires_at'];

    protected $casts = [
        'zoom_token_expires_at' => 'datetime',
    ];

    public function refreshZoomToken()
    {
        if ($this->zoom_token_expires_at && $this->zoom_token_expires_at->isFuture()) {
            return;
        }

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->zoom_refresh_token,
            'client_id' => env('ZOOM_CLIENT_KEY'),
            'client_secret' => env('ZOOM_CLIENT_SECRET'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            $this->zoom_access_token = $data['access_token'];
            $this->zoom_refresh_token = $data['refresh_token'];
            $this->zoom_token_expires_at = Carbon::now()->addSeconds($data['expires_in']);
            $this->save();
        }
    }

}
