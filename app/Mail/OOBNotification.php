<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OOBNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $orderOfBusiness;

    /**
     * Create a new message instance.
     */
    public function __construct($orderOfBusiness, $meeting)
    {
        $this->orderOfBusiness = $orderOfBusiness;
        $this->meeting = $meeting;

    }
    public function build()
    {
        $meeting = $this->orderOfBusiness->meeting;

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

    return $this->subject('Order of Business Dissemination Notice')
                ->view('emails.oob_notification')
                ->with([
                    'meeting' => $meeting,
                    'orderOfBusiness' => $this->orderOfBusiness,
                    'year' => $meeting->year,
                    'council_type' => $council_type,
                ]);
    }
}
