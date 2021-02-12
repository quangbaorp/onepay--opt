<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function reg()
    {
        # code...
        return view('pages.register');
    }
    public function index()
    {
        return view('pages.onepay');
    }
    public function onepay(Request $request)
    {
        $data= [];
        $md5HashData = "";
        $SECURE_SECRET = env('SECRET_ONEPAY');
        $vpcURL = env('ONEPAYURL');
        $data['vpc_Version'] = '2'; 
        $data['vpc_ReturnURL'] = route('responeOnepay');
        $data['vpc_TicketNo'] = $_SERVER ['REMOTE_ADDR'];
        $data['vpc_Amount'] = '10000000';
        $data['vpc_command'] = 'pay';
        $data['vpc_MerchTxnRef'] = rand();
        $data['vpc_OrderInfo']= "BoolRP";
        $data['vpc_Locale'] = 'en';
        $data['vpc_Merchant'] = 'TESTONEPAY';
        $data['vpc_AccessCode'] = '6BEB2546';
        $data['vpc_Currency'] = 'vnd';
        $data['title'] = 'Mua Bool';
        $appendAmp = 0;
        ksort ($data);
        foreach($data as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
        }
        $md5HashData = rtrim($md5HashData, "&");
        if (strlen($SECURE_SECRET) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
        }
        return Response::make( '', 302 )->header( 'Location', $vpcURL );
    }
    
    public function null2unknown($data)
    {
        if ($data == "") {
            return "No Value Returned";
        } else {
            return $data;
        }
    }
    public function responeOnepay(Request $request)
    {
        $SECURE_SECRET = "6D0870CDE5F24F34F3915FB0045120DB";
        $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
        $vpc_MerchTxnRef = $_GET["vpc_MerchTxnRef"];
        unset($_GET["vpc_SecureHash"]);
        $errorExists = false;
        if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {
            ksort($_GET);
            $md5HashData = "";
            foreach ($_GET as $key => $value) {

                if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
            $md5HashData = rtrim($md5HashData, "&");
            if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)))) {
                
                $hashValidated = "CORRECT";
            } else {
                
                $hashValidated = "INVALID HASH";
            }
        } else {
            $hashValidated = "INVALID HASH";
        }
        $amount = $this->null2unknown($_GET["vpc_Amount"]);
        $command = $this->null2unknown($_GET["vpc_Command"]);
        $message = $this->null2unknown($_GET["vpc_Message"]);
        $version = $this->null2unknown($_GET["vpc_Version"]);
        $cardType = $this->null2unknown($_GET["vpc_Card"]);
        $orderInfo = $this->null2unknown($_GET["vpc_OrderInfo"]);
        $merchantID = $this->null2unknown($_GET["vpc_Merchant"]);
        $merchTxnRef = $this->null2unknown($_GET["vpc_MerchTxnRef"]);
        $transactionNo = $this->null2unknown($_GET["vpc_TransactionNo"]);
        $txnResponseCode = $this->null2unknown($_GET["vpc_TxnResponseCode"]);
        $verType = array_key_exists("vpc_VerType", $_GET) ? $_GET["vpc_VerType"] : "No Value Returned";
        $verStatus = array_key_exists("vpc_VerStatus", $_GET) ? $_GET["vpc_VerStatus"] : "No Value Returned";
        $token = array_key_exists("vpc_VerToken", $_GET) ? $_GET["vpc_VerToken"] : "No Value Returned";
        $verSecurLevel = array_key_exists("vpc_VerSecurityLevel", $_GET) ? $_GET["vpc_VerSecurityLevel"] : "No Value Returned";
        $enrolled = array_key_exists("vpc_3DSenrolled", $_GET) ? $_GET["vpc_3DSenrolled"] : "No Value Returned";
        $xid = array_key_exists("vpc_3DSXID", $_GET) ? $_GET["vpc_3DSXID"] : "No Value Returned";
        $acqECI = array_key_exists("vpc_3DSECI", $_GET) ? $_GET["vpc_3DSECI"] : "No Value Returned";
        $authStatus = array_key_exists("vpc_3DSstatus", $_GET) ? $_GET["vpc_3DSstatus"] : "No Value Returned";
        $errorTxt = "";
        if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
            $errorTxt = "Error ";
        }
        $transStatus = "";
        if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
            $errorTxt = "Error ";
        }
        $transStatus = "";
        if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
            $transStatus = "Giao dịch thành công";
        }elseif ($hashValidated=="INVALID HASH" && $txnResponseCode=="0"){
            $transStatus = "Giao dịch chờ xử lý";
        }elseif($txnResponseCode=="1"){
            $transStatus = "Giao dịch không thành công. Ngân hàng phát
            hành thẻ từ chối cấp phép cho giao dịch. Vui
            lòng liên hệ ngân hàng theo số điện thoại sau
            mặt thẻ để biết chính xác nguyên nhân Ngân
            hàng từ chối";
        }elseif($txnResponseCode=="3"){
            $transStatus = "The transaction is unsuccessful.
            A technical error has occurred during the
            transaction.
            Please contact OnePAY for details
            (Hotline 1900 633 927)
            ";
        }
        elseif($txnResponseCode=="4"){
            $transStatus = "Giao dịch không thành công, có lỗi trong quá
            trình cài đặt cổng thanh toán. Vui lòng liên hệ
            với OnePAY để được hỗ trợ (Hotline 1900 633
            927)
            ";
        }
        elseif($txnResponseCode=="5"){
            $transStatus = "Giao dịch không thành công, số tiền không hợp
            lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ
            (Hotline 1900 633 927)            
            ";
        }
        elseif($txnResponseCode=="6"){
            $transStatus = "Giao dịch không thành công, loại tiền tệ không
            hợp lệ. Vui lòng liên hệ với OnePAY để được
            hỗ trợ (Hotline 1900 633 927)            
            ";
        }
        elseif($txnResponseCode=="7"){
            $transStatus = "Giao dịch không thành công. Ngân hàng phát
            hành thẻ từ chối cấp phép cho giao dịch. Vui
            lòng liên hệ ngân hàng theo số điện thoại sau
            mặt thẻ để biết chính xác nguyên nhân Ngân
            hàng từ chối           
            ";
        }
        elseif($txnResponseCode=="8"){
            $transStatus = "Giao dịch không thành công. Số thẻ không
            đúng. Vui lòng kiểm tra và thực hiện thanh toán
            lại                    
            ";
        }
        elseif($txnResponseCode=="9"){
            $transStatus = "Giao dịch không thành công. Tên chủ thẻ
            không đúng. Vui lòng kiểm tra và thực hiện
            thanh toán lại               
            ";
        }
        else {
            $transStatus = "Giao dịch thất bại";
        }
        return view('pages.respone' , compact(['transStatus', 'amount']));
    }
}
