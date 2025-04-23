<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class MeetingNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     public $meeting;

     public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    public function build()
    {
        $meeting = $this->meeting;

        


        $council_type = "";
        if ($meeting->getMeetingCouncilType() == 0){
            $council_type = config('meetings.council_types.local_level.'.$meeting->council_type) ;
        }
        elseif ($meeting->getMeetingCouncilType() == 1){
            $council_type = config('meetings.council_types.university_level.'.$meeting->council_type) ;
        }
        elseif ($meeting->getMeetingCouncilType() == 2){
            $council_type = config('meetings.council_types.board_level.'.$meeting->council_type) ;
        }

        
        return $this->subject('New Meeting Scheduled')
                    ->view('emails.create-meeting-notification')
                    ->with([
                    'description' => $this->meeting->description,
                    'date' => $this->meeting->meeting_date_time,
                    'venue' => $this->meeting->venue_id,
                    'submission_start' => $this->meeting->submission_start,
                    'submission_end' => $this->meeting->submission_end,
                    'link' => $this->meeting->link,
                    'quarter' => config('meetings.quarterly_meetings')[$this->meeting->quarter] ?? 'N/A',
                    'council_type' => $council_type,
                    'modality' => $this->meeting->modality,
                    ]);
    }
   
}
