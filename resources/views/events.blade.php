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
        <form id="eventForm" action="{{ route('event.store') }}" method="post">
            @csrf
            <input type="hidden" id="ticketDataInput" name="ticketData" value="">
            <div class="form-group">
                <label for="eventname">Event Name</label>
                <input type="text" class="form-control" id="eventname" name="eventname" />
            </div>
            <div class="form-group">
                <label for="eventdesc">Event Description</label>
                <textarea class="form-control" id="eventdesc" name="eventdesc"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="startdate">Start Date</label>
                    <input type="date" class="form-control" id="startdate" name="startdate" />
                </div>
                <div class="form-group col-md-6">
                    <label for="enddate">End Date</label>
                    <input type="date" class="form-control" id="enddate" name="enddate" />
                </div>
            </div>
            <div class="form-group">
                <label for="organiser">Organiser</label>
                <input type="text" class="form-control" id="organiser" name="organiser" />
            </div>
            <div class="form-group">
                <label for="tickets">Tickets</label>
                <button type="button" class="btn btn-sm btn-primary" id="addTicketBtn">Add New Ticket</button>
            </div>

            <table class="table table-bordered" id="ticketTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ticket No.</th>
                        <th>Price</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
            </table>
            <div id="ticketFormContainer" style="display: none;">
                <form id="ticketForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" id="ticket_id" name="ticket_id"
                                placeholder="Ticket ID" />
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="ticket_no" name="ticket_no"
                                placeholder="Ticket No" />
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="price" name="price"
                                placeholder="Price" />
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" class="btn btn-success" id="saveTicketBtn">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="addedTickets"></div>

            <center><button type="submit" id="eventsave" class="btn btn-group-vertical">Save Event</button></center>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
            document.getElementById('addTicketBtn').addEventListener('click', function () {
            document.getElementById('ticketFormContainer').style.display = 'block';
        });
            $('#eventsave').click(function(event) {
               
                event.preventDefault(); 
                var eventname = $('#eventname').val();
                var eventdesc = $('#eventdesc').val();
                var startdate = $('#startdate').val();
                var enddate = $('#enddate').val();
                var organiser = $('#organiser').val();
                var ticketData = $('#ticketDataInput').val();

                if (eventname === '' || eventdesc === '' || startdate === '' || enddate === '' ||
                    organiser === '') {
                    alert('Please fill in all required fields.');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '{{ route('event.store') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "name": eventname,
                        "desc": eventdesc,
                        "sdate": startdate,
                        "edate": enddate,
                        "org": organiser,
                        "ticketdata": ticketData
                    },
                    success: function(response) {
                        alert('Event saved successfully');
                        
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error saving event');
                    }
                });
            });
        });


        $(document).ready(function() {
            var rowCounter = 1;

            
            function getAllTickets() {
                $.ajax({
                    url: '/tickets', 
                    method: 'GET',
                    success: function(response) {
                        console.log('getAllTickets function called');
                        $('#addedTickets').empty();

                       
                        var table = $('<table class="table table-bordered">').appendTo('#addedTickets');
                        var tbody = $('<tbody>').appendTo(table);

                        
                        $.each(response.tickets, function(index, ticket) {
                            var row = $('<tr>');
                            row.append('<td>' + ticket.ticket_id + '</td>');
                            row.append('<td>' + ticket.ticket_no + '</td>');
                            row.append('<td>' + ticket.price + '</td>');
                            var editBtn = $(
                                '<button class="btn btn-primary btn-sm editBtn">Edit</button>'
                                ).data('id', ticket.id);
                            var deleteBtn = $(
                                '<button class="btn btn-danger btn-sm deleteBtn">Delete</button>'
                                ).data('id', ticket.id);
                            var btnTd = $('<td>').append(editBtn).append(deleteBtn);
                            row.append(btnTd);

                            tbody.append(row);
                        });
                        var ticketData = response.tickets.map(function(ticket) {
                            return {
                                ticket_id: ticket.ticket_id,
                                ticket_no: ticket.ticket_no,
                                price: ticket.price
                            };
                        });

                        
                        $('#ticketDataInput').val(JSON.stringify(ticketData));
                    },
                    error: function(xhr, status, error) {
                      
                        console.error(error);
                    }
                });
            }

            
            getAllTickets();

            
            $('#addedTickets').on('click', '.deleteBtn', function() {
                var ticketId = $(this).data('id');
                var deleteUrl = '/delete/' + ticketId;

               
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": ticketId
                    },
                    success: function(response) {
                        $(this).closest('tr').remove();
                        alert('Ticket deleted successfully!');
                        
                        getAllTickets();
                        
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error deleting ticket!');
                    }
                });
            });

           
            $('#saveTicketBtn').click(function() {
               var ticket_id = $('#ticket_id').val();
               var ticket_no = $('#ticket_no').val();
               var price = $('#price').val();

                $.ajax({
                    type: 'POST',
                    url: '{{ route('tickets.store') }}',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "ticket_id": ticket_id,
                    "ticket_no": ticket_no,
                    "price": price,
                    },
                    success: function(response) {
                        alert('Ticket Saved Successfully');

                        
                        getAllTickets();
                        $('#ticket_id').val('');
                $('#ticket_no').val('');
                $('#price').val('');

                // Hide the ticket form container
                $('#ticketFormContainer').hide();
                    },
                    error: function(error) {
                        alert('Error: ' + error.responseJSON.message);
                    }
                }); 
                document.getElementById('ticketFormContainer').style.display = 'none';

            });


            $('#addedTickets').on('click', '.editBtn', function() {
                var row = $(this).closest('tr');
                var editBtn = $(this);
                var ticketId = editBtn.data('id');

                
                row.find('td').not(':last').each(function() {
                    var text = $(this).text();
                    var input = $('<input type="text" class="form-control">').val(text);
                    $(this).html(input);
                });

                
                var saveBtn = $('<button class="btn btn-success btn-sm saveBtn">Save</button>').data('id',
                    ticketId);
                editBtn.replaceWith(saveBtn);

               
                row.find('.deleteBtn').hide();
            });

           
            $('#addedTickets').on('click', '.saveBtn', function() {
                var row = $(this).closest('tr');
                var saveBtn = $(this);
                var ticketId = saveBtn.data('id');

               
                var ticketIdVal = row.find('td:eq(0) input').val();
                var ticketNoVal = row.find('td:eq(1) input').val();
                var priceVal = row.find('td:eq(2) input').val();

                
                $.ajax({
                    url: '/update/' + ticketId,
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "ticket_id": ticketIdVal,
                        "ticket_no": ticketNoVal,
                        "price": priceVal
                    },
                    success: function(response) {
                        
                        row.find('td:eq(0)').text(ticketIdVal);
                        row.find('td:eq(1)').text(ticketNoVal);
                        row.find('td:eq(2)').text(priceVal);

                        
                        var editBtn = $(
                                '<button class="btn btn-primary btn-sm editBtn">Edit</button>')
                            .data('id', ticketId);
                        saveBtn.replaceWith(editBtn);

                       
                        var deleteBtn = $(
                            '<button class="btn btn-danger btn-sm deleteBtn">Delete</button>'
                            ).data('id', ticketId);
                        row.find('td:last').empty().append(deleteBtn);

                        alert('Ticket updated successfully!');
                        
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
