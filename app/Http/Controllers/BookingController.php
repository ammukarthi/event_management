<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;
use App\Services\PaymentService;
use App\Traits\MailTrait;

class BookingController extends Controller
{
    use MailTrait;

    public function bookingHistory(Request $request){

        $user = $request->user();

        $bookings = Booking::with('payment')->where('user_id',$user->id)->get();

        return response()->json([
            'status' => 'success',
            'bookings'=> $bookings
        ],200);
    }

    public function bookTicket(Request $request,$ticket_id){

        $validate = Validator::make($request->all(),[
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        if($validate->fails()){
            return response()->json(['status' => 'error','message' => $validate->errors()->first()],400);
        }

        $ticket = Ticket::withSum([
                        'bookings as booked_quantity' => function ($q) {
                            $q->whereIn('status', ['confirmed', 'pending']);
                        }
                    ], 'quantity')->find($ticket_id);

        $available_qty = $ticket->quantity - $ticket->booked_quantity; 

        if($request->quantity > $available_qty){

            return response()->json(['status' => 'error','available_qty' => $available_qty,'message' => 'Ticket quantity is not valid'],400);
        }

        $amount = $request->quantity * $ticket->price;

        $booking = new Booking();
        $booking->user_id = $request->user()->id;
        $booking->ticket_id = $ticket_id;
        $booking->quantity = $request->quantity;
        $booking->status = 'pending';
        $booking->save();

        $bookingId = $booking->id;

         return response()->json([
            'booking_id' => $bookingId,
            'amount' => $amount,
            'status' => 'success',
            'message'=>'Booking done Please Proceed to Payment'
        ],200);
    }

    public function cancel(Request $request,$booking_id){

        $booking = Booking::findorFail($booking_id);

        if($booking->status == "cancelled"){
            return response()->json([
            'status' => 'error',
            'message'=>'Booking is already Cancelled'
            ],400);
        }

        if($booking->status != "confirmed"){
            return response()->json([
            'status' => 'error',
            'message'=>'Booking is Not Confirmed to Cancel'
            ],400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        $payment = Payment::where('booking_id',$booking_id)->first();

        if($payment && $payment->status == "success"){

            $payment->status = 'refunded';
            $payment->save();

        }

        $this->sendMail(
            $request->user()->email,
            'Booking Cancelled',
            'Your booking is cancelled.'
        );

        return response()->json([
            'status' => 'success',
            'message'=>'Booking Cancelled successfully'
        ],200);
    }

    public function paymentProcess(Request $request,$booking_id,PaymentService $paymentService){

        $booking = Booking::with(['ticket','payment'])->findorFail($booking_id);

        if($booking->payment && ($booking->payment == "sucess" || "refunded")){

            return response()->json([
                'status' => 'error',
                'message'=>'Booking Payment Already Completed or the booking is cancelled'
            ],500);
        }

        $amount = $booking->quantity * $booking->ticket->price;

        $payment = $paymentService->process($amount);

        if($booking->payment){

            $ps = $booking->payment;

        }else{

            $ps = new Payment();
            $ps->booking_id = $booking_id;
            $ps->amount = $amount;
        }

        if (!$payment['status']) {

            $ps->status = 'failed';
            $ps->save();

            return response()->json([
                'status' => 'error',
                'message' => $payment['message']
            ], 400);
        }

        // Save booking if payment successful

        $ps->status = 'success';
        $ps->save();

        $booking->status = "confirmed";
        $booking->save();

        $this->sendMail(
            $request->user()->email,
            'Booking Confirmed',
            'Your booking is successful.'
        );

        return response()->json([
            'status' => 'success',
            'transaction_id' => $payment['transaction_id'],
            'message' => 'Booking confirmed'
        ],200);

    }

    public function paymentDetail($id){

        $payment = Payment::with('booking')->find($id);

         return response()->json([
            'status' => 'success',
            'payment' => $payment
        ],200);

    }
}
