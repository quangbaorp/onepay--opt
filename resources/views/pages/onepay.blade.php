<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OnePay</title>
    <meta name="csrf" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
    <div class="container">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thanh Toán OnePay</h4>
                </div>
                <div class="card-body">
                    <form action="{{route('onepay')}}" method="post">
                        @csrf
                        <input type="hidden" name="product" value="1">
                        <input type="submit" name="" id="" value="xác nhận mua hàng" class="form-control btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>