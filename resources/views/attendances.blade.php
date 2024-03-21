<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Daily Attendance Print</title>

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
            text-align: center;
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

                        <td class="text-center">
                            <h2 class="text-center text-gray-500">Daily Attendance Sheet</h2>
                            <h3>Hostel-2 Mess (Date : {{now()->format('d-m-Y')}})</h3>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td style="text-align: center;">Sr. No.</td>
            <td style="text-align: center;">Member Name</td>
            <td  style="text-align: center;">Lunch</td>
            <td style="text-align: center;">Dinner</td>
        </tr>
        @foreach($attendances as $attendance)
        <tr class="item">
            <td style="text-align: center;">{{$loop->iteration}}</td>
            <td style="text-align: center;">{{$attendance->member->name}}</td>
            <td style="text-align: center;" >@if($attendance->is_lunch) &#10004; @else &#10060; @endif</td>
            <td style="text-align: center;">@if($attendance->is_dinner) &#10004; @else &#10060; @endif</td>
        </tr>
        @endforeach


    </table>
</div>
</body>
</html>

<script type="text/javascript">
    window.onload = function () {
        window.print();
    }
</script>
