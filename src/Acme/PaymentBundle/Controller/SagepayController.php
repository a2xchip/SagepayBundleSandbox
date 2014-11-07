<?php
namespace Acme\PaymentBundle\Controller;

use Acme\PaymentBundle\Model\PaymentDetails;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\SensitiveValue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Range;

class SagepayController extends Controller
{
    public function prepareOnsiteAction(Request $request)
    {
        $paymentName = 'sagepay_onsite';

        $form = $this->createPurchaseForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            $storage = $this->getPayum()->getStorage('Acme\PaymentBundle\Model\PaymentDetails');



            /** @var PaymentDetails */
            $paymentDetails = $storage->createModel();

            $storage->updateModel($paymentDetails);
            $paymentDetails['VendorTxCode'] = $data['VendorTxCode'];
            $paymentDetails['Amount'] = $data['Amount'];
            $paymentDetails['Currency'] = $data['Currency'];
            $paymentDetails['Description'] = $data['Description'];
            $paymentDetails['BillingSurname'] = $data['BillingSurname'];
            $paymentDetails['BillingFirstnames'] = $data['BillingFirstnames'];
            $paymentDetails['BillingAddress1'] = $data['BillingAddress1'];
            $paymentDetails['BillingCity'] = $data['BillingCity'];
            $paymentDetails['BillingPostCode'] = $data['BillingPostCode'];
            $paymentDetails['BillingCountry'] = $data['BillingCountry'];
            $paymentDetails['DeliverySurname'] = $data['DeliverySurname'];
            $paymentDetails['DeliveryFirstnames'] = $data['DeliveryFirstnames'];
            $paymentDetails['DeliveryAddress1'] = $data['DeliveryAddress1'];
            $paymentDetails['DeliveryCity'] = $data['DeliveryCity'];
            $paymentDetails['DeliveryPostCode'] = $data['DeliveryPostCode'];
            $paymentDetails['DeliveryCountry'] = $data['DeliveryCountry'];
            
            $storage->updateModel($paymentDetails);

            $captureToken = $this->getTokenFactory()->createCaptureToken(
                $paymentName,
                $paymentDetails,
                'acme_payment_details_view'
            );
            $request->getSession()->set('payum_token', $captureToken->getHash());

            return $this->forward(
                'PayumBundle:Capture:do',
                array(
                    'payum_token' => $captureToken,
                )
            );
        }

        return $this->render(
            'AcmePaymentBundle:Sagepay:prepare.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createPurchaseForm()
    {
        return $this->createFormBuilder()
            ->add(
                'VendorTxCode',
                null,
                array(
                    'data' => uniqid(),
                )
            )
            ->add(
                'Amount',
                null,
                array(
                    'data' => 2.3,
                )
            )
            ->add(
                'Currency',
                null,
                array(
                    'data' => 'GBP',
                )
            )
            ->add(
                'Description',
                null,
                array(
                    'data' => 'Sagepay payment',
                )
            )
            ->add(
                'BillingSurname',
                null,
                array(
                    'data' => 'Jeliuc',
                )
            )
            ->add(
                'BillingFirstnames',
                null,
                array(
                    'data' => 'Alexandr',
                )
            )
            ->add(
                'BillingAddress1',
                null,
                array(
                    'data' => 'Stefan cel Mare st.',
                )
            )
            ->add(
                'BillingCity',
                null,
                array(
                    'data' => 'Cahul',
                )
            )
            ->add(
                'BillingPostCode',
                null,
                array(
                    'data' => '3900',
                )
            )
            ->add(
                'BillingCountry',
                null,
                array(
                    'data' => 'MD',
                )
            )
            ->add(
                'DeliverySurname',
                null,
                array(
                    'data' => 'Jeliuc',
                )
            )
            ->add(
                'DeliveryFirstnames',
                null,
                array(
                    'data' => 'Alexandr',
                )
            )
            ->add(
                'DeliveryAddress1',
                null,
                array(
                    'data' => 'Stefanc cel Mare st.',
                )
            )
            ->add(
                'DeliveryCity',
                null,
                array(
                    'data' => 'Cahul',
                )
            )
            ->add(
                'DeliveryPostCode',
                null,
                array(
                    'data' => '3900',
                )
            )
            ->add(
                'DeliveryCountry',
                null,
                array(
                    'data' => 'MD',
                )
            )
            ->getForm();

            
            
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }
}
