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

        $level_mapping = [
            0 => 'local_level',
            1 => 'university_level',
            2 => 'board_level',
        ];
        
        $level = strtolower($this->meeting->getMeetingLevel()); // Converts 'Local' -> 'local', 'University' -> 'university'
        $level_key = "{$level}_level";
        $council_type = isset(config('meetings.council_types')[$level_key][$this->meeting->council_type]) 
        ? config('meetings.council_types')[$level_key][$this->meeting->council_type] 
        : 'N/A';
        
        return $this->subject('New Meeting Scheduled')
                    ->view('emails.create-meeting-notification')
                    ->with([
                    'description' => $this->meeting->description,
                    'date' => $this->meeting->meeting_date_time,
                    'venue' => $this->meeting->venue_id,
                    'submission_start' => $this->meeting->submission_start,
                    'submission_end' => $this->meeting->submission_end,
                    'link' => $this->meeting->link,
                    'quarter' => config('meetings.quaterly_meetings')[$this->meeting->quarter] ?? 'N/A',
                    'council_type' => $council_type,
                    'modality' => $this->meeting->modality,
                    ]);
    }
   
}
