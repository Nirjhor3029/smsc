<!DOCTYPE html>
<html>
<head>
    <title>ekShop Promotional SMS, Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <script language="JavaScript" src="https://code.jquery.com/jquery-1.12.4.js" type="text/javascript"></script>
    <script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"
            type="text/javascript"></script>
    <script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"
            type="text/javascript"></script>
    <script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"
            type="text/javascript"></script>
    <script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"
            type="text/javascript"></script>

    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css"/>


</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <br><br>
            <h3 class="text-danger text-center">EKSHOP promotional sms sending panel</h3>
            <br><br>
            <div style="float: right">
                <a href="{{route('promotion.send.form')}}">
                    <button type="button" class="btn btn btn-primary">Send</button>
                </a>

            </div>
            <br><br>
            @if(session()->has('message'))
                <div class="alert {{session('alert') ?? 'alert-info'}}">
                    {{ session('message') }}
                </div>
            @endif
        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            <table class="table table-responsive table-striped table-bordered dt-responsive data-table" cellspacing="0"
                   width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>number</th>
                    <th>Dlr</th>
                    <th>Timestamp</th>
                </tr>
                </thead>

            </table>

        </div>
    </div>
</div>

</body>

<script type="text/javascript">
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('promotion.list') }}",

            columns: [
                {data: 'id', name: 'id'},
                {data: 'receiver_number', name: 'receiver_number'},
                {data: 'is_dlr_received', name: 'is_dlr_received'},
                {data: 'created_at', name: 'created_at'},


                // {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            // createdRow: function ( row, data, index ) {
            //     if ( data['is_dlr_received'] == '1' ) {
            //         $('td', row).eq(2).text('Delivered');
            //     } else {
            //         $('td', row).eq(2).text('Sent');
            //     }
            // },
        })

    });
</script>

</html>
