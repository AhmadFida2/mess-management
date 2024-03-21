<x-mail::message>
# Dear {{$name}},

New bill for your Mess has been generated. The bill details are given below. <br>

|Bill Month                                                     |Units Consumed|Bill Amount|Due Date |
|:-------------------------------------------------------------:|:------------:|:---------:|:-------:|
|{{\Carbon\Carbon::parse($date)->startOfMonth()->format('F Y')}}|{{$units}}    |{{$amount}}|{{$date}}|
   


<p><i>Kindly pay your bill before due-date.</i></p><br>
Thanks,<br>
Mess Committee
</x-mail::message>

