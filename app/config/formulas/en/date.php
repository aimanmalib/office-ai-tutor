<?php

declare(strict_types=1);

// Teks EN rujukan fungsi (date); kunci = name dalam config/formulas/date.php, susunan sama.
return [
    'DATE' => ['desc' => 'Returns the serial number of a date built from separate year, month, and day values.', 'usecase' => 'Building a full date from separate year, month, and day columns.'],
    'DATEDIF' => ['desc' => 'Returns the difference between two dates in years, months, or days, based on the unit you choose (Y, M, D, MD, YM, or YD).', 'usecase' => 'Calculating an employee\'s age or length of service in complete years.', 'sample' => 'Age in completed years'],
    'DATEVALUE' => ['desc' => 'Converts a date stored as text into a date serial number.'],
    'DAY' => ['desc' => 'Returns the day of the month (1 to 31) for a date.'],
    'DAYS' => ['desc' => 'Returns the number of days between two dates, with the end date as the first argument.', 'usecase' => 'Counting how many days an invoice has been outstanding.'],
    'DAYS360' => ['desc' => 'Returns the number of days between two dates based on a 360-day year (12 months of 30 days), as used in some accounting calculations.'],
    'EDATE' => ['desc' => 'Returns the serial number of the date that falls a given number of months before or after a start date.', 'usecase' => 'Calculating the end date of a subscription or contract from its length in months.', 'sample' => 'The date three months after today'],
    'EOMONTH' => ['desc' => 'Returns the last day of the month that is a given number of months before or after a start date.', 'usecase' => 'Finding the month-end closing date for monthly reports.', 'sample' => 'Last day of the current month'],
    'HOUR' => ['desc' => 'Returns the hour (0 to 23) of a time value.'],
    'ISOWEEKNUM' => ['desc' => 'Returns the ISO 8601 week number of a date, with weeks starting on Monday.'],
    'MINUTE' => ['desc' => 'Returns the minute (0 to 59) of a time value.'],
    'MONTH' => ['desc' => 'Returns the month (1 to 12) of a date.', 'usecase' => 'Grouping transactions by month for a monthly sales summary.'],
    'NETWORKDAYS' => ['desc' => 'Returns the number of working days between two dates, excluding Saturdays, Sundays, and any listed holidays.', 'usecase' => 'Counting the working days available to complete a project.'],
    'NETWORKDAYS.INTL' => ['desc' => 'Returns the number of working days between two dates using the weekend days you specify, excluding any listed holidays.'],
    'NOW' => ['desc' => 'Returns the current date and time, which updates each time the worksheet recalculates.', 'usecase' => 'Showing the current date and time on a printed report.'],
    'SECOND' => ['desc' => 'Returns the second (0 to 59) of a time value.'],
    'TIME' => ['desc' => 'Returns the decimal number for a time built from hour, minute, and second values.'],
    'TIMEVALUE' => ['desc' => 'Converts a time stored as text into the decimal number that represents it.'],
    'TODAY' => ['desc' => 'Returns today\'s date, which updates whenever the workbook is opened or recalculated.', 'usecase' => 'Calculating how many days a payment is overdue as of today.'],
    'WEEKDAY' => ['desc' => 'Returns the day of the week for a date as a number, with the numbering scheme set by <code>return_type</code>.', 'usecase' => 'Checking whether a booking date falls on a weekday or a weekend.', 'sample' => '1 = Monday through 7 = Sunday'],
    'WEEKNUM' => ['desc' => 'Returns the week number of the year (1 to 54) for a date.'],
    'WORKDAY' => ['desc' => 'Returns the date that falls a given number of working days before or after a start date, excluding weekends and any listed holidays.', 'usecase' => 'Setting a project deadline 10 working days from today.'],
    'WORKDAY.INTL' => ['desc' => 'Returns the date that falls a given number of working days before or after a start date, using the weekend days you specify and excluding any listed holidays.'],
    'YEAR' => ['desc' => 'Returns the year of a date.', 'usecase' => 'Grouping sales data by year.'],
    'YEARFRAC' => ['desc' => 'Returns the fraction of a year represented by the days between two dates, using the day-count basis you choose.', 'usecase' => 'Prorating a bonus or annual leave for an employee who has served less than a full year.'],
];
