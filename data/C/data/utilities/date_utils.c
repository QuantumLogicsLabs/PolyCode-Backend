/*
 * File: date_utils.c
 * Description: Date and time utility functions for C programming
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <stdbool.h>

// Date structure
typedef struct {
    int year;
    int month;
    int day;
} Date;

// Time structure
typedef struct {
    int hour;
    int minute;
    int second;
} Time;

// DateTime structure
typedef struct {
    Date date;
    Time time;
} DateTime;

// Month names
const char* MONTH_NAMES[] = {
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
};

const char* MONTH_SHORT_NAMES[] = {
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
};

// Days of week
const char* DAY_NAMES[] = {
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
};

const char* DAY_SHORT_NAMES[] = {
    "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
};

// ============================================================================
// BASIC DATE FUNCTIONS
// ============================================================================

/**
 * @brief Create a Date structure
 * @param year Year (e.g., 2026)
 * @param month Month (1-12)
 * @param day Day (1-31)
 * @return Date structure
 */
Date createDate(int year, int month, int day) {
    Date date;
    date.year = year;
    date.month = month;
    date.day = day;
    return date;
}

/**
 * @brief Create a Time structure
 * @param hour Hour (0-23)
 * @param minute Minute (0-59)
 * @param second Second (0-59)
 * @return Time structure
 */
Time createTime(int hour, int minute, int second) {
    Time time;
    time.hour = hour;
    time.minute = minute;
    time.second = second;
    return time;
}

/**
 * @brief Create a DateTime structure
 * @param date Date component
 * @param time Time component
 * @return DateTime structure
 */
DateTime createDateTime(Date date, Time time) {
    DateTime datetime;
    datetime.date = date;
    datetime.time = time;
    return datetime;
}

/**
 * @brief Check if a year is a leap year
 * @param year Year to check
 * @return true if leap year, false otherwise
 */
bool isLeapYear(int year) {
    if (year % 4 != 0) {
        return false;
    } else if (year % 100 != 0) {
        return true;
    } else if (year % 400 != 0) {
        return false;
    } else {
        return true;
    }
}

/**
 * @brief Get the number of days in a month
 * @param year Year (for leap year calculation)
 * @param month Month (1-12)
 * @return Number of days in the month, or -1 for invalid month
 */
int daysInMonth(int year, int month) {
    if (month < 1 || month > 12) return -1;
    
    switch (month) {
        case 1: case 3: case 5: case 7: case 8: case 10: case 12:
            return 31;
        case 4: case 6: case 9: case 11:
            return 30;
        case 2:
            return isLeapYear(year) ? 29 : 28;
        default:
            return -1;
    }
}

/**
 * @brief Validate a date
 * @param date Date to validate
 * @return true if valid, false otherwise
 */
bool isValidDate(Date date) {
    if (date.year < 1 || date.month < 1 || date.month > 12 || date.day < 1) {
        return false;
    }
    
    int max_days = daysInMonth(date.year, date.month);
    return date.day <= max_days;
}

/**
 * @brief Validate a time
 * @param time Time to validate
 * @return true if valid, false otherwise
 */
bool isValidTime(Time time) {
    return (time.hour >= 0 && time.hour < 24 &&
            time.minute >= 0 && time.minute < 60 &&
            time.second >= 0 && time.second < 60);
}

// ============================================================================
// DATE COMPARISON AND ARITHMETIC
// ============================================================================

/**
 * @brief Compare two dates
 * @param date1 First date
 * @param date2 Second date
 * @return -1 if date1 < date2, 0 if equal, 1 if date1 > date2
 */
int compareDates(Date date1, Date date2) {
    if (date1.year != date2.year) {
        return (date1.year < date2.year) ? -1 : 1;
    }
    if (date1.month != date2.month) {
        return (date1.month < date2.month) ? -1 : 1;
    }
    if (date1.day != date2.day) {
        return (date1.day < date2.day) ? -1 : 1;
    }
    return 0;
}

/**
 * @brief Compare two times
 * @param time1 First time
 * @param time2 Second time
 * @return -1 if time1 < time2, 0 if equal, 1 if time1 > time2
 */
int compareTimes(Time time1, Time time2) {
    if (time1.hour != time2.hour) {
        return (time1.hour < time2.hour) ? -1 : 1;
    }
    if (time1.minute != time2.minute) {
        return (time1.minute < time2.minute) ? -1 : 1;
    }
    if (time1.second != time2.second) {
        return (time1.second < time2.second) ? -1 : 1;
    }
    return 0;
}

