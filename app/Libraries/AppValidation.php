<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

/**
 * Custom Validation Rules for the application.
 * Contains the logic for the 'isTodayOrFuture' and 'validTime' rules.
 *
 * NOTE: For these rules to work, this class must be registered in
 * app/Config/Validation.php in the $ruleSets array.
 */
class AppValidation
{
    /**
     * Validates if a given date string is today's date or a future date.
     * (Rule: isTodayOrFuture)
     *
     * @param string|null $str The date string input from the form (e.g., 'YYYY-MM-DD').
     * @return bool True if the date is today or later.
     */
    public function isTodayOrFuture(string $str = null): bool
    {
        if (empty($str)) {
            // Let the 'required' rule handle empty or missing fields.
            return false;
        }

        try {
            // 1. Parse the input string into a CodeIgniter Time object
            $inputDate = Time::parse($str);

            // 2. Get today's date and set the time to midnight (00:00:00)
            $today = Time::now()->setTime(0, 0, 0);

            // 3. Set the input date's time to midnight as well for accurate day-level comparison
            $inputDate->setTime(0, 0, 0);

            // 4. Check if the input date is greater than or equal to today's date.
            return $inputDate->getTimestamp() >= $today->getTimestamp();
        } catch (\Exception $e) {
            // Log error or handle exception if date parsing fails
            return false;
        }
    }

    /**
     * Validates if a string is a valid 24-hour time format (HH:MM or HH:MM:SS).
     * This is the missing rule definition.
     * (Rule: valid_time)
     *
     * @param string|null $str The time string input (e.g., '14:30' or '09:00:00').
     * @return bool True if the time format is valid.
     */
    public function validTime(string $str = null): bool
    {
        if (empty($str)) {
            // Use 'required' to check for emptiness
            return false;
        }

        // Regex for HH:MM (00:00 to 23:59)
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str)) {
            return true;
        }

        // Regex for HH:MM:SS (00:00:00 to 23:59:59)
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $str)) {
            return true;
        }

        // Failed to match standard time formats
        return false;
    }
}
