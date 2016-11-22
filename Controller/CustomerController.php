<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Reporting\Customer;
use AppBundle\Repository\Reporting\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends BaseController
{
    /**
     * CustomerController constructor.
     */
    public function __construct(){
        $this->customerRepository = new CustomerRepository();
    }
    /**
     * @Route("/customer/list", name="customerlist")
     */
    public function listAction()
    {
        $customers = $this->customerRepository->findAllCustomers();
        return $this->render('customer/list.html.twig', array('customers' => $customers));
    }

    /**
     * @Route("/customer/edit", name="customeredit")
     * @return Response
     */
    public function editAction(Request $request){
        $customerObject = $this->customerRepository->findByCustomerID($request->query->get('customer'));
        return $this->render('customer/edit.html.twig', array('customer' => $customerObject));
    }

    /**
     * @Route("/customer/new",name="customernew")
     * @return Response
     */
    public function newAction(){
        $customer = new Customer();
        return $this->render('customer/new.html.twig', array('customer' => $customer));
    }

    /**
     * @Route("/customer/create",name="customercreate")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request){
        $customer = new Customer();
        $customer->setCustomerID(uniqid(sha1($request->get('customerName') . rand(0,1000))));
        $customer->setCustomerName(addslashes($request->get('customerName')));
        $customer->setExternalCustomerCode(addslashes($request->get('externalCustomerCode')));
        $customer->setExternalCustomerName(addslashes($request->get('externalCustomerName')));
        $this->customerRepository->create($customer);
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('customerlist');
    }

    /**
     * @Route("/customer/update", name="customerupdate")
     */
    public function updateAction(Request $request){
        $customerID = $request->get('customerID');
        $customer = $this->customerRepository->findByCustomerID($customerID);
        $customer->setCustomerName(addslashes($request->get('customerName')));
        $customer->setExternalCustomerCode(addslashes($request->get('externalCustomerCode')));
        $customer->setExternalCustomerName(addslashes($request->get('externalCustomerName')));
        $this->customerRepository->update($customer);
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('customerlist');
    }
}