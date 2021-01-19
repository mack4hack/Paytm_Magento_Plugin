<?php
    namespace One97\Paytm\Controller\Standard;
    use Magento\Framework\App\CsrfAwareActionInterface;
    use Magento\Framework\App\Request\InvalidRequestException;
    use Magento\Framework\App\RequestInterface;

    class Redirect extends \One97\Paytm\Controller\Paytm  implements CsrfAwareActionInterface{

        public function createCsrfValidationException(
            RequestInterface $request
        ): ?InvalidRequestException {
            return null;
        }

        public function validateForCsrf(RequestInterface $request): ?bool {
            return true;
        }
        
        /* this funtion redirect to Paytm with proper form post */
        public function execute() {
           
            $order = $this->getOrder();
            if ($order->getBillingAddress()) {
                $order->setState("pending_payment")->setStatus("pending_payment");
                $order->addStatusToHistory($order->getStatus(), "Customer was redirected to paytm.");
                $order->save();
                $dataRaw=$this->_paytmModel->buildPaytmRequest($order);
              //  $data['actionURL']=$this->_paytmModel->getRedirectUrl();
               // $resultPage = $this->resultPageFactory->create();
               // $resultPage->getLayout()->initMessages();
               // $resultPage->getLayout()->getBlock('paytm_standard_redirect')->setName($data);
                echo json_encode(array('response'=>$dataRaw));
            } else {
                $this->_cancelPayment();
                $this->_paytmSession->restoreQuote();
                $this->getResponse()->setRedirect(
                    $this->getPaytmHelper()->getUrl('checkout')
                );
            }
        }
    }
?>