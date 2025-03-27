<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Proposal;
use App\Models\LocalCouncilMeeting;

class ProposalSubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $proposal;
    public $meeting;

    /**
     * Create a new message instance.
     */
    public function __construct(Proposal $proposal, LocalCouncilMeeting $meeting)
    {
        $this->proposal = $proposal;
        $this->meeting = $meeting;
    }

    public function build()
    {

        $level_mapping = [
            'Local' => 'local_level',
            'University' => 'university_level',
            'Board' => 'board_level',
        ];
        
        $level = ucfirst(strtolower($this->meeting->getMeetingLevel())); // Ensures it matches keys in $level_mapping
        $level_key = $level_mapping[$level] ?? null;
        // Retrieve council type name
        $council_type = $level_key && isset(config('meetings.council_types')[$level_key][$this->meeting->council_type])
        ? config('meetings.council_types')[$level_key][$this->meeting->council_type]
        : 'N/A';

        $quarter = config('meetings.quaterly_meetings')[$this->meeting->quarter] ?? 'N/A';



        return $this->subject('New Proposal Submitted')
                    ->view('emails.proposal_submission_notification')
                    ->with([
                        'description' => $this->meeting->description,
                        'date' => $this->meeting->meeting_date_time,
                        'venue' => $this->meeting->venue_id,
                        'submission_start' => $this->meeting->submission_start,
                        'submission_end' => $this->meeting->submission_end,
                        'link' => $this->meeting->link,
                        'year' => $this->meeting->year,
                        'quarter' => config('meetings.quaterly_meetings')[$this->meeting->quarter] ?? 'N/A',
                        'council_type' => $council_type,
                        'modality' => $this->meeting->modality,
                        ]);
    }
}
