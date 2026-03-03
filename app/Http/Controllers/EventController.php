<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    public function events(Request $request){

        $today = Carbon::today(); 

        $rowcheck = $request->row ? $request->row : 10;

        $events = Event::when($request->search,function($query,$search){
               $query->where('title','LIKE','%'.$search.'%');
        })->when($request->date,function($query,$date){
               $query->whereDate('date', $request->date);
        })->when($request->location,function($query,$location){
                $query->where('location','LIKE','%'.$location.'%');
        })
        ->whereDate('date', '>=', $today)
        ->orderBy('created_at','desc')
        ->paginate($rowcheck);

       $pagination_meta["total"] = $events->total();
       $pagination_meta["per_page"] = $events->perPage();
       $pagination_meta["current_page"] = $events->currentPage();
       $pagination_meta["last_page"] = $events->lastPage();
       $pagination_meta["previous"] = $events->previousPageUrl();
       $pagination_meta["next"] = $events->nextPageUrl();

       return response()->json(['status' => 'success','events' => $events->items(),'pagination' => $pagination_meta],200);
    }

    public function addEvents(Request $request){

        $validate = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date|after_or_equal:today',
            'location' => 'required',
        ]);

        if($validate->fails()){
            return response()->json(['status' => 'error','message' => $validate->errors()->first()],400);
        }

        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->location = $request->location;
        $event->user_id = $request->user()->id;
        $event->save();

         return response()->json([
            'status' => 'success',
            'message'=>'Event Created successfully'
        ],201);

    }

    public function editEvents(Request $request,$id){

        $validate = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date|after_or_equal:today',
            'location' => 'required',
        ]);

        if($validate->fails()){
            return response()->json(['status' => 'error','message' => $validate->errors()->first()],400);
        }

        $event = Event::findOrFail($id);

        $event->title = $request->title;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->location = $request->location;
        $event->save();

         return response()->json([
            'status' => 'success',
            'message'=>'Event updated successfully'
        ],200);

    }

    public function view($id)
    {
        $event = Event::with([
                'tickets' => function ($query) {
                    $query->withSum([
                        'bookings as booked_quantity' => function ($q) {
                            $q->whereIn('status', ['confirmed', 'pending']);
                        }
                    ], 'quantity');
                }
            ])->find($id);

        return response()->json(['status' => 'success','event' => $event],200);
    }

    public function delete($id){

        $event = Event::findOrFail($id);

        foreach ($event->tickets as $ticket) {
            if ($ticket->bookings()->exists()) {
                return response()->json(['status' => 'error','message' => 'Some tickets have bookings ! So you cannot delete the event.'], 403);
            }
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully'
        ],200);
    }
}
