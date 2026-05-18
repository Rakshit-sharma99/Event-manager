<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $images = Gallery::where('event_id', $eventId)->latest()->paginate(18);

        return view('gallery.index', compact('event', 'images'));
    }

    public function store(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $data = $request->validate([
            'images.*' => ['required', 'image', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:120'],
        ]);

        foreach ($request->file('images', []) as $image) {
            Gallery::create([
                'event_id' => (string) $event->getKey(),
                'image_path' => $image->store('gallery', 'public'),
                'caption' => $data['caption'] ?? 'Event moment',
            ]);
        }

        return back()->with('success', 'Gallery updated.');
    }

    public function destroy(Request $request, string $eventId, string $imageId)
    {
        $this->ownEvent($request, $eventId);
        Gallery::where('_id', $imageId)->where('event_id', $eventId)->delete();

        return back()->with('success', 'Image removed.');
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}
