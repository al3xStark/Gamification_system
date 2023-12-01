<?php

namespace App\Http\Controllers\Api;

use App\Events\EventCreate;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPartnerUser;
use App\Models\PartnerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;


class EventsController extends Controller
{
    public function createAction(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'code' => 'required|string|exists:events,code,project_id,' . auth()->id(),
            'fields' => 'array',
        ]);

        $event = Event::where([
            'code' => $request->code,
        ])->first();

        $fields = $event->fields;

        if (!empty($event->fields)) {
            if (empty($request->fields)) {
                throw new \Exception('fields in required');
            }

            if (!empty(
            array_diff(
                $event->fields,
                array_keys(array_filter($request->fields, fn($value) => !empty($value)))
            )
            )) {
                throw new \Exception('fields different');
            }

            $fields = [];

            foreach ($event->fields as $key) {
                $fields[$key] = $request->fields[$key];
            }
        }

        $fieldsHash = hash('sha256', json_encode($fields));

        if (!($user = PartnerUser::where([
            'project_id' => auth()->id(),
            'user_id' => $request->user_id,
        ])->first())) {
            $user = PartnerUser::create([
                'project_id' => auth()->id(),
                'user_id' => $request->user_id,
            ]);
        }

        $eventWithUser = $event
            ->whereHas('eventPartnerUsers', fn($query) => $query->where([
                'partner_user_id' => $user->id,
                'fields_hash' => $fieldsHash,
            ]))
            ->first();

        if (empty($eventWithUser)) {
            $eventPartnerUser = EventPartnerUser::create([
                'event_id' => $event->id,
                'partner_user_id' => $user->id,
                'count' => 1,
                'fields' => $fields,
                'fields_hash' => $fieldsHash,
            ]);
        } else {
            $eventPartnerUser = $eventWithUser
                ->eventPartnerUsers()
                ->where([
                    'partner_user_id' => $user->id,
                    'fields_hash' => $fieldsHash,
                ])->first();
            $eventPartnerUser->count++;
            $eventPartnerUser->save();
            $eventPartnerUser->refresh();
        }

        event(new EventCreate($eventPartnerUser));

        return response([]);
    }
}
