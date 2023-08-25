<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Orderview</title>
</head>
<body>
    <div class="container">
        <div class="row gx-0">
            <div class="col-xs-12">
                <div class="invoice-title"><strong>Invoice</strong><br><br><strong>Order
                        No:{{ $data[0]['orderGroupId'] }}
                    </strong></div>
                <hr>
                <div class="row gx-0">
                    <div class="col-12">
                        <address><strong>&nbsp;</strong></address>
                        <table style="width: 100%; border-collapse: collapse; ">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; ">
                                        <div>
                                            <address><strong>Billed To:<br></strong>
                                                {{ $data[0]['ShippingAddress'] }}
                                            </address>
                                        </div>

                                    </td>
                                    <td style="width: 50%;  text-align: right;"><strong>Shipped
                                            To:<br></strong>ReactShopping sitehinjewadi middle eastpune-411057<br></td>
                                </tr>
                                <tr>
                                    <td style="width: 50%; "><strong>Payment Method:<br></strong>
                                        {{ $data[0]['paymentType'] }}
                                    </td>
                                    <td style="width: 50%;  text-align: right;"><strong>Order
                                            Date: {{ $data[0]['created_at'] }}</strong><br><br></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>e-mail : reactecommerce213@email.com<br>
                    </div>
                </div>
            </div>
            <div class="row gx-0">
                <div class="col-6 justify-content-end text-right mt-2">
                    <div class="row justify-content-end text-end"></div>
                </div>
            </div>
            <div class="row gx-0">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Purchase Summary</strong></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row justify-content-center">
                                <div class="table-responsive">
                                    <table class="table table-condensed">
                                        <thead>
                                            <tr>
                                                <td style="text-align: center;"><strong>Product Name</strong></td>
                                                <td class="text-center" style="text-align: center;">
                                                    <strong>Amount</strong>
                                                </td>
                                                <td class="text-center" style="text-align: center;">
                                                    <strong>Discount</strong>
                                                </td>
                                                <td class="text-center" style="text-align: center;">
                                                    <strong>Quantity</strong>
                                                </td>
                                                <td class="text-center" style="text-align: center;">
                                                    <strong>Total</strong>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $datas)
                                                <tr>
                                                    <td style="text-align: center;">{{ $datas['title'] }}</td>
                                                    <td class="text-center" style="text-align: center;">
                                                        ${{ $datas['price'] }}</td>
                                                    <td class="text-center text-success" style="text-align: center;">
                                                        {{ $datas['discountPercentage'] }}%</td>
                                                    <td class="text-center" style="text-align: center;"><span
                                                            class="fw-bolder me-2">x{{ $datas['quantity'] }}</span></td>
                                                    <td class="text-center fw-bolder" style="text-align: center;">
                                                        {{ $datas['TotalAmount'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3" class="border-0" style="text-align: center;"><br>
                                                </td>
                                                <td class="text-center" style="text-align: center;">Shipping charge:
                                                </td>
                                                <td class="text-center" style="text-align: center;">+30</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="border-0" style="text-align: center;"><br>
                                                </td>
                                                <td class="text-center" style="text-align: center;">S-GST:</td>
                                                <td class="text-center" style="text-align: center;">+17.5</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="border-0" style="text-align: center;"><br>
                                                </td>
                                                <td class="text-center" style="text-align: center;">G-GST:</td>
                                                <td class="text-center" style="text-align: center;">+17.5</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="border-0" style="text-align: center;"><br>
                                                </td>
                                                <td class="text-center" style="text-align: center;">coupon:</td>
                                                <td class="text-center" style="text-align: center;">
                                                    -{{ $data[0]['coupon'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="border-0" style="text-align: center;"><br>
                                                </td>
                                                <td class="text-center fw-bolder" style="text-align: center;">Total :
                                                </td>
                                                <td class="text-center fw-bolder" style="text-align: center;">
                                                    {{ $total + 30 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
