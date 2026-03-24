<?php
// app/Console/Commands/SendEventReminders.php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\CampusEvent;
use App\Models\EventAttendance;
use App\Traits\PublishesRedisEvents;
use App\Enums\RealtimeEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    use PublishesRedisEvents;

    protected $signature   = 'events:send-reminders';
    protected $description = 'Send 24h-before reminder emails and notifications to event attendees';

    public function handle(): void
    {
        // Find events starting between 23h and 25h from now (1h window, run hourly)
        $windowStart = now()->addHours(23);
        $windowEnd   = now()->addHours(25);

        $events = CampusEvent::with('organizer')
            ->whereBetween('start_date', [$windowStart, $windowEnd])
            ->whereNotNull('published_at')
            ->get();

        $this->info("Found {$events->count()} event(s) in the 24h window.");

        foreach ($events as $event) {
            // Get attendees who haven't received a reminder yet
            $attendances = EventAttendance::with('user')
                ->where('event_id', $event->id)
                ->where('status', 'registered')
                ->where('reminder_sent', false)
                ->get();

            $this->info("  Event '{$event->title}': {$attendances->count()} attendee(s) to remind.");

            foreach ($attendances as $attendance) {
                try {
                    // Send reminder email
                    Mail::to($attendance->user->email)
                        ->send(new EventReminderMail($event, $attendance->user));

                    // Send realtime notification
                    $this->publishEvent(RealtimeEvent::EVENT_REMINDER, [
                        'eventId'    => $event->id,
                        'eventTitle' => $event->title,
                        'userId'     => $attendance->user->id,
                        'startDate'  => $event->start_date->toISOString(),
                        'location'   => $event->location,
                    ]);

                    $attendance->update(['reminder_sent' => true]);

                    $this->line("    ✓ Reminder sent to {$attendance->user->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send event reminder to {$attendance->user->email}: {$e->getMessage()}");
                    $this->warn("    ✗ Failed for {$attendance->user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info('Done.');
    }
}
