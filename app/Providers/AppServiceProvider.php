<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
      // // Custom validation rule to check if there are already 4 meetings set for the year
      // Validator::extend('max_meetings_per_year', function ($attribute, $value, $parameters, $validator) {
      //     $year = $value;
      //     $meetingsCount = Meetings::where('year', $year)
      //         // ->where('status', '!=', 1) # uncomment this line if you want to include status
      //         ->count();
      //     return $meetingsCount < 4;
      // }, 'There are already 4 meetings set for this year.');

      // Custom validation rule to check if there is already a meeting set for the same year and quarter
      Validator::extend('unique_meeting_per_quarter', function ($attribute, $value, $parameters, $validator) {

          $year = $parameters[0];
          $level = $parameters[1];
          $campus_id = $parameters[2];
          $quarter = $value;
          $existingMeeting = Meetings::where('year', $year)
              ->where('quarter', $quarter)
              ->where('level', $level)
              ->where('campus_id', $campus_id)
              ->where('status', '!=', 1) // Exclude specific statuses if necessary
              ->first();
          return $existingMeeting === null;
      }, 'There is already a quarter set for this year.');

      // Custom validation rule to check if a date is before or equal to another date
      Validator::extend('before_or_equal_datetime', function ($attribute, $value, $parameters, $validator) {
          $datetime = $parameters[0];
          return strtotime($value) <= strtotime($datetime);
      }, 'The :attribute must be before or equal to the meeting date & time.');

      // Custom validation rule to check if a date is beyond or equal to meeting submission end date
      Validator::extend('beyond_or_equal_submission_end', function ($attribute, $value, $parameters, $validator) {
          $meetingId = $parameters[0];
          $meeting = \App\Models\Meetings::find($meetingId);
          if (!$meeting || !$meeting->submission_end) {
              return false; // Fail validation if meeting or submission_end is not found
          }
          // Compare the given value with submission_end
          return strtotime($value) >= strtotime($meeting->submission_end);
      }, 'The :attribute must be beyond or equal to the meeting\'s submission end date.');

      // Custom validation rule for preliminaries format
      Validator::extend('preliminaries_format', function ($attribute, $value, $parameters, $validator) {
          $lines = explode("\n", $value);

          foreach ($lines as $line) {
              // Ensure the line matches the required format
              if (!preg_match('/^\d+\.\d+ .+$/', trim($line))) {
                  return false;
              }
          }

          return true;
      }, 'Each line in the :attribute must follow the format "1.1 Call to Order".');
  }
}
