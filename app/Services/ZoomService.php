namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ZoomService
{
    protected $zoomAccessToken;

    public function __construct($zoomAccessToken)
    {
        $this->zoomAccessToken = $zoomAccessToken;
    }

    /**
     * Create a Zoom Meeting
     */
    public function createMeeting($creator, $topic, $duration = 30)
    {
        $meetingStartTime = Carbon::now()->toIso8601String();

        $meetingData = [
            'topic' => $topic,
            'type' => 2, // Scheduled meeting
            'start_time' => $meetingStartTime,
            'duration' => $duration,
            'timezone' => 'Asia/Kolkata',
            'password' => '123456',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'mute_upon_entry' => true,
                'waiting_room' => true, // Secure meeting
            ]
        ];

        $response = Http::withToken($this->zoomAccessToken)
            ->post('https://api.zoom.us/v2/users/me/meetings', $meetingData);

        return $response->json();
    }

    /**
     * Update a Zoom Meeting
     */
    public function updateMeeting($meetingId, $data)
    {
        $response = Http::withToken($this->zoomAccessToken)
            ->patch("https://api.zoom.us/v2/meetings/{$meetingId}", $data);

        return $response->json();
    }

    /**
     * Delete a Zoom Meeting
     */
    public function deleteMeeting($meetingId)
    {
        $response = Http::withToken($this->zoomAccessToken)
            ->delete("https://api.zoom.us/v2/meetings/{$meetingId}");

        return $response->successful();
    }
}
