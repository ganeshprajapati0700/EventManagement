<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
class TicketController extends Controller
{
    public function ticketstore(Request $request) 
    {  
        $this->validate($request, [
            "ticket_id" => "required|unique:tickets,ticket_id",
            "ticket_no" => "required|unique:tickets,ticket_no",
            "price" => "required|numeric",
        ]);

        $tickets = new Ticket();
        $tickets->ticket_id = $request->ticket_id;
        $tickets->ticket_no = $request->ticket_no;
        $tickets->price = $request->price;
        $tickets->save();
        

        return redirect('/tickets');
    }

   
    public function tickets()
    {
        // Get all tickets from the database
        $tickets = Ticket::all();

        // Return all tickets as JSON
        return response()->json(['tickets' => $tickets]);
    }

    public function destroy(Request $request, $id)
    {  
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    public function update(Request $request, $ticketId)
    {
        // Validate the request data if needed
        $this->validate($request, [
            // "ticket_id" => "required|unique:tickets,ticket_id",
            // "ticket_no" => "required|unique:tickets,ticket_no",
            "price" => "required|numeric",
        ]);

        // Find the ticket by ID
        $ticket = Ticket::findOrFail($ticketId);

        // Update ticket details
        $ticket->ticket_id = $request->input('ticket_id');
        $ticket->ticket_no = $request->input('ticket_no');
        $ticket->price = $request->input('price');
        // Update other fields as needed

        // Save the updated ticket
        $ticket->save();

        // Redirect or return a response as needed
        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully');
    }
}
    
