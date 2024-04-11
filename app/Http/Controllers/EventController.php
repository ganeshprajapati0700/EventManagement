<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventTicket;
class EventController extends Controller
{
    public function event()
    {
        return view("events");
    }
    
    public function eventdatastore(Request $request)
    {
        $ticketData = json_decode($request->ticketdata, true);
        
            $event = new Event();
            $event->eventname = $request->name;
            $event->eventdesc = $request->desc;
            $event->startdate = $request->sdate;
            $event->enddate = $request->edate;
            $event->organiser = $request->org;
            $event->save();
        
            // dd($request->ticketdata);
            
            foreach ($ticketData  as $ticket) {
                $eventTicket = new EventTicket();
                $eventTicket->event_id = $event->id;
                $eventTicket->ticket_id = $ticket['ticket_id'];
                $eventTicket->ticket_no = $ticket['ticket_no'];
                $eventTicket->price = $ticket['price'];
                $eventTicket->save();
            }
    
            return redirect()->back()->with('success', 'Event and tickets saved successfully!');
        

    }
}
