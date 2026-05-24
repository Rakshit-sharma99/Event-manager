<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestRequest;
use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $guests = Guest::where('event_id', $eventId)
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->when($request->rsvp, fn ($q) => $q->where('rsvp_status', $request->rsvp))
            ->orderBy('name')
            ->paginate(14);
        $stats = $this->stats($eventId);

        return view('guests.index', compact('event', 'guests', 'stats'));
    }

    public function create(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $guest = new Guest(['rsvp_status' => 'pending']);

        return view('guests.create', compact('event', 'guest'));
    }

    public function store(GuestRequest $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        Guest::create([
            ...$request->validated(),
            'event_id' => (string) $event->getKey(),
            'rsvp_status' => $request->rsvp_status ?: 'pending',
            'invite_token' => Str::random(48),
        ]);

        return redirect()->route('guests.index', $event)->with('success', 'Guest added.');
    }

    public function bulkImport(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);

        if ($request->isMethod('get')) {
            return view('guests.bulk-import', compact('event'));
        }

        $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $rows = array_map('str_getcsv', file($request->file('csv')->getRealPath()));
        $imported = 0;

        foreach (array_slice($rows, 1) as $row) {
            if (count($row) < 2) {
                continue;
            }
            Guest::create([
                'event_id' => (string) $event->getKey(),
                'name' => $row[0],
                'email' => $row[1],
                'phone' => $row[2] ?? null,
                'rsvp_status' => 'pending',
                'dietary_preference' => $row[3] ?? 'not specified',
                'plus_one_count' => (int) ($row[4] ?? 0),
                'invite_token' => Str::random(48),
            ]);
            $imported++;
        }

        return redirect()->route('guests.index', $event)->with('success', "{$imported} guests imported.");
    }

    public function edit(Request $request, string $eventId, string $guestId)
    {
        $event = $this->ownEvent($request, $eventId);
        $guest = Guest::where('_id', $guestId)->where('event_id', $eventId)->firstOrFail();

        return view('guests.create', compact('event', 'guest'));
    }

    public function update(GuestRequest $request, string $eventId, string $guestId)
    {
        $this->ownEvent($request, $eventId);
        Guest::where('_id', $guestId)->where('event_id', $eventId)->firstOrFail()->update($request->validated());

        return redirect()->route('guests.index', $eventId)->with('success', 'Guest updated.');
    }

    public function destroy(Request $request, string $eventId, string $guestId)
    {
        $this->ownEvent($request, $eventId);
        Guest::where('_id', $guestId)->where('event_id', $eventId)->delete();

        return back()->with('success', 'Guest removed.');
    }

    public function sendInvite(Request $request, string $eventId, string $guestId)
    {
        $event = $this->ownEvent($request, $eventId);
        $guest = Guest::where('_id', $guestId)->where('event_id', $eventId)->firstOrFail();
        $guest->update(['invite_token' => $guest->invite_token ?: Str::random(48), 'invite_sent_at' => now()]);

        // Actually send the direct invitation email via MailService
        app(\App\Services\MailService::class)->sendGuestInvitation($guest, $event);

        return back()->with('success', 'Invitation email sent directly to ' . $guest->email);
    }

    public function publicRsvp(string $token)
    {
        $guest = Guest::where('invite_token', $token)->firstOrFail();
        $event = Event::findOrFail($guest->event_id);

        return view('guests.public-rsvp', compact('guest', 'event'));
    }

    public function submitRsvp(Request $request, string $token)
    {
        $guest = Guest::where('invite_token', $token)->firstOrFail();
        $data = $request->validate([
            'rsvp_status' => ['required', 'in:yes,no,maybe'],
            'dietary_preference' => ['nullable', 'string', 'max:80'],
            'dietary_note' => ['nullable', 'string', 'max:240'],
            'plus_one_count' => ['nullable', 'integer', 'min:0', 'max:5'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        $guest->update($data);
        GuestResponse::updateOrCreate(['guest_id' => (string) $guest->getKey()], [...$data, 'responded_at' => now()]);

        return back()->with('success', 'RSVP received. Thank you.');
    }

    public function export(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $rows = Guest::where('event_id', $eventId)->get()->map(fn ($guest) => [
            $guest->name, $guest->email, $guest->phone, $guest->rsvp_status, $guest->dietary_preference, $guest->plus_one_count,
        ]);
        $csv = "Name,Email,Phone,RSVP,Dietary,Plus One\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', $row))."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.Str::slug($event->event_name).'-guests.csv"',
        ]);
    }

    public function statsJson(Request $request, string $eventId)
    {
        $this->ownEvent($request, $eventId);

        return response()->json($this->stats($eventId));
    }

    private function stats(string $eventId): array
    {
        return [
            'yes' => Guest::where('event_id', $eventId)->where('rsvp_status', 'yes')->count(),
            'no' => Guest::where('event_id', $eventId)->where('rsvp_status', 'no')->count(),
            'maybe' => Guest::where('event_id', $eventId)->where('rsvp_status', 'maybe')->count(),
            'pending' => Guest::where('event_id', $eventId)->where('rsvp_status', 'pending')->count(),
        ];
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}