/**
 * @brief Calculate days between two dates
 * @param date1 First date
 * @param date2 Second date
 * @return Number of days between dates (positive if date2 > date1)
 */
int daysBetween(Date date1, Date date2) {
    // Simple implementation using tm structure
    struct tm tm1 = {0};
    tm1.tm_year = date1.year - 1900;
    tm1.tm_mon = date1.month - 1;
    tm1.tm_mday = date1.day;
    tm1.tm_hour = 12; // Noon to avoid daylight saving issues
    
    struct tm tm2 = {0};
    tm2.tm_year = date2.year - 1900;
    tm2.tm_mon = date2.month - 1;
    tm2.tm_mday = date2.day;
    tm2.tm_hour = 12; // Noon to avoid daylight saving issues
    
    time_t time1 = mktime(&tm1);
    time_t time2 = mktime(&tm2);
    
    if (time1 == -1 || time2 == -1) return 0;
    
    double diff_seconds = difftime(time2, time1);
    return (int)(diff_seconds / (24 * 60 * 60));
}

/**
 * @brief Add days to a date
 * @param date Original date
 * @param days Number of days to add (can be negative)
 * @return New date after adding days
 */
Date addDays(Date date, int days) {
    struct tm tm = {0};
    tm.tm_year = date.year - 1900;
    tm.tm_mon = date.month - 1;
    tm.tm_mday = date.day;
    tm.tm_hour = 12; // Noon to avoid daylight saving issues
    
    time_t time = mktime(&tm);
    if (time == -1) return date;
    
    time += days * 24 * 60 * 60; // Add days in seconds
    struct tm* new_tm = localtime(&time);
    
    Date new_date;
    new_date.year = new_tm->tm_year + 1900;
    new_date.month = new_tm->tm_mon + 1;
    new_date.day = new_tm->tm_mday;
    
    return new_date;
}

// ============================================================================
// FORMATTING FUNCTIONS
// ============================================================================

/**
 * @brief Format date as string
 * @param date Date to format
 * @param format Format string (supports YYYY, MM, DD, MMM, etc.)
 * @param buffer Buffer to store formatted string
 * @param buffer_size Size of buffer
 */
void formatDate(Date date, const char* format, char* buffer, size_t buffer_size) {
    char temp[256];
    size_t pos = 0;
    
    for (size_t i = 0; format[i] != '\0' && pos < buffer_size - 1; i++) {
        if (format[i] == 'Y' && format[i+1] == 'Y' && format[i+2] == 'Y' && format[i+3] == 'Y') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%04d", date.year);
            i += 3;
        } else if (format[i] == 'M' && format[i+1] == 'M' && format[i+2] == 'M') {
            snprintf(temp, sizeof(temp), "%s", MONTH_SHORT_NAMES[date.month - 1]);
            pos += snprintf(buffer + pos, buffer_size - pos, "%s", temp);
            i += 2;
        } else if (format[i] == 'M' && format[i+1] == 'M') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%02d", date.month);
            i += 1;
        } else if (format[i] == 'D' && format[i+1] == 'D') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%02d", date.day);
            i += 1;
        } else {
            pos += snprintf(buffer + pos, buffer_size - pos, "%c", format[i]);
        }
    }
    
    buffer[pos] = '\0';
}

/**
 * @brief Format time as string
 * @param time Time to format
 * @param format Format string (supports HH, MM, SS)
 * @param buffer Buffer to store formatted string
 * @param buffer_size Size of buffer
 */
void formatTime(Time time, const char* format, char* buffer, size_t buffer_size) {
    size_t pos = 0;
    
    for (size_t i = 0; format[i] != '\0' && pos < buffer_size - 1; i++) {
        if (format[i] == 'H' && format[i+1] == 'H') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%02d", time.hour);
            i += 1;
        } else if (format[i] == 'M' && format[i+1] == 'M') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%02d", time.minute);
            i += 1;
        } else if (format[i] == 'S' && format[i+1] == 'S') {
            pos += snprintf(buffer + pos, buffer_size - pos, "%02d", time.second);
            i += 1;
        } else {
            pos += snprintf(buffer + pos, buffer_size - pos, "%c", format[i]);
        }
    }
    
    buffer[pos] = '\0';
}

/**
 * @brief Format datetime as string
 * @param datetime DateTime to format
 * @param format Format string (combines date and time formats)
 * @param buffer Buffer to store formatted string
 * @param buffer_size Size of buffer
 */
