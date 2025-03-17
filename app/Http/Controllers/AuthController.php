<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\{Booking,Event,Slot,User};
use Carbon\Carbon;


class AuthController extends Controller
{
    public function loginPage()  {
        return view('auth.login');
    }
    public function login(Request $request) : RedirectResponse  {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
     
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
     
                return redirect()->intended('dashboard');
            }
     
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout(Request $request) {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/login');
    }
    public function dashboard() {
        return view('dashboard');
    }


    public function createMeeting(Request $request)
    {
        $zoomAccessToken = auth()->user()->creator->zoom_access_token; // Assuming each creator has their own Zoom token
        $zoomService = new ZoomService($zoomAccessToken);

        $meeting = $zoomService->createMeeting(auth()->user(), "My Meeting");

        return response()->json($meeting);
    }

    public function bookEvent(Request $request) {
        try {
            $creator = User::find(2);
            $creatorInfo = $creator->CreatorDetail;
            $creatorInfo->refreshZoomToken();
            $zoomAccessToken = $creatorInfo->zoom_access_token; // Get creator's access token
            $event = Event::find(1);
            $slot = $event->slots[0];
            if(!$slot->zoom_meeting_id){
                // $meetingStartTime = Carbon::parse($event->date.' '.$slot->start_time);
                    // $meetingStartTime = Carbon::now();
                    $meetingData = [
                        'topic' => 'Group Meeting with ' . $creator->name,
                        'type' => 2, // Scheduled meeting
                        'start_time' => $meetingStartTime->toIso8601String(), 
                        'duration' => 30, // Duration in minutes
                        'timezone' => 'Asia/Kolkata',
                        'password' => '447744', // Optional
                        'settings' => [
                            'host_video' => true,
                            'participant_video' => true,
                            'mute_upon_entry' => true,
                            'waiting_room' => false,
                            // 'meeting_authentication' => true, // Require authentication
                            'approval_type' => 0, // No approval required
                            // 'encryption_type' => 'enhanced_encryption',
                            'authentication_option' => 'enforce_login', // Require Zoom login
                            // 'join_before_host' => false, // Prevent early joining
                        ]
                    ];
                    $zoomResponse = Http::withToken($zoomAccessToken)->post('https://api.zoom.us/v2/users/me/meetings', $meetingData);
                    $zoomMeeting = $zoomResponse->json();
                    // dd($zoomMeeting['join_url']);
                    echo "<pre>";
                    print_r($zoomMeeting);
                    dd('$zoomMeeting');
                // if (isset($zoomMeeting['join_url'])) {
                //     // Store the meeting link in the database
                //     Slot::where('event_id', $event->id)->where('id', $slot->id)->update([
                //         'zoom_meeting_id' => $zoomMeeting['id'],
                //         'zoom_meeting_url' => $zoomMeeting['join_url']
                //     ]);
                // }
            }

            // //Start::Commented Code
            // // To create a unique url for each user
            // // but it creator should have atleast pro version (Paid Version) 
            // $eventSlot = Slot::where('event_id', $event->id)->where('id', $slot->id)->first();
            // $user = auth()->user();
            // $participantData = [
            //     'email' => $user->email,
            //     'first_name' => $user->name,
            //     'role' => 0, // Attendee
            // ];
            
            // $response = Http::withToken($zoomAccessToken)->post("https://api.zoom.us/v2/meetings/{$eventSlot->zoom_meeting_id}/registrants", $participantData);
            // $joinUrl = $response->json()['join_url']; // This join link is unique per user
            // $resJson = $response->json(); // This join link is unique per user
            // dd($resJson);
            // //End::Commented Code
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateSlot(Request $request)
    {
        try {
            $creator = User::find(2);
            $creatorInfo = $creator->CreatorDetail;
            $creatorInfo->refreshZoomToken();
            $zoomAccessToken = $creatorInfo->zoom_access_token; // Get creator's access token
            $slot = Slot::where('event_id', 1)->where('id', 1)->first();
            if(!$slot){
                dd("Slot not found.");
            }
            if($slot->zoom_meeting_id){
                // $meetingStartTime = Carbon::parse($event->date.' '.$slot->start_time);
                $meetingStartTime = Carbon::now();
                $meetingData = [
                    'topic' => 'Updated Group Meeting with ' . $creator->name,
                    'type' => 2, // Scheduled meeting
                    'start_time' => $meetingStartTime->toIso8601String(), 
                    'duration' => 30, // Duration in minutes
                    'timezone' => 'Asia/Kolkata',
                    'password' => '111111', // Optional
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'mute_upon_entry' => true,
                        'waiting_room' => false,
                        // 'meeting_authentication' => true, // Require authentication
                        'approval_type' => 0, // No approval required
                        // 'encryption_type' => 'enhanced_encryption',
                        'authentication_option' => 'enforce_login', // Require Zoom login
                        // 'join_before_host' => false, // Prevent early joining
                    ]
                ];

                $response = Http::withToken($zoomAccessToken)
                    ->patch("https://api.zoom.us/v2/meetings/{$slot->zoom_meeting_id}", $meetingData);

                if ($response->successful()) {
                    echo "Meeting updated successfully.";
                } else {
                    echo "Error: " . $response->body();
                }
            }
            dd("Slot updated successfully.");
            

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteSlot(Request $request)
    {
        try {
            $creator = User::find(2);
            $creatorInfo = $creator->CreatorDetail;
            $creatorInfo->refreshZoomToken();
            $zoomAccessToken = $creatorInfo->zoom_access_token; // Get creator's access token
            $slot = Slot::where('event_id', 1)->where('id', 1)->first();
            if(!$slot){
                dd("Slot not found.");
            }

            $booking = Booking::where('event_id', 1)->where('slot_id', 1)->get();
            if(count($booking)){
                dd('This slot is already booked.');
            }

            dd("Slot deleted successfully.");
            if(!empty($slot->zoom_meeting_id)){
                $meetingId = $slot->zoom_meeting_id;
                $response = Http::withToken($zoomAccessToken)
                    ->delete("https://api.zoom.us/v2/meetings/{$meetingId}");
                if ($response->successful()) {
                    echo "Meeting deleted successfully.";
                } else {
                    echo "Error: " . $response->body();
                }
            }

            dd("Slot deleted successfully.");
            

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
