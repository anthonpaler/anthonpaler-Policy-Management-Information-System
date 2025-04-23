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