void formatDateTime(DateTime datetime, const char* format, char* buffer, size_t buffer_size) {
    char temp[256];
    size_t pos = 0;
    
    for (size_t i = 0; format[i] != '\0' && pos < buffer_size - 1; i++) {
        if (format[i] == 'Y' || format[i] == 'M' || format[i] == 'D') {
            // Date formatting
            char date_format[10] = {0};
            int j = 0;
            while (i < strlen(format) && (format[i] == 'Y' || format[i] == 'M' || format[i] == 'D')) {
                date_format[j++] = format[i++];
            }
            i--;
            formatDate(datetime.date, date_format, temp, sizeof(temp));
            pos += snprintf(buffer + pos, buffer_size - pos, "%s", temp);
        } else if (format[i] == 'H' || format[i] == 'S') {
            // Time formatting
            char time_format[10] = {0};
            int j = 0;
            while (i < strlen(format) && (format[i] == 'H' || format[i] == 'M' || format[i] == 'S')) {
                time_format[j++] = format[i++];
            }
            i--;
            formatTime(datetime.time, time_format, temp, sizeof(temp));
            pos += snprintf(buffer + pos, buffer_size - pos, "%s", temp);
        } else {
            pos += snprintf(buffer + pos, buffer_size - pos, "%c", format[i]);
        }
    }
    
    buffer[pos] = '\0';
}

// ============================================================================
// CURRENT DATE/TIME FUNCTIONS
// ============================================================================

/**
 * @brief Get current date
 * @return Current date
 */
Date getCurrentDate() {
    time_t now = time(NULL);
    struct tm* tm_now = localtime(&now);
    
    return createDate(tm_now->tm_year + 1900, tm_now->tm_mon + 1, tm_now->tm_mday);
}

/**
 * @brief Get current time
 * @return Current time
 */
Time getCurrentTime() {
    time_t now = time(NULL);
    struct tm* tm_now = localtime(&now);
    
    return createTime(tm_now->tm_hour, tm_now->tm_min, tm_now->tm_sec);
}

/**
 * @brief Get current datetime
 * @return Current datetime
 */
DateTime getCurrentDateTime() {
    return createDateTime(getCurrentDate(), getCurrentTime());
}

/**
 * @brief Get day of week for a date
 * @param date Date to get day of week for
 * @return Day of week (0=Sunday, 1=Monday, ..., 6=Saturday)
 */
int getDayOfWeek(Date date) {
    struct tm tm = {0};
    tm.tm_year = date.year - 1900;
    tm.tm_mon = date.month - 1;
    tm.tm_mday = date.day;
    tm.tm_hour = 12; // Noon to avoid daylight saving issues
    
    time_t time = mktime(&tm);
    struct tm* tm_result = localtime(&time);
    
    return tm_result->tm_wday;
}

/**
 * @brief Get day of year for a date
 * @param date Date to get day of year for
 * @return Day of year (1-366)
 */
int getDayOfYear(Date date) {
    int day_of_year = date.day;
    
    for (int month = 1; month < date.month; month++) {
        day_of_year += daysInMonth(date.year, month);
    }
    
    return day_of_year;
}

/**
 * @brief Get week of year for a date
 * @param date Date to get week of year for
 * @return Week of year (1-53)
 */
