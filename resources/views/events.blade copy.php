<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container" style="width: 60%; padding:4%;">
        <h2 style="text-align: center;">Event Management</h2>
        <form id="eventForm" action="{{ route('eventstore') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="eventname">Event Name</label>
                <input type="text" class="form-control" id="eventname" name="eventname">
            </div>
            <div class="form-group">
                <label for="eventdesc">Event Description</label>
                <textarea class="form-control" id="eventdesc" name="eventdesc"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="startdate">Start Date</label>
                    <input type="date" class="form-control" id="startdate" name="startdate">
                </div>
                <div class="form-group col-md-6">
                    <label for="enddate">End Date</label>
                    <input type="date" class="form-control" id="enddate" name="enddate">
                </div>
            </div>
            <div class="form-group">
                <label for="organiser">Organiser</label>
                <input type="text" class="form-control" id="organiser" name="organiser">
            </div>
            <div class="form-group">
                <label for="tickets">Tickets</label>
                <button type="button" class="btn btn-sm btn-primary" id="addTicketBtn">Add New Ticket</button>
            </div>
            
        </form>
        <div id="ticketRows">
            <table class="table table-bordered" id="ticketTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ticket No.</th>
                        <th>Price</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody id="ticketRows">
                    <!-- Existing ticket rows will be added here -->
                </tbody>
            </table>
            
            <form id="ticketForm">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="ticket_id" name="ticket_id" placeholder="Ticket ID">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="ticket_no" name="ticket_no" placeholder="Ticket No">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="price" name="price" placeholder="Price">
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" class="btn btn-success" id="saveTicketBtn">Save</button>
                    </div>
                </div>
            </form>
            
    </div>

        <div id="addedTickets"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
     
        // Function to fetch and display all tickets
        function getAllTickets() {
            $.ajax({
                url: '/tickets', // Route to fetch all tickets
                method: 'GET',
                success: function(response) {
                    // Clear existing ticket list
                    $('#addedTickets').empty();

                    // Create the table structure
                    var table = $('<table class="table table-bordered">').appendTo('#addedTickets');
                    var tbody = $('<tbody>').appendTo(table);

                    // Loop through the tickets and append rows to the table body
                    $.each(response.tickets, function(index, ticket) {
                        var row = $('<tr>');
                        row.append('<td>' + ticket.ticket_id + '</td>');
                        row.append('<td>' + ticket.ticket_no + '</td>');
                        row.append('<td>' + ticket.price + '</td>');
                        var editBtn = $('<button class="btn btn-primary btn-sm editBtn">Edit</button>').data('id', ticket.id);
                        var deleteBtn = $('<button class="btn btn-danger btn-sm deleteBtn">Delete</button>').data('id', ticket.id);
                        var btnTd = $('<td>').append(editBtn).append(deleteBtn);
                        row.append(btnTd);

                        tbody.append(row);
                    });
                },
                error: function(xhr, status, error) {
                    // Handle any errors
                    console.error(error);
                }
            });
        }

        // Call getAllTickets to fetch and display tickets when the document is ready
        getAllTickets();

        // Add event listener to delete button
        $('#addedTickets').on('click', '.deleteBtn', function() {
            var ticketId = $(this).data('id');
            var deleteUrl = '/delete/' + ticketId;

            // Send AJAX request
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": ticketId
                },
                success: function(response) {
                    $(this).closest('tr').remove(); // Remove the deleted row
                    alert('Ticket deleted successfully!');
                    getAllTickets();
                    $('#ticketForm')[0].reset();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error deleting ticket!');
                }
            });
        });

        // Add event listener to save ticket button
        $('#saveTicketBtn').click(function() {
            var formData = $('#ticketForm').serialize();

            $.ajax({
                type: 'POST',
                url: '{{ route("tickets.store") }}',
                data: formData,
                success: function(response) {
                    alert('Ticket Saved Successfully');

                    // Clear form inputs after successful save
                    $('#ticketForm')[0].reset();
                    var newRow = $('<tr>');
                newRow.append('<td>' + response.ticket.ticket_id + '</td>');
                newRow.append('<td>' + response.ticket.ticket_no + '</td>');
                newRow.append('<td>' + response.ticket.price + '</td>');
                var editBtn = $('<button class="btn btn-primary btn-sm editBtn">Edit</button>').data('id', response.ticket.id);
                var deleteBtn = $('<button class="btn btn-danger btn-sm deleteBtn">Delete</button>').data('id', response.ticket.id);
                var btnTd = $('<td>').append(editBtn).append(deleteBtn);
                newRow.append(btnTd);

                $('#addedTickets table tbody').append(newRow);

                // Clear form inputs after successful save
                $('#ticketForm')[0].reset();
                },
                error: function(error) {
                    alert('Error: ' + error.responseJSON.message);
                }
            });
        });

        // Add event listener to add ticket button
        $('#addTicketBtn').click(function() {
            var newRowHtml =
                '<tr id="ticketRow_' + rowCounter + '">' +
                '    <td><input type="text" class="form-control" name="ticket_id_' + rowCounter + '" placeholder="Ticket ID"></td>' +
                '    <td><input type="text" class="form-control" name="ticket_no_' + rowCounter + '" placeholder="Ticket No"></td>' +
                '    <td><input type="text" class="form-control" name="price_' + rowCounter + '" placeholder="Price"></td>' +
                '    <td><button type="button" class="btn btn-sm btn-danger removeTicketBtn" data-rowid="' + rowCounter + '">Remove</button></td>' +
                '</tr>';

            $('#ticketRows').append(newRowHtml);
            rowCounter++;
        });

        // Add event listener to remove ticket button
        $(document).on('click', '.removeTicketBtn', function() {
            var rowId = $(this).data('rowid');
            $('#ticketRow_' + rowId).remove();
        });

        // ganesh code here 
    // Add event listener to edit button
