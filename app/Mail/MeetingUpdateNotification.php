<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;
    public $updatedFields;
    /**
     * Create a new message instance.
     */
    public function __construct($meeting, $updatedFields)
    {
        $this->meeting = $meeting;
        $this->updatedFields = $updatedFields;
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

        
        return $this->subject('Meeting Updated Notification')
                    ->view('emails.meeting-update-notification')
                    ->with(['updatedFields' => $this->updatedFields,
                    'quarter' => config('meetings.quaterly_meetings')[$this->meeting->quarter] ?? 'N/A',
                    'council_type' => $council_type,

                
                
                ]);

    }

    /**
     * Get the message content definition.
     */
    
}
