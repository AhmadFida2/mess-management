<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Mess Bill Print</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }



        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: large;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table>
        <tr class="top">
            <td colspan="12">
                <table>
                    <tr>

                        <td class="text-center ">
                            <h2 class="text-center text-gray-500">Mess Bill Print</h2>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="5">
                <table>
                    <tr>
                        <td>
                            <b>Member Name </b>: {{$bill->member->name}}<br />
                            <b>Mobile No </b>: {{$bill->member->mobile}} <br />
                            <b>Email ID </b>: {{$bill->member->email}}
                        </td>
                        <td></td>
                        <td></td> <td></td>
                        <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>



                        <td>
                            <b>Bill Month</b> : {{$date->format('F Y')}}<br />
                            <b>Due Date </b> : {{$date->startOfMonth()->addDays(9)->format('d-M-Y')}}<br />
                            <b>Print Date</b> : {{now()->format('d-M-Y H:i:s A')}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr class="heading">
            <td colspan="5" >Attendance Details</td>
        </tr>
        <tr class="heading">
            <td style="text-align: center;">Date</td>
            <td  style="text-align: center;">Meal</td>
            <td style="text-align: center;">Units</td>
            <td colspan="2" style="text-align: center;">Amount</td>
        </tr>
        @foreach($attendances as $attendance)
        <tr class="item">
            <td style="text-align: center;">{{\Carbon\Carbon::parse($attendance->date)->format('d-M-Y')}}</td>
            <td style="text-align: center;" >{{$attendance->meal}}</td>
            <td style="text-align: center;">{{$attendance->units}}</td>
            <td colspan="2" style="text-align: center;">{{$attendance->units * $unit_cost}}</td>
        </tr>
        @endforeach

        <tr class="total">
            <td colspan="3"></td>

            <td colspan="2">Grand Total: Rs. {{$bill->units * $unit_cost}} /-</td>
        </tr>
        <br><br>
        <tr class="heading">
            <td colspan="5" >Payment Details</td>
        </tr>
        <tr class="heading">
            <td style="text-align: center;">Units</td>
            <td style="text-align: center;">Amount</td>
            <td style="text-align: center;">Paid Amount</td>
            <td style="text-align: center;">Remaining Amount</td>
            <td style="text-align: center;">Bill Status</td>
        </tr>

        <tr class="details">
            <td style="text-align: center;">{{$bill->units}}</td>
            <td style="text-align: center;">Rs. {{$bill->amount}}/-</td>
            <td style="text-align: center;">Rs. {{$paid_amount}}/-</td>
            <td style="text-align: center;">Rs. {{$bill->amount -$paid_amount }}/-</td>
            <td style="text-align: center;font-weight: bolder;">{{$status[$bill->status]}}</td>
        </tr>

    </table>
</div>
</body>
</html>

<script type="text/javascript">
    window.onload = function () {
        window.print();
    }
</script>