$('#addedTickets').on('click', '.editBtn', function() {
    var row = $(this).closest('tr');
    var editBtn = $(this);
    var ticketId = editBtn.data('id');

    // Convert table data to input fields for editing
    row.find('td').not(':last').each(function() {
        var text = $(this).text();
        var input = $('<input type="text" class="form-control">').val(text);
        $(this).html(input);
    });

    // Replace "Edit" button with "Save" button
    var saveBtn = $('<button class="btn btn-success btn-sm saveBtn">Save</button>').data('id', ticketId);
    editBtn.replaceWith(saveBtn);

    // Replace "Delete" button with empty cell
    row.find('.deleteBtn').hide();
});

// Add event listener to save button after editing
$('#addedTickets').on('click', '.saveBtn', function() {
    var row = $(this).closest('tr');
    var saveBtn = $(this);
    var ticketId = saveBtn.data('id');

    // Get updated values from input fields
    var ticketIdVal = row.find('td:eq(0) input').val();
    var ticketNoVal = row.find('td:eq(1) input').val();
    var priceVal = row.find('td:eq(2) input').val();

    // Send AJAX request to update ticket
    $.ajax({
        url: '/update/' + ticketId,
        type: 'POST', // Assuming you use PUT method for updating
        data: {
            "_token": "{{ csrf_token() }}",
            "ticket_id": ticketIdVal,
            "ticket_no": ticketNoVal,
            "price": priceVal
        },
        success: function(response) {
            // Update table data with new values
            row.find('td:eq(0)').text(ticketIdVal);
            row.find('td:eq(1)').text(ticketNoVal);
            row.find('td:eq(2)').text(priceVal);

            // Replace "Save" button with "Edit" button
            var editBtn = $('<button class="btn btn-primary btn-sm editBtn">Edit</button>').data('id', ticketId);
            saveBtn.replaceWith(editBtn);

            // Restore "Delete" button
            var deleteBtn = $('<button class="btn btn-danger btn-sm deleteBtn">Delete</button>').data('id', ticketId);
            row.find('td:last').empty().append(deleteBtn);

            alert('Ticket updated successfully!');
            $('#ticketForm')[0].reset();
                    getAllTickets();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('Error: ' + error.responseJSON.message);
        }
    });
});


    });
</script>

    
    
    
</body>
</html>
