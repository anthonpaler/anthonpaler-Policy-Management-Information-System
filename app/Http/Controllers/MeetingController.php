<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function viewMeetings(Request $request){

        return view('content.meetings.viewMeetings');
    }
    public function viewCreateMeeting(Request $request)
    {
        return view ('content.meetings.createMeeting');
    }

}
