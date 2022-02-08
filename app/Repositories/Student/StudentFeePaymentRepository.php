<?php
namespace App\Repositories\Student;

use App\Models\Student\BilldeskPayment;
use Razorpay\Api\Api as RazorpayApi;
use Stripe\Error\Api as StripeApi;
use Stripe\Error\Card;
use Stripe\Error\RateLimit;
use Stripe\Error\ApiConnection;
use Stripe\Error\Authentication;
use Stripe\Error\InvalidRequest;
use App\Models\Student\StudentRecord;
use App\Models\Student\StudentFeeRecord;
use Illuminate\Validation\ValidationException;
use App\Repositories\Student\StudentRecordRepository;

class StudentFeePaymentRepository
{
	protected $student_record;
	protected $student_record_repo;
	protected $student_fee_record;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
	public function __construct(
		StudentRecord $student_record,
		StudentRecordRepository $student_record_repo,
		StudentFeeRecord $student_fee_record
	) {
		$this->student_record = $student_record;
		$this->student_record_repo = $student_record_repo;
		$this->student_fee_record = $student_fee_record;
	}

    /**
     * Complete Razorpay payment
     *
     * @param array $params
     * @return null
     */
    public function razorpayPayment(StudentRecord $student_record, $params)
    {
        $transaction_id     = gv($params, 'transaction_id');
        $installments       = gv($params, 'installments',[]);
        $fee_installment_id = gv($params, 'fee_installment_id');

        $api = new RazorpayApi(config('config.razorpay_key'), config('config.razorpay_secret'));
        $payment = $api->payment->fetch($transaction_id);
        $payment = $payment->toArray();

        $notes = gv($payment, 'notes', []);

        if (! $payment) {
        	throw ValidationException::withMessages(['message' => trans('general.missing_parameter')]);
        }

        $student_record_id  = gv($notes, 'student_record_id');
        $fee                = gv($notes, 'fee');
        $handling_fee       = gv($notes, 'handling_fee', 0);

        if ($student_record->id != $student_record_id) {
        	throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $amount = gv($payment, 'amount', 0);
        $amount = $amount / 100;

        if ($fee + $handling_fee != $amount) {
            throw ValidationException::withMessages(['message' => trans('finance.total_mismatch')]);
        }

        $calculated_handling_fee = getPaymentGatewayHandlingFee('razorpay', $fee);

        if ($calculated_handling_fee != $handling_fee) {
            throw ValidationException::withMessages(['message' => trans('finance.handling_fee_mismatch')]);
        }

        $params['date'] = date('Y-m-d');
        $params['amount'] = $amount - $handling_fee;
        $params['handling_fee'] = $handling_fee;
        $params['is_online_payment'] = 1;
        $params['gateway'] = 'razorpay';
        $params['source'] = 'Razorpay';
        $params['gateway_token'] = $transaction_id;
        $params['reference_number'] = strtoupper(randomString(20));
        $params['installment_id'] = $fee_installment_id;
        $params['installments'] = $installments;

        $this->student_record_repo->makePayment($student_record, $params);
    }

    /**
     * Complete Paystack payment
     *
     * @param array $params
     * @return null
     */
    public function paystackPayment(StudentRecord $student_record, $params)
    {
        $transaction_id     = gv($params, 'transaction_id');
        $installments       = gv($params, 'installments',[]);
        $fee_installment_id = gv($params, 'fee_installment_id');

        $client = new \GuzzleHttp\Client();
        $request = $client->get('https://api.paystack.co/transaction/verify/'.$transaction_id, [
            'headers' => [
                'Authorization' =>'Bearer '.config('config.paystack_secret_key')
            ]
        ]);
        $response = json_decode($request->getBody(), true);

        $status = gv($response, 'status');

        if (! $status) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $data = gv($response, 'data', []);
        $metadata = gv($data, 'metadata', []);
        $custom_fields = gv($metadata, 'custom_fields', []);
        $student_record_id_array = searchByKey($custom_fields, 'variable_name', 'student_record_id');
        $student_record_id = gv($student_record_id_array, 'value');

        $fee_array = searchByKey($custom_fields, 'variable_name', 'fee');
        $fee = gv($fee_array, 'value');

        $handling_fee_array = searchByKey($custom_fields, 'variable_name', 'handling_fee');
        $handling_fee = gv($handling_fee_array, 'value');

        if ($student_record->id != $student_record_id) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $amount = gv($data, 'amount', 0);
        $amount = $amount / 100;

        if ($fee + $handling_fee != $amount) {
            throw ValidationException::withMessages(['message' => trans('finance.total_mismatch')]);
        }

        $calculated_handling_fee = getPaymentGatewayHandlingFee('paystack', $fee);

        if ($calculated_handling_fee != $handling_fee) {
            throw ValidationException::withMessages(['message' => trans('finance.handling_fee_mismatch')]);
        }

        $params['date'] = date('Y-m-d');
        $params['amount'] = $amount - $handling_fee;
        $params['handling_fee'] = $handling_fee;
        $params['is_online_payment'] = 1;
        $params['gateway'] = 'paystack';
        $params['source'] = 'Paystack';
        $params['gateway_token'] = $transaction_id;
        $params['reference_number'] = strtoupper(randomString(20));
        $params['installment_id'] = $fee_installment_id;
        $params['installments'] = $installments;

        $this->student_record_repo->makePayment($student_record, $params);
    }

    /**
     * Complete Stripe payment
     *
     * @param array $params
     * @return null
     */
    public function stripePayment(StudentRecord $student_record, $params)
    {
        $stripeToken        = gv($params, 'stripeToken');
        $fee_installment_id = gv($params, 'fee_installment_id');
        $installments       = gv($params, 'installments',[]);
        $amount             = gv($params, 'amount', 0);
        $fee                = gv($params, 'fee');
        $handling_fee       = gv($params, 'handling_fee', 0);
        $currency           = getDefaultCurrency()['name'];

        if (! $amount) {
            throw ValidationException::withMessages(['message' => trans('finance.cannot_process_if_amount_is_zero')]);
        }

        if ($fee + $handling_fee != ($amount / 100)) {
            throw ValidationException::withMessages(['message' => trans('finance.total_mismatch')]);
        }

        $calculated_handling_fee = getPaymentGatewayHandlingFee('stripe', $fee);

        if ($calculated_handling_fee != $handling_fee) {
            throw ValidationException::withMessages(['message' => trans('finance.handling_fee_mismatch')]);
        }

        \Stripe\Stripe::setApiKey(config('config.stripe_private_key'));
        try {
            $charge = \Stripe\Charge::create([
                'amount'   => $amount,
                'currency' => $currency,
                'source'   => $stripeToken
            ]);
        } catch (Card $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
        catch (StripeApi $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
        catch (InvalidRequest $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
        catch (RateLimit $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
        catch (ApiConnection $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
        catch (Authentication $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }

        $amount = $amount / 100;

        $params['date'] = date('Y-m-d');
        $params['amount'] = $amount - $handling_fee;
        $params['handling_fee'] = $handling_fee;
        $params['is_online_payment'] = 1;
        $params['gateway'] = 'stripe';
        $params['source'] = 'Stripe';
        $params['gateway_token'] = $charge->id;
        $params['reference_number'] = strtoupper(randomString(20));
        $params['installment_id'] = $fee_installment_id;
        $params['installments'] = $installments;

        $this->student_record_repo->makePayment($student_record, $params);
    }

    /**
     * Validate Paypal payment
     *
     * @param array $params
     * @return null
     */
    public function validatePaypalPayment(StudentRecord $student_record, $params)
    {
        $amount             = gv($params, 'amount', 0);
        $fee                = gv($params, 'fee');
        $handling_fee       = gv($params, 'handling_fee', 0);

        if ($fee + $handling_fee != $amount) {
            throw ValidationException::withMessages(['message' => trans('finance.total_mismatch')]);
        }

        if (! $amount) {
            throw ValidationException::withMessages(['message' => trans('finance.cannot_process_if_amount_is_zero')]);
        }

        $calculated_handling_fee = getPaymentGatewayHandlingFee('paypal', $fee);

        if ($calculated_handling_fee != $handling_fee) {
            throw ValidationException::withMessages(['message' => trans('finance.handling_fee_mismatch')]);
        }
    }

    /**
     * Complete Paypal payment
     *
     * @param array $params
     * @return null
     */
    public function paypalPayment(StudentRecord $student_record, $params)
    {
        $fee_installment_id = gv($params, 'fee_installment_id');
        $installments       = gv($params, 'installments', []);
        $amount             = gv($params, 'amount', 0);
        $fee                = gv($params, 'fee');
        $handling_fee       = gv($params, 'handling_fee', 0);
        $currency           = getDefaultCurrency()['name'];

        $params['date'] = date('Y-m-d');
        $params['amount'] = $amount - $handling_fee;
        $params['handling_fee'] = $handling_fee;
        $params['is_online_payment'] = 1;
        $params['gateway'] = 'paypal';
        $params['source'] = 'Paypal';
        $params['reference_number'] = strtoupper(randomString(20));
        $params['installment_id'] = $fee_installment_id;
        $params['installments'] = $installments;

        $this->student_record_repo->makePayment($student_record, $params);
    }

    public function billdeskPayment(StudentRecord $student_record, $params)
    {
        $amount = is_numeric(request('amount')) ? request('amount') : 0;

        if (! $amount) {
            throw ValidationException::withMessages(['message' => trans('validation.min.numeric', ['attribute' => trans('finance.amount'), 'min' => 1])]);
        }

        $handling_fee = 0;
        if (config('config.billdesk_charge_handling_fee')) {
            if (config('config.billdesk_fixed_handling_fee')) {
                $handling_fee = currency(config('config.billdesk_handling_fee'));
            } else {
                $handling_fee = currency($amount * (config('config.billdesk_handling_fee') / 100));
            }
        }

        $total = $amount + $handling_fee;

        $part1 = '|NA|' . $total . '|NA|NA|NA|INR|DIRECT|R|';
        $part2 = '|NA|NA|F|' . config('config.billdesk_email') . '|' . config('config.billdesk_phone') . '|NA|NA|NA|NA|NA|NA';
        
        $reference_number = date('ymd') . strtoupper(randomString(20));

        $str = config('config.billdesk_merchant_id').'|'.$reference_number.$part1.config('config.billdesk_security_id').$part2;

        $checksum = hash_hmac('sha256',$str,config('config.billdesk_checksum_key'), false); 

        $msg = $str.'|'.strtoupper($checksum);

        BilldeskPayment::forceCreate([
            'reference_number'  => $reference_number,
            'student_uuid'      => $student_record->student->uuid,
            'student_record_id' => $student_record->id,
            'date'              => today(),
            'total'             => $total,
            'amount'            => $amount,
            'handling_fee'      => $handling_fee,
            'installment_id'    => request('installment_id'),
            'user_id'           => \Auth::user()->id,
            'options'           => array(
                'installments' => request('installments'),
                'msg' => $msg
            )
        ]);

        return array(
            'msg' => $msg, 
            'key' => $reference_number, 
            'url' => url('/billdesk/status')
        );
    }

    public function billdeskStatus()
    {
        $data = request('msg');

        $msg = explode('|', $data);
        $checksum_value = array_pop($msg);
        $reference_number = gv($msg, 1);
    
        $checksum = hash_hmac('sha256',implode('|', $msg), config('config.billdesk_checksum_key') , false); 

        if ($checksum_value != strtoupper($checksum)) {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('general.invalid_action'));
        }

        $status = gv($msg, 14);

        if ($status !== "0300") {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('finance.payment_failed'));
        }

        $payment = BilldeskPayment::whereReferenceNumber($reference_number)->whereStatus(0)->first();

        if (! $payment) {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('general.invalid_link'));
        }

        $student_record = $this->student_record->whereId($payment->student_record_id)->whereHas('student', function($q) use ($payment) {
            $q->whereUuid($payment->student_uuid);
        })->first();

        if (! $student_record) {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('general.invalid_link'), 'payment' => $payment);
        }

        if ($payment->total != gv($msg, 4)) {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('finance.total_mismatch'), 'payment' => $payment);
        }

        $calculated_handling_fee = getPaymentGatewayHandlingFee('billdesk', $payment->amount);

        if ($calculated_handling_fee != $payment->handling_fee) {
            return array('status' => false, 'reference_number' => $reference_number, 'message' => trans('finance.handling_fee_mistmatch'), 'payment' => $payment);
        }

        $params['date'] = date('Y-m-d');
        $params['amount'] = $payment->total - $payment->handling_fee;
        $params['handling_fee'] = $payment->handling_fee;
        $params['is_online_payment'] = 1;
        $params['gateway'] = 'billdesk';
        $params['source'] = 'Billdesk';
        $params['gateway_token'] = strtoupper(randomString(20));
        $params['reference_number'] = $reference_number;
        $params['installment_id'] = $payment->installment_id;
        $params['installments'] = $payment->getOption('installments');
        $params['is_student_or_parent'] = true;

        $this->student_record_repo->makePayment($student_record, $params);

        $payment->status = 1;
        $payment->save();

        return array('status' => true, 'payment' => $payment, 'reference_number' => $reference_number);
    }
}