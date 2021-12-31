<?php

namespace App\Http\Controllers;

use Exception;
use NumberFormatter;
use DateTime;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Quote;


class QuoteController extends Controller
{

    /**
     * Create a new instance
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Stores and returns a quote
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Create a validator with the input data
        $validator = Validator::make($request->all(), [
            'age' => 'required|string',
            'currency_id' => 'required|string',
            'start_date' => 'required|date|date_format:Y-m-d|after:today',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date'
        ]);

        // If the inputted data fails, return the validator errors
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Format age(s) into an array
        $ages = explode(",", $request->get('age'));

        /*
         * Currency formatter check, used to see if the currency code pushed to the validator is valid.
         * NOTE: This can only happen if a user is messing with the form input
         */
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, $request->get('currency_id'));

        // If we are unable to format with the inputted currency_id, return an error
        if (!$fmt->formatCurrency(100, $request->get('currency_id'))) {
            return response()->json(['error' => 'We were unable to provide a quote with the inputted currency type.'], 400);
        }

        // To & from dates & days of travel
        $from = new DateTime($request->get('start_date'));
        $from->format(DateTime::ISO8601);

        $to = new DateTime($request->get('end_date'));
        $to->format(DateTime::ISO8601);

        $interval = $from->diff($to);
        $days = $interval->format('%a');

        // Create the total amount;
        $total = 0;

        // Loop through the ages, ensuring all are between 18 & 70, grab the appropriate load and add to the total with the days provided above.
        foreach($ages as $age) {
            if ($age < 18 || $age > 70) {
                return response()->json(['error' => 'We were unable to provide a quote with the age(s) provided.'], 400);
            }

            $load = 1.0;
            switch(true) {
                case ($age >= 18 && $age <= 30):
                    $load = 0.6;
                    break;
                case ($age >= 31 && $age <= 40):
                    $load = 0.7;
                    break;
                case ($age >= 41 && $age <= 50):
                    $load = 0.8;
                    break;
                case ($age >= 51 && $age <= 60):
                    $load = 0.9;
                    break;
                case ($age >= 61 && $age <= 70):
                    $load = 1.0;
                    break;
            }

            $total += 3 * $load * $days;
        }

        // Create an entry in the quotes table
        $quote = Quote::create([
            'user_id' => auth()->user()->id,
            'age' => implode(",", $ages),
            'currency_id' => $request->get('currency_id'),
            'total' => number_format($total, 2),
            'start_date' => $from,
            'end_date' => $to,
        ]);

        return response()->json([
            'total' => number_format($total, 2),
            'currency_id' => $quote->currency_id,
            'quotation_id' => $quote->id
        ]);
    }

    public function create() {
        return view('quote');
    }
}
