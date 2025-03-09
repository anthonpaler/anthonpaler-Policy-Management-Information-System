<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
      // Custom validation rule to check if there is already a meeting set for the same year and quarter
      Validator::extend('unique_meeting_per_quarter', function ($attribute, $value, $parameters, $validator) {
          $year = $parameters[0];
          $level = $parameters[1];
          $campus_id = $parameters[2];
          $council_type = $parameters[3];
          $quarter = $value;

          if($level == 0){
            $existingMeeting = LocalCouncilMeeting::where('year', $year)
            ->where('quarter', $quarter)
            ->where('campus_id', $campus_id)
            ->where('council_type', $council_type)
            ->first();
            return $existingMeeting === null;
          } 

          if($level == 1){
            $existingMeeting = UniversityCouncilMeeting::where('year', $year)
            ->where('quarter', $quarter)
            ->where('council_type', $council_type)
            ->first();
            return $existingMeeting === null;
          } 

          if($level == 2){
            $existingMeeting = BorMeeting::where('year', $year)
            ->where('quarter', $quarter)
            ->where('council_type', $council_type)
            ->first();
            return $existingMeeting === null;
          } 
         
      }, 'There is already a quarter with the same council type set for this year.');

      // Custom validation rule to check if a date is beyond or equal to meeting submission end date

    //   Validator::extend('beyond_or_equal_submission_end', function ($attribute, $value, $parameters, $validator) {
    //       $meetingId = $parameters[0];
    //       $meeting = \App\Models\Meetings::find($meetingId);
    //       if (!$meeting || !$meeting->submission_end) {
    //           return false; // Fail validation if meeting or submission_end is not found
    //       }
    //       // Compare the given value with submission_end
    //       return strtotime($value) >= strtotime($meeting->submission_end);
    //   }, 'The :attribute must be beyond or equal to the meeting\'s submission end date.');
  }
}
