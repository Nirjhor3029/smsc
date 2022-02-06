<!DOCTYPE html>
<html>
<head>
    <title>Bulk sms send</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script language="JavaScript" src="https://code.jquery.com/jquery-1.12.4.js" type="text/javascript"></script>
    <!------ Include the above in your HEAD tag ---------->

    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>

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
    <section style="text-align: center; font-weight: bold;" >
        <h2>EKSHOP BULK SMS</h2>
    </section>
    @if (session()->has('success'))
        <div class="alert alert-info text-center">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-5">
            <div class="pt-2">
                <br><br><br><br>
                <form action="{{route('promotion.bulk.send')}}" method="post">
                    <div class="form-group">

                        <div>
                            <label for="campaign_name">Campaign Name <a style="color: red">*</a></label>
                            <input name="campaign_name" class="form-control" id="campaign_name" value="{{ old('campaign_name') }}" required />
                        </div>
                        <br>


                        <div>
                            <label for="numbers">Mobile Numbers <a style="color: red">*</a></label>
                            <textarea  placeholder="Separate numbers by comma (,)&#10;&#10;Example:&#10;01711987654,&#10;01756453219,&#10;01517654389" name="numbers" class="form-control" id="numbers" rows="10" required >{{ old('numbers') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label for="smsText">Message <a style="color: red">*</a></label>
                        <textarea maxlength="500" name="smsText" class="form-control" id="smsText" rows="5" required>{{ old('smsText') }}</textarea>
                        <div id="the-count">
                            <span id="current">0</span>
                            <span id="maximum">/ 500</span>
                            <span id="msg_count"></span>
                        </div>
                    </div>
                    <br>

                    <div>
                        <label for="secret_code">Secret code <a style="color: #ff0000">*</a></label>
                        <input value="{{ old('secret_code') }}" name="secret_code" class="form-control" id="secret_code" required />
                    </div>
                    <br>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="client" value="ekShop_bulk" />

                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>

        </div>

        <div class="col-md-7">
            <div class="pt-5">
                <br><br><br><br>
                <table class="table table-responsive table-striped table-bordered dt-responsive data-table" cellspacing="0"
                       width="100%">
                    <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Create time</th>
                        <th>body</th>
                        <th>SMS</th>
                        <th>Errors</th>
                        <th>Client</th>
                    </tr>
                    </thead>

                </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('bulk.promotion.list') }}",

            columns: [
                {data: 'campaign_name', name: 'campaign_name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'body', name: 'body'},
                {data: 'sms', name: 'sms'},
                {data: 'error_numbers', name: 'error_numbers'},
                {data: 'client', name: 'client'},

            ],

        })

    });

</script>

<script>
    $('textarea').keyup(function() {

        var msg_count = $('#smsText').val();
        msg_count_result = $('#msg_count');

        $.ajax({
            type:'GET',
            url: '/ajax-check-msg-length',
            data: {msg_count: msg_count},
            success: function(data){
                var data_ready = '( ' + data + ' ' + 'SMS )';
                msg_count_result.text(data_ready);
            }

        });


    
    var characterCount = $(this).val().length,
        current = $('#current'),
        maximum = $('#maximum'),
        theCount = $('#the-count');
      
    current.text(characterCount);

   

    if (characterCount < 69) {
      current.css('color', '#0AAA1D');
    }
    if (characterCount > 70 && characterCount < 139) {
      current.css('color', '#8C9F00');
    }
    if (characterCount > 140 && characterCount < 209) {
      current.css('color', '#793535');
    }
    if (characterCount > 210 && characterCount < 279) {
      current.css('color', '#841c1c');
    }
    if (characterCount > 280 && characterCount < 400) {
      current.css('color', '#8f0001');
    }
    
    if (characterCount >= 500) {
      maximum.css('color', '#8f0001');
      current.css('color', '#8f0001');
      theCount.css('font-weight','bold');
    } 
    
    else {
      maximum.css('color','#666');
      theCount.css('font-weight','normal');
    }
       
  });
</script>


</body>
</html>