int getWeekOfYear(Date date) {
    int day_of_year = getDayOfYear(date);
    int first_day_of_year = getDayOfWeek(createDate(date.year, 1, 1));
    
    // Adjust for the first week
    int week = (day_of_year + first_day_of_year - 1) / 7 + 1;
    
    return week;
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * @brief Print date information
 * @param date Date to print
 */
void printDateInfo(Date date) {
    printf("Date: %04d-%02d-%02d\n", date.year, date.month, date.day);
    printf("Day of week: %s\n", DAY_NAMES[getDayOfWeek(date)]);
    printf("Day of year: %d\n", getDayOfYear(date));
    printf("Week of year: %d\n", getWeekOfYear(date));
    printf("Leap year: %s\n", isLeapYear(date.year) ? "Yes" : "No");
    printf("Days in month: %d\n", daysInMonth(date.year, date.month));
}

/**
 * @brief Print time information
 * @param time Time to print
 */
void printTimeInfo(Time time) {
    printf("Time: %02d:%02d:%02d\n", time.hour, time.minute, time.second);
    
    if (time.hour < 12) {
        printf("12-hour format: %02d:%02d:%02d AM\n", 
               (time.hour == 0) ? 12 : time.hour, time.minute, time.second);
    } else {
        printf("12-hour format: %02d:%02d:%02d PM\n", 
               (time.hour == 12) ? 12 : time.hour - 12, time.minute, time.second);
    }
}

/**
 * @brief Print datetime information
 * @param datetime DateTime to print
 */
void printDateTimeInfo(DateTime datetime) {
    printDateInfo(datetime.date);
    printTimeInfo(datetime.time);
}

// ============================================================================
// TEST FUNCTION
// ============================================================================

void testDateUtils() {
    printf("=== Date Utilities Test ===\n\n");
    
    // Test current date/time
    printf("1. Current Date/Time:\n");
    Date current_date = getCurrentDate();
    Time current_time = getCurrentTime();
    DateTime current_datetime = createDateTime(current_date, current_time);
    
    char buffer[100];
    formatDate(current_date, "YYYY-MM-DD", buffer, sizeof(buffer));
    printf("   Current date: %s\n", buffer);
    
    formatTime(current_time, "HH:MM:SS", buffer, sizeof(buffer));
    printf("   Current time: %s\n", buffer);
    
    formatDateTime(current_datetime, "YYYY-MM-DD HH:MM:SS", buffer, sizeof(buffer));
    printf("   Current datetime: %s\n", buffer);
    
    // Test date creation and validation
    printf("\n2. Date Creation and Validation:\n");
    Date test_date1 = createDate(2026, 3, 23);
    Date test_date2 = createDate(2024, 2, 29); // Leap year
    Date test_date3 = createDate(2023, 2, 29); // Invalid date
    
    printf("   2026-03-23 is valid: %s\n", isValidDate(test_date1) ? "Yes" : "No");
    printf("   2024-02-29 is valid: %s\n", isValidDate(test_date2) ? "Yes" : "No");
    printf("   2023-02-29 is valid: %s\n", isValidDate(test_date3) ? "Yes" : "No");
    
    // Test leap year
    printf("\n3. Leap Year Test:\n");
    printf("   2024 is leap year: %s\n", isLeapYear(2024) ? "Yes" : "No");
    printf("   2023 is leap year: %s\n", isLeapYear(2023) ? "Yes" : "No");
    printf("   2000 is leap year: %s\n", isLeapYear(2000) ? "Yes" : "No");
    printf("   1900 is leap year: %s\n", isLeapYear(1900) ? "Yes" : "No");
    
    // Test date comparison
    printf("\n4. Date Comparison:\n");
    Date date1 = createDate(2026, 3, 23);
    Date date2 = createDate(2026, 3, 24);
    Date date3 = createDate(2026, 3, 23);
    
    int cmp1 = compareDates(date1, date2);
    int cmp2 = compareDates(date2, date1);
    int cmp3 = compareDates(date1, date3);
    
    printf("   2026-03-23 vs 2026-03-24: %d\n", cmp1);
    printf("   2026-03-24 vs 2026-03-23: %d\n", cmp2);
    printf("   2026-03-23 vs 2026-03-23: %d\n", cmp3);
    
    // Test days between
    printf("\n5. Days Between:\n");
    Date start = createDate(2026, 3, 23);
    Date end = createDate(2026, 4, 2);
    
    int days = daysBetween(start, end);
    printf("   Days between 2026-03-23 and 2026-04-02: %d\n", days);
    
    // Test add days
    printf("\n6. Add Days:\n");
    Date base = createDate(2026, 3, 23);
    Date future = addDays(base, 10);
    Date past = addDays(base, -5);
    
    formatDate(future, "YYYY-MM-DD", buffer, sizeof(buffer));
    printf("   2026-03-23 + 10 days = %s\n", buffer);
    
    formatDate(past, "YYYY-MM-DD", buffer, sizeof(buffer));
    printf("   2026-03-23 - 5 days = %s\n", buffer);
    
    // Test date info
    printf("\n7. Date Information:\n");
    printDateInfo(test_date1);
    
    // Test time info
    printf("\n8. Time Information:\n");
    Time test_time = createTime(14, 30, 45);
    printTimeInfo(test_time);
    
    // Test datetime info
    printf("\n9. DateTime Information:\n");
    DateTime test_datetime = createDateTime(test_date1, test_time);
    printDateTimeInfo(test_datetime);
    
    printf("\n=== Date utilities test completed ===\n");
}

int main() {
    testDateUtils();
    return 0;
}
