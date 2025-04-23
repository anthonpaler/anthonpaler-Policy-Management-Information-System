<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoardOob;


class BoardRegents extends Controller
{
    public function index(){
        // Check for available OOB for BOR
        $oob = BoardOob::where('status', 1)->latest()->first();

        if ($oob && $oob->meeting) {
            return redirect()->route(getUserRole().'.order_of_business.view-oob', [
                'level' => $oob->meeting->getMeetingLevel(),
                'oob_id' => encrypt($oob->id)
            ]);
        }

        // Otherwise, show dashboard with a "no agenda" notice
        $noAgendaMessage = "There is no available Order of Business (Provisional Agenda) at the moment.";

        return view('content.dashboard.bor.dashboard', compact('noAgendaMessage'));
    }
}
